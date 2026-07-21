<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../Database/Database.php';

$config = require __DIR__ . '/../config_local.php';

$apiKey = $config['GROQ_API_KEY'] ?? null;

if (!$apiKey) {
    echo json_encode(['error' => 'API Key not configured properly.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userQuestion = trim($input['question'] ?? '');

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

function callGroqAPI($prompt, $apiKey)
{
    $url = "https://api.groq.com/openai/v1/chat/completions";

    $payload = json_encode([
        "model" => "llama-3.3-70b-versatile",
        "messages" => [
            ["role" => "user", "content" => $prompt]
        ],
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

$intentPrompt = "
Analyze this user input: '$userQuestion'
Classify it into ONE of two categories:
1. 'DATABASE' - If the question asks for POS system data, inventory, sales, users, logs, or stock metrics.
2. 'CASUAL' - If it is general conversation, math, or small talk.

Respond with ONLY 'DATABASE' or 'CASUAL'. Do not add punctuation.
";

$intent = strtoupper(callGroqAPI($intentPrompt, $apiKey));

if (strpos($intent, 'API_ERROR') !== false) {
    echo json_encode(['success' => false, 'error' => $intent]);
    exit;
}

// ROUTE 1: Casual Assistant Response
if (strpos($intent, 'CASUAL') !== false) {
    $casualPrompt = "
You are a friendly AI Assistant embedded in a POS & Inventory Management System.
User says: '$userQuestion'

Instructions:
- Provide a concise, polite answer.
- Keep currency references in Philippine Pesos (₱) if money is mentioned.
";
    $response = callGroqAPI($casualPrompt, $apiKey);
    echo json_encode(['success' => true, 'answer' => $response]);
    exit;
}

$schemaContext = "
You are a SQL query generator for a PHP POS and Inventory System.
Convert natural language questions into valid MySQL SELECT queries.

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

$sqlQuery = callGroqAPI($schemaContext . "\nUser Question: " . $userQuestion, $apiKey);
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
    $summaryPrompt = "
You are a helpful POS AI Assistant.
User Question: '$userQuestion'
Database SQL Result: $dataContext

Instructions:
- Provide a clear, natural summary based strictly on the database result.
- ALWAYS use Philippine Pesos (₱) for currency values.
- If the result array is empty [], inform the user politely that no matching records were found.
";

    $naturalAnswer = callGroqAPI($summaryPrompt, $apiKey);

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
