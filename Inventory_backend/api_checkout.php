<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

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

require_once 'TransactionManager.php';

$transactionManager = new TransactionManager($pdo);

$result = $transactionManager->processCheckout(
    $data['cart'],
    $paymentMethod,
    $discountAmount,
    $totalAmount,
    $cashReceived,
    $changeAmount
);

if (!$result['success']) {
    http_response_code(500);
}
echo json_encode($result);
?>
