<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // GET all products (joined with category name)
    case 'GET':
        $sql = 'SELECT p.id, p.name, p.price, p.stock, p.category_id, p.image,
                       c.name AS category
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY p.id ASC';
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll());
        break;

    // POST — add product with Multipart Form image uploading
    case 'POST':

        session_start();

        $name        = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price       = floatval($_POST['price'] ?? 0);
        $stock       = intval($_POST['stock'] ?? 0);

        $imageName = 'default_product.png';

        if (!$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required and must be valid']);
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName    = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $imageName = time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = '../Images/';
                $dest_path = $uploadFileDir . $imageName;

                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = 'default_product.png';
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid image format']);
                break;
            }
        }

        try {
            $pdo->beginTransaction();

            // INSERT PRODUCT
            $stmt = $pdo->prepare("
                INSERT INTO products (name, category_id, price, stock, image)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $category_id, $price, $stock, $imageName]);
            $productId = $pdo->lastInsertId();

            // --- FIFO IMPLEMENTATION: Create initial stock tracking batch ---
            if ($stock > 0) {
                $initialBatch = $pdo->prepare("
                    INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost)
                    VALUES (?, ?, ?, ?)
                ");
                // Using price as unit_cost fallback
                $initialBatch->execute([$productId, $stock, $stock, $price]);
            }

            // INSERT LOG
            $log = $pdo->prepare("
                INSERT INTO product_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $log->execute([
                $productId,
                $name,
                'Added',
                null,
                $stock,
                $_SESSION['username'] ?? 'Unknown User'
            ]);

            $pdo->commit();
            echo json_encode(['success' => true, 'id' => $productId]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product batch structure: ' . $e->getMessage()]);
        }
        break;

    // PUT — edit product
    case 'PUT':

        session_start();

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

            // GET OLD DATA
            $getOld = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
            $getOld->execute([$id]);
            $oldProduct = $getOld->fetch(PDO::FETCH_ASSOC);

            if (!$oldProduct) {
                http_response_code(404);
                echo json_encode(['error' => 'Product not found']);
                $pdo->rollBack();
                break;
            }

            // --- FIFO IMPLEMENTATION: Handle manual updates to stock counts ---
            if ($stock !== intval($oldProduct['stock'])) {
                if ($stock > $oldProduct['stock']) {
                    // Stock increased: create a new auxiliary restock batch entry
                    $addedQty = $stock - $oldProduct['stock'];
                    $addBatch = $pdo->prepare("
                        INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost)
                        VALUES (?, ?, ?, ?)
                    ");
                    $addBatch->execute([$id, $addedQty, $addedQty, $price]);
                } else {
                    // Stock decreased manually: drain oldest remaining batches chronologically
                    $deductQty = $oldProduct['stock'] - $stock;

                    $batchStmt = $pdo->prepare("
                        SELECT id, quantity_remaining FROM product_batches 
                        WHERE product_id = ? AND quantity_remaining > 0 
                        ORDER BY created_at ASC
                    ");
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

            // UPDATE PRODUCT
            $stmt = $pdo->prepare("
                UPDATE products
                SET name = ?, category_id = ?, price = ?, stock = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $category_id, $price, $stock, $id]);

            // INSERT LOG
            $log = $pdo->prepare("
                INSERT INTO product_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $log->execute([
                $id,
                $name,
                'Edited',
                $oldProduct['stock'],
                $stock,
                $_SESSION['username'] ?? 'Unknown User'
            ]);

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Database error on edit execution: ' . $e->getMessage()]);
        }
        break;

    // DELETE — remove product
    case 'DELETE':

        session_start();

        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }

        // 1. GET PRODUCT DATA FIRST 
        $getProduct = $pdo->prepare("SELECT name, stock, image FROM products WHERE id = ?");
        $getProduct->execute([$id]);
        $product = $getProduct->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
            break;
        }

        // 2. PHYSICAL FILE CLEANUP
        if (!empty($product['image']) && $product['image'] !== 'default_product.png') {
            $filePath = '../Images/' . $product['image'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        try {
            $pdo->beginTransaction();

            // 1. Log the deletion event before references vanish
            $log = $pdo->prepare("
                INSERT INTO product_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $log->execute([
                null,
                $product['name'],
                'Deleted',
                $product['stock'],
                null,
                $_SESSION['username'] ?? 'Unknown User'
            ]);

            // 🔥 FIX A: Clear out any item purchase traces linked to sales histories
            $deleteOrderItems = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
            $deleteOrderItems->execute([$id]);

            // 🔥 FIX B: Clean up your custom FIFO tracking batches
            $deleteBatches = $pdo->prepare("DELETE FROM product_batches WHERE product_id = ?");
            $deleteBatches->execute([$id]);

            // 4. Finally delete product row from active catalog securely
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete record securely.']);
        }
        break;
}
