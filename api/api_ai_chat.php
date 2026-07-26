<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../src/Database.php';

$config = require __DIR__ . '/../config_local.php';

$apiKey = $config['GROQ_API_KEY'] ?? null;

if (!$apiKey) {
    echo json_encode(['error' => 'API Key not configured properly.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userQuestion = trim($input['question'] ?? '');
$history = $input['history'] ?? [];

if (empty($userQuestion)) {
    echo json_encode(['success' => false, 'error' => 'Question is required.']);
    exit;
}

$lowerQ = strtolower($userQuestion);
$commonGreetings = ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'who are you', 'help'];
if (in_array($lowerQ, $commonGreetings)) {
    echo json_encode([
        'success' => true,
        'answer' => 'Hello! I am your POS Assistant. You can ask me about inventory stock, product prices, order details, or sales summaries!'
    ]);
    exit;
}

function callGroqAPI($messagesOrPrompt, $apiKey)
{
    $url = "https://api.groq.com/openai/v1/chat/completions";

    $messages = is_array($messagesOrPrompt) ? $messagesOrPrompt : [
        ["role" => "user", "content" => $messagesOrPrompt]
    ];

    $payload = json_encode([
        "model" => "llama-3.3-70b-versatile",
        "messages" => $messages,
        "temperature" => 0.1
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return 'API_ERROR: cURL Error - ' . curl_error($ch);
    }
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['error'])) {
        return 'API_ERROR: ' . ($responseData['error']['message'] ?? 'Unknown Groq Error');
    }

    return trim($responseData['choices'][0]['message']['content'] ?? '');
}

// 1. INTENT CLASSIFICATION WITH CONTEXT HISTORY
$classificationMessages = [];
$classificationMessages[] = [
    "role" => "system",
    "content" => "Analyze the user's current message in the context of the chat history. Classify the user's current message into ONE of three categories:
1. 'ACTION' - If the user is instructing the system to perform a database modification, such as restocking a product (e.g. 'restock x', 'add z stock'), or updating product prices (e.g. 'change price to x', 'actually make it y').
2. 'DATABASE' - If the user is asking for reports, logs, sales, stock levels, or system data (read-only).
3. 'CASUAL' - If it is general conversation, math, greetings, or small talk.

Respond with ONLY 'ACTION', 'DATABASE', or 'CASUAL'. Do not add punctuation or any explanation."
];

foreach ($history as $msg) {
    $classificationMessages[] = [
        "role" => $msg['role'] === 'assistant' ? 'assistant' : 'user',
        "content" => $msg['content']
    ];
}

$classificationMessages[] = [
    "role" => "user",
    "content" => "User's current message: '$userQuestion'"
];

$intent = strtoupper(callGroqAPI($classificationMessages, $apiKey));

if (strpos($intent, 'API_ERROR') !== false) {
    echo json_encode(['success' => false, 'error' => $intent]);
    exit;
}

// ROUTE 1: Action Handler (supports bulk changes and context resolution)
if (strpos($intent, 'ACTION') !== false) {
    $actionMessages = [];
    $actionMessages[] = [
        "role" => "system",
        "content" => "You are a parser that converts the user's current request into a strict JSON payload, taking conversation history into account to resolve missing details (like product name, product ID, or action type) from previous turns. Output ONLY valid JSON containing an array of actions. Do not use code blocks or backticks.

JSON Schema:
{
  \"actions\": [
    {
      \"action\": \"RESTOCK\" | \"UPDATE_PRICE\" | \"UPDATE_BOUGHT_PRICE\" | \"UNKNOWN\",
      \"product_name\": \"string (name of the product, or blank if unknown)\",
      \"product_id\": \"number or null\",
      \"quantity\": \"number or null (quantity to restock)\",
      \"price\": \"number or null (new retail price or bought price)\"
    }
  ]
}"
    ];

    foreach ($history as $msg) {
        $actionMessages[] = [
            "role" => $msg['role'] === 'assistant' ? 'assistant' : 'user',
            "content" => $msg['content']
        ];
    }

    $actionMessages[] = [
        "role" => "user",
        "content" => "User's current message: '$userQuestion'"
    ];

    $parsedJson = callGroqAPI($actionMessages, $apiKey);
    $parsedJson = trim(str_replace(['```json', '```', '`'], '', $parsedJson));
    $actionData = json_decode($parsedJson, true);

    if (!$actionData || !isset($actionData['actions']) || !is_array($actionData['actions']) || empty($actionData['actions'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Unable to determine the action request. Please specify the product name and action clearly (e.g. "restock JVC Earphones by 50").'
        ]);
        exit;
    }

    try {
        $database = new Database();
        $db = $database->getConnection();
        $db->beginTransaction();

        $confirmations = [];

        foreach ($actionData['actions'] as $act) {
            if (!isset($act['action']) || $act['action'] === 'UNKNOWN') {
                throw new Exception("Unable to understand one of the actions requested.");
            }

            $product = null;
            if (!empty($act['product_id'])) {
                $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
                $stmt->execute([':id' => $act['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if (!$product && !empty($act['product_name'])) {
                $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE :name LIMIT 1");
                $stmt->execute([':name' => '%' . $act['product_name'] . '%']);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$product) {
                throw new Exception('Product "' . ($act['product_name'] ?? '#' . $act['product_id']) . '" not found in database.');
            }

            $oldStock = (int)$product['stock'];
            $productId = (int)$product['id'];
            $productName = $product['name'];

            if ($act['action'] === 'RESTOCK') {
                $qty = (int)($act['quantity'] ?? 0);
                if ($qty <= 0) {
                    throw new Exception("Please specify a valid quantity greater than zero to restock **" . $productName . "**.");
                }
                $newStock = $oldStock + $qty;
                
                $upd = $db->prepare("UPDATE products SET stock = :stock WHERE id = :id");
                $upd->execute([':stock' => $newStock, ':id' => $productId]);

                $batch = $db->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost, created_at) VALUES (:pid, :qty, :qty, :cost, NOW())");
                $batch->execute([
                    ':pid' => $productId,
                    ':qty' => $qty,
                    ':cost' => $product['price_bought']
                ]);

                $log = $db->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by, created_at) VALUES (:pid, :name, 'Restocked', :old, :new, :admin, NOW())");
                $log->execute([
                    ':pid' => $productId,
                    ':name' => $productName,
                    ':old' => $oldStock,
                    ':new' => $newStock,
                    ':admin' => 'AI Assistant'
                ]);

                $confirmations[] = "restocked **" . $productName . "** by " . $qty . " units (new stock: " . $newStock . ")";
            } else if ($act['action'] === 'UPDATE_PRICE') {
                $newPrice = (float)($act['price'] ?? 0);
                if ($newPrice <= 0) {
                    throw new Exception("Please specify a valid price greater than zero for **" . $productName . "**.");
                }

                $upd = $db->prepare("UPDATE products SET price = :price WHERE id = :id");
                $upd->execute([':price' => $newPrice, ':id' => $productId]);

                $log = $db->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by, created_at) VALUES (:pid, :name, 'Price Update', :old, :old, :admin, NOW())");
                $log->execute([
                    ':pid' => $productId,
                    ':name' => $productName . " (Price: ₱" . number_format($product['price'], 2) . " -> ₱" . number_format($newPrice, 2) . ")",
                    ':old' => $oldStock,
                    ':new' => $oldStock,
                    ':admin' => 'AI Assistant'
                ]);

                $confirmations[] = "updated retail price of **" . $productName . "** to **₱" . number_format($newPrice, 2) . "**";
            } else if ($act['action'] === 'UPDATE_BOUGHT_PRICE') {
                $newPriceBought = (float)($act['price'] ?? 0);
                if ($newPriceBought <= 0) {
                    throw new Exception("Please specify a valid bought price greater than zero for **" . $productName . "**.");
                }

                $upd = $db->prepare("UPDATE products SET price_bought = :price_bought WHERE id = :id");
                $upd->execute([':price_bought' => $newPriceBought, ':id' => $productId]);

                $log = $db->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by, created_at) VALUES (:pid, :name, 'Bought Price Update', :old, :old, :admin, NOW())");
                $log->execute([
                    ':pid' => $productId,
                    ':name' => $productName . " (Bought Price: ₱" . number_format($product['price_bought'], 2) . " -> ₱" . number_format($newPriceBought, 2) . ")",
                    ':old' => $oldStock,
                    ':new' => $oldStock,
                    ':admin' => 'AI Assistant'
                ]);

                $confirmations[] = "updated bought price of **" . $productName . "** to **₱" . number_format($newPriceBought, 2) . "**";
            }
        }

        $db->commit();
        
        $finalAnswer = "Successfully " . implode(" and ", $confirmations) . ".";
        echo json_encode([
            'success' => true,
            'answer' => $finalAnswer
        ]);
        exit;

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        echo json_encode([
            'success' => false,
            'error' => 'Action Error: ' . $e->getMessage()
        ]);
        exit;
    }
}

