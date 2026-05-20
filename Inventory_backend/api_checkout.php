<?php

header('Content-Type: application/json');

require '../Database/config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['cart'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart data missing'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    foreach ($data['cart'] as $item) {

        $id = intval($item['id']);
        $qty = intval($item['qty']);

        // Check current stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found");
        }

        if ($product['stock'] < $qty) {
            throw new Exception("Not enough stock");
        }

        // Deduct stock
        $update = $pdo->prepare("
            UPDATE products
            SET stock = stock - ?
            WHERE id = ?
        ");

        $update->execute([$qty, $id]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true
    ]);

} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>