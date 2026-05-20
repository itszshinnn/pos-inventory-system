<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

// 1. GLOBAL GATEKEEPER
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// 2. AUTHORIZATION CHECK FOR WRITING RIGHTS
if (in_array($method, ['POST', 'PUT', 'DELETE']) && ($_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Administrative privileges required.']);
    exit;
}

switch ($method) {
    case 'GET':
        $sql = 'SELECT p.id, p.name, p.price, p.stock, p.category_id, p.image,
                       c.name AS category
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY p.id ASC';
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $name        = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price       = floatval($_POST['price'] ?? 0);
        $stock       = intval($_POST['stock'] ?? 0);
        $imageName   = 'default_product.png';

        if (!$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required and must be valid']);
            break;
        }

        // SECURE IMAGE UPLOAD ARCHITECTURE
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];

            // Evaluate file properties via binary headers, not string file names
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($fileTmpPath);

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif'
            ];

            if (array_key_exists($mimeType, $allowedMimeTypes)) {
                $fileExtension = $allowedMimeTypes[$mimeType];
                // Completely rename the file to clean out special character traversal tricks
                $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                $dest_path = '../Images/' . $imageName;

                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = 'default_product.png';
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid image format. Only JPG, PNG, WEBP, and GIF allowed.']);
                break;
            }
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category_id, $price, $stock, $imageName]);
            $productId = $pdo->lastInsertId();

            if ($stock > 0) {
                $initialBatch = $pdo->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost) VALUES (?, ?, ?, ?)");
                $initialBatch->execute([$productId, $stock, $stock, $price]);
            }

            $log = $pdo->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([$productId, $name, 'Added', null, $stock, $_SESSION['username']]);

            $pdo->commit();
            echo json_encode(['success' => true, 'id' => $productId]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Server error saving product setup structure.']);
        }
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $id          = intval($input['id'] ?? 0);
        $name        = trim($input['name'] ?? '');
        $category_id = intval($input['category_id'] ?? 0);
        $price       = floatval($input['price'] ?? 0);
        $stock       = intval($input['stock'] ?? 0);

        if (!$id || !$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required']);
            break;
        }

        try {
            $pdo->beginTransaction();

            $getOld = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
            $getOld->execute([$id]);
            $oldProduct = $getOld->fetch(PDO::FETCH_ASSOC);

            if (!$oldProduct) {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
                $pdo->rollBack();
                break;
            }

            if ($stock !== intval($oldProduct['stock'])) {
                if ($stock > $oldProduct['stock']) {
                    $addedQty = $stock - $oldProduct['stock'];
                    $addBatch = $pdo->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost) VALUES (?, ?, ?, ?)");
                    $addBatch->execute([$id, $addedQty, $addedQty, $price]);
                } else {
                    $deductQty = $oldProduct['stock'] - $stock;
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

            $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->execute([$name, $category_id, $price, $stock, $id]);

            $log = $pdo->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([$id, $name, 'Edited', $oldProduct['stock'], $stock, $_SESSION['username']]);

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
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

        $getProduct = $pdo->prepare("SELECT name, stock, image FROM products WHERE id = ?");
        $getProduct->execute([$id]);
        $product = $getProduct->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            break;
        }

        if (!empty($product['image']) && $product['image'] !== 'default_product.png') {
            $filePath = '../Images/' . $product['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        try {
            $pdo->beginTransaction();

            $log = $pdo->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, ?, ?, ?, ?)");
            $log->execute([null, $product['name'], 'Deleted', $product['stock'], null, $_SESSION['username']]);

            $deleteOrderItems = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
            $deleteOrderItems->execute([$id]);

            $deleteBatches = $pdo->prepare("DELETE FROM product_batches WHERE product_id = ?");
            $deleteBatches->execute([$id]);

            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Secure deletion process failed.']);
        }
        break;
}
