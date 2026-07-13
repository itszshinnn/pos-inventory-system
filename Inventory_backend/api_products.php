<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/Database.php';

$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required.']);
    exit;
}

$username = $_SESSION['username'] ?? 'Admin';

$method = $_SERVER['REQUEST_METHOD'];

if (in_array($method, ['POST', 'PUT', 'DELETE']) && ($_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Administrative privileges required.']);
    exit;
}

switch ($method) {
    case 'GET':
        require_once 'InventoryManager.php';
        $manager = new InventoryManager($pdo);
        $products = $manager->getAllProducts();
        echo json_encode($products);
        break;

    case 'POST':
        $name         = trim($_POST['name'] ?? '');
        $category_id  = intval($_POST['category_id'] ?? 0);
        $price_bought = floatval($_POST['price_bought'] ?? 0);
        $price        = floatval($_POST['price'] ?? 0);
        $stock        = intval($_POST['stock'] ?? 0);
        $description  = trim($_POST['description'] ?? '');
        $brand        = trim($_POST['brand'] ?? '');
        $color        = trim($_POST['color'] ?? '');
        $type         = trim($_POST['type'] ?? '');
        $size         = trim($_POST['capacity_size'] ?? '');
        $resolution   = trim($_POST['resolution'] ?? '');

        $imageName   = 'default_product.png';
        $modelPath   = null;

        if (!$name || !$category_id || $price < 0 || $price_bought < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All core fields are required and must be valid']);
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($fileTmpPath);
            $allowedMimeTypes = ['image/jpeg' => 'jpg', 'image/png'  => 'png', 'image/webp' => 'webp', 'image/gif'  => 'gif'];

            if (array_key_exists($mimeType, $allowedMimeTypes)) {
                $fileExtension = $allowedMimeTypes[$mimeType];
                $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                $dest_path = '../Images/' . $imageName;
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = 'default_product.png';
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid image format.']);
                break;
            }
        }

        if (isset($_FILES['model_file']) && $_FILES['model_file']['error'] === UPLOAD_ERR_OK) {
            $modelTmpPath = $_FILES['model_file']['tmp_name'];
            $modelNameOrig = $_FILES['model_file']['name'];
            $modelExt = strtolower(pathinfo($modelNameOrig, PATHINFO_EXTENSION));

            if (in_array($modelExt, ['glb', 'gltf'])) {
                if (!is_dir('../Models')) mkdir('../Models', 0777, true);

                $newModelName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $modelExt;
                $modelDestPath = '../Models/' . $newModelName;
                if (move_uploaded_file($modelTmpPath, $modelDestPath)) {
                    $modelPath = '../Models/' . $newModelName;
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid model format. Only GLB or GLTF allowed.']);
                break;
            }
        }

        require_once 'InventoryManager.php';
        require_once 'Product.php';
        $manager = new InventoryManager($pdo);

        $newProduct = new Product($name, $category_id, $price_bought, $price, $stock);

        $result = $manager->addProduct($newProduct, $imageName, $modelPath, $description, $brand, $color, $type, $size, $resolution, $username);

        if (isset($result['error'])) http_response_code(500);
        echo json_encode($result);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id            = intval($input['id'] ?? 0);
        $name          = trim($input['name'] ?? '');
        $category_id   = intval($input['category_id'] ?? 0);
        $price_bought         = floatval($input['price_bought'] ?? 0);
        $price         = floatval($input['price'] ?? 0);
        $stock         = intval($input['stock'] ?? 0);
        $description   = trim($input['description'] ?? '');
        $brand         = trim($input['brand'] ?? '');
        $color         = trim($input['color'] ?? '');
        $type          = trim($input['type'] ?? '');
        $capacity_size = trim($input['capacity_size'] ?? '');
        $resolution    = trim($input['resolution'] ?? '');

        if (!$id || !$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All core fields are required']);
            break;
        }

        try {
            $pdo->beginTransaction();

            $getOld = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $getOld->execute([$id]);
            $oldStock = intval($getOld->fetchColumn());

            if ($stock !== $oldStock) {
                if ($stock > $oldStock) {
                    $addedQty = $stock - $oldStock;
                    $addBatch = $pdo->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost) VALUES (?, ?, ?, ?)");
                    $addBatch->execute([$id, $addedQty, $addedQty, $price]);
                } else {
                    $deductQty = $oldStock - $stock;
                    $batchStmt = $pdo->prepare("SELECT id, quantity_remaining FROM product_batches WHERE product_id = ? AND quantity_remaining > 0 ORDER BY created_at ASC");
                    $batchStmt->execute([$id]);
                    $batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($batches as $batch) {
                        if ($deductQty <= 0) break;
                        if ($batch['quantity_remaining'] >= $deductQty) {
                            $updateB = $pdo->prepare("UPDATE product_batches SET quantity_remaining = quantity_remaining - ? WHERE id = ?");
                            $updateB->execute([$deductQty, $batch['id']]);
                            $deductQty = 0;
                        } else {
                            $deductQty -= $batch['quantity_remaining'];
                            $updateB = $pdo->prepare("UPDATE product_batches SET quantity_remaining = 0 WHERE id = ?");
                            $updateB->execute([$batch['id']]);
                        }
                    }
                }
            }

            $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price_bought = ?, price = ?, stock = ?, description = ?, brand = ?, color = ?, type = ?, capacity_size = ?, resolution = ? WHERE id = ?");
            $stmt->execute([$name, $category_id, $price_bought, $price, $stock, $description, $brand, $color, $type, $capacity_size, $resolution, $id]);

            $log = $pdo->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([$id, $name, 'Edited', $oldStock, $stock, $username]);

            $pdo->commit();
            require_once __DIR__ . '/../MailService.php';
            $threshold = 3;
            if ($stock <= $threshold) {
                MailService::sendLowStockAlert($name, $stock);
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Database update interruption error.']);
        }
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }

        require_once 'InventoryManager.php';
        $manager = new InventoryManager($pdo);

        $result = $manager->deleteProduct($id, $username);

        if (isset($result['error'])) {
            http_response_code($result['code']);
        }
        echo json_encode($result);
        break;
}