// ROUTE 2: Casual Assistant Response
if (strpos($intent, 'CASUAL') !== false) {
    $casualMessages = [];
    $casualMessages[] = [
        "role" => "system",
        "content" => "You are a friendly AI Assistant embedded in a POS & Inventory Management System. The current date today is: " . date('l, F j, Y') . ". Provide a concise, polite answer. Keep currency references in Philippine Pesos (₱) if money is mentioned."
    ];

    foreach ($history as $msg) {
        $casualMessages[] = [
            "role" => $msg['role'] === 'assistant' ? 'assistant' : 'user',
            "content" => $msg['content']
        ];
    }

    $casualMessages[] = [
        "role" => "user",
        "content" => $userQuestion
    ];

    $response = callGroqAPI($casualMessages, $apiKey);
    echo json_encode(['success' => true, 'answer' => $response]);
    exit;
}

// ROUTE 3: Database Query Handler
$schemaContext = "
You are a SQL query generator for a PHP POS and Inventory System.
Convert natural language questions into valid MySQL SELECT queries.

The current date today is: " . date('Y-m-d') . ".

CRITICAL RULES:
- Return ONLY the raw SQL query.
- DO NOT use markdown code blocks or backticks.
- STRICT SECURITY RULE: ONLY generate SELECT statements.
- COLUMN RULE: ONLY use table and column names explicitly listed in the DATABASE SCHEMA below.

