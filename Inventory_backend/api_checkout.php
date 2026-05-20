<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

// 1. GATEKEEPER: Prevent unauthenticated ledger submissions
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart']) || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart data is missing or empty']);
    exit;
}

$paymentMethod  = $data['payment_method'] ?? 'Cash';
$discountAmount = floatval($data['discount_amount'] ?? 0);
$totalAmount    = floatval($data['total_amount'] ?? 0);
$cashReceived   = floatval($data['cash_received'] ?? 0);
$changeAmount   = floatval($data['change_amount'] ?? 0);

try {
    $pdo->beginTransaction();

    $countStmt = $pdo->query('SELECT COUNT(*) FROM orders');
    $nextNumber = $countStmt->fetchColumn() + 1;
    $orderNo = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    $orderSql = 'INSERT INTO orders (order_no, total_amount, discount_amount, payment_method, cash_received, change_amount) VALUES (?, ?, ?, ?, ?, ?)';
    $orderStmt = $pdo->prepare($orderSql);
    $orderStmt->execute([$orderNo, $totalAmount, $discountAmount, $paymentMethod, $cashReceived, $changeAmount]);

    $orderId = $pdo->lastInsertId();
    $itemSql = 'INSERT INTO order_items (order_id, product_id, quantity, price_at_sale) VALUES (?, ?, ?, ?)';
    $itemStmt = $pdo->prepare($itemSql);

    foreach ($data['cart'] as $item) {
        $id    = intval($item['id']);
        $qty   = intval($item['qty']);
        $price = floatval($item['price']);

        $stmt = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product ID " . $id . " could not be found.");
        }

        if ($product['stock'] < $qty) {
            throw new Exception("Insufficient stock parameters tracking for item: " . $product['name']);
        }

        $batchStmt = $pdo->prepare("
            SELECT id, quantity_remaining 
            FROM product_batches 
            WHERE product_id = ? AND quantity_remaining > 0
            ORDER BY created_at ASC
        ");
        $batchStmt->execute([$id]);
        $batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);

        $remainingToDeduct = $qty;

        foreach ($batches as $batch) {
            if ($remainingToDeduct <= 0) break;

            $batchId = $batch['id'];
            $currentBatchStock = $batch['quantity_remaining'];

            if ($currentBatchStock >= $remainingToDeduct) {
                $newBatchStock = $currentBatchStock - $remainingToDeduct;
                $updateBatch = $pdo->prepare("UPDATE product_batches SET quantity_remaining = ? WHERE id = ?");
                $updateBatch->execute([$newBatchStock, $batchId]);
                $remainingToDeduct = 0;
            } else {
                $remainingToDeduct -= $currentBatchStock;
                $updateBatch = $pdo->prepare("UPDATE product_batches SET quantity_remaining = 0 WHERE id = ?");
                $updateBatch->execute([$batchId]);
            }
        }

        if ($remainingToDeduct > 0) {
            throw new Exception("FIFO Error: Batches for '" . $product['name'] . "' are desynced with total stock.");
        }

        $updateProductsTable = $pdo->prepare("
            UPDATE products 
            SET stock = (SELECT COALESCE(SUM(quantity_remaining), 0) FROM product_batches WHERE product_id = ?) 
            WHERE id = ?
        ");
        $updateProductsTable->execute([$id, $id]);
        $itemStmt->execute([$orderId, $id, $qty, $price]);
    }

    $pdo->commit();
    echo json_encode([
        'success'  => true,
        'order_no' => $orderNo,
        'message'  => 'Transaction completed and logged successfully.'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
