<?php
session_start();
header('Content-Type: application/json');
require_once 'PaymentManager.php';

$data = json_decode(file_get_contents('php://input'), true);

$cart = $data['cart'] ?? [];
$paymentMethod = trim($data['payment_method'] ?? '');
$totalAmount = floatval($data['total_amount'] ?? 0);
$discountAmount = floatval($data['discount_amount'] ?? 0);
$discountType = isset($data['discount_type']) ? trim($data['discount_type']) : null;
$cashReceived = floatval($data['cash_received'] ?? 0);
$changeAmount = floatval($data['change_amount'] ?? 0);
$userId = $_SESSION['user_id'];
$customerEmail = trim($data['email'] ?? "");

// --- SERVER-SIDE VALIDATION ---
$errors = [];

// 1. Validate cart presence and array structure
if (empty($cart) || !is_array($cart)) {
    $errors[] = "Checkout failed: Cart is empty or invalid.";
} else {
    foreach ($cart as $index => $item) {
        $itemId = intval($item['id'] ?? 0);
        $itemQty = intval($item['qty'] ?? 0);
        $itemPrice = floatval($item['price'] ?? -1);

        if ($itemId <= 0 || $itemQty <= 0 || $itemPrice < 0) {
            $errors[] = "Invalid item data detected in cart item #" . ($index + 1) . ".";
            break;
        }
    }
}

// 2. Validate allowed payment methods
$allowedPaymentMethods = ['Cash', 'Card', 'GCash', 'Maya'];
if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
    $errors[] = "Invalid payment method selected: " . htmlspecialchars($paymentMethod);
}

// 3. Validate numerical amounts
if ($totalAmount < 0 || $discountAmount < 0) {
    $errors[] = "Total amount or discount amount cannot be negative.";
}

// 4. Validate cash sufficiency for Cash payments
if ($paymentMethod === 'Cash' && $cashReceived < $totalAmount) {
    $errors[] = "Insufficient cash received. Required: Php" . number_format($totalAmount, 2) . ", Received: Php" . number_format($cashReceived, 2);
}

// 5. Validate customer email format if provided
if (!empty($customerEmail) && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid customer email address format.";
}

// If any validation failed, reject transaction immediately
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    exit;
}

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