BUSINESS LOGIC & CALCULATIONS:
- Revenue = SUM(total_amount) from 'orders'
- Today's Revenue = SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = CURDATE()
- COGS (Cost of Goods Sold) = SUM(cost_of_goods_sold) from 'orders'
- Profit = SUM(total_amount - cost_of_goods_sold) from 'orders'
- Today's Profit = SELECT SUM(total_amount - cost_of_goods_sold) FROM orders WHERE DATE(created_at) = CURDATE()

DATABASE SCHEMA:
- categories (id, name, created_at)
- products (id, name, category_id, price_bought, price, stock, created_at, image, model_path, description, brand, color, type, capacity_size, resolution)
- orders (id, order_no, user_id, total_amount, discount_amount, payment_method, created_at, cash_received, change_amount, cost_of_goods_sold)
- order_items (id, order_id, product_id, quantity, price_at_sale, cost_of_goods_sold)
- inventory_logs (id, product_id, product_name, action_type, old_stock, new_stock, changed_by, created_at)
- product_batches (id, product_id, quantity_received, quantity_remaining, unit_cost, created_at)
- purchase_orders (id, reference_no, status, payment_method, amount_paid, created_at, received_by)
- po_items (id, po_id, product_id, order_qty, unit_cost)
- system_settings (setting_key, setting_value, updated_at)
- users (id, username, password, created_at, role)
";

$sqlMessages = [];
$sqlMessages[] = [
    "role" => "system",
    "content" => $schemaContext
];

foreach ($history as $msg) {
    $sqlMessages[] = [
        "role" => $msg['role'] === 'assistant' ? 'assistant' : 'user',
        "content" => $msg['content']
    ];
}

$sqlMessages[] = [
    "role" => "user",
    "content" => "User Question: " . $userQuestion
];

$sqlQuery = callGroqAPI($sqlMessages, $apiKey);
$sqlQuery = trim(str_replace(['```sql', '```', '`'], '', $sqlQuery));

if (!preg_match('/^\s*SELECT/i', $sqlQuery)) {
    echo json_encode([
        'success' => false,
        'error' => 'Security Error: Model generated invalid SQL -> ' . $sqlQuery
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $stmt = $db->prepare($sqlQuery);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dataContext = json_encode($results);
    
    $summaryMessages = [];
    $summaryMessages[] = [
        "role" => "system",
        "content" => "You are a helpful POS AI Assistant. The current date today is: " . date('l, F j, Y') . ". Provide a clear, natural summary based strictly on the database SQL Result. ALWAYS use Philippine Pesos (₱) for currency values. If the result array is empty [], inform the user politely that no matching records were found."
    ];

    foreach ($history as $msg) {
        $summaryMessages[] = [
            "role" => $msg['role'] === 'assistant' ? 'assistant' : 'user',
            "content" => $msg['content']
        ];
    }

    $summaryMessages[] = [
        "role" => "user",
        "content" => "User Question: '$userQuestion'\nDatabase SQL Result: $dataContext"
    ];

    $naturalAnswer = callGroqAPI($summaryMessages, $apiKey);

    echo json_encode([
        'success' => true,
        'answer' => $naturalAnswer
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'DB Error: ' . $e->getMessage() . ' | SQL tried: ' . $sqlQuery
    ]);
}
