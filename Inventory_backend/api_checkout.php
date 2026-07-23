<?php
session_start();
header('Content-Type: application/json');
require_once 'PaymentManager.php';

$data = json_decode(file_get_contents('php://input'), true);

$cart = $data['cart'];
$paymentMethod = $data['payment_method'];
$totalAmount = $data['total_amount'];
$discountAmount = $data['discount_amount'] ?? 0;
$discountType = $data['discount_type'] ?? null;
$cashReceived = $data['cash_received'] ?? 0;
$changeAmount = $data['change_amount'] ?? 0;
$userId = $_SESSION['user_id'];
$customerEmail = $data['email'] ?? "";

if ($paymentMethod === 'GCash' || $paymentMethod === 'Maya' || $paymentMethod === 'Card') {

    $paymentManager = new PaymentManager();
    $result = $paymentManager->createGcashCheckout($cart, $totalAmount, $paymentMethod);

    if ($result['success']) {

        $_SESSION['pending_cart'] = $cart;
        $_SESSION['pending_total'] = $totalAmount;
        $_SESSION['pending_payment_method'] = $paymentMethod;
        $_SESSION['pending_discount'] = $discountAmount;
        $_SESSION['pending_discount_type'] = $discountType;
        $_SESSION['pending_email'] = $customerEmail;

        echo json_encode([
            'success' => true,
            'is_redirect' => true,
            'checkout_url' => $result['checkout_url']
        ]);
        exit;

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'PayMongo Error: ' . $result['message']
        ]);
        exit;

    }

} else {

    require_once '../Database/Database.php';
    require_once 'TransactionManager.php';

    $dbInstance = new Database();
    $dbConnection = $dbInstance->getConnection();

    $tm = new TransactionManager($dbConnection);
    $userId = $_SESSION['user_id'];
    $processResult = $tm->processCheckout(
        $cart,
        $paymentMethod,
        $discountAmount,
        $discountType,
        $totalAmount,
        $cashReceived,
        $changeAmount,
        $userId
    );

    if ($processResult['success']) {

        echo json_encode([
            'success'   => true,
            'message'   => 'Sale Confirmed',
            'order_no'  => $processResult['order_no']
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Database Error: ' . $processResult['message']
        ]);

    }

    exit;
}