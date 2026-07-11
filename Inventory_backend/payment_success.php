<?php
session_start();

if (isset($_SESSION['pending_cart']) && !empty($_SESSION['pending_cart'])) {
    
    $cart = $_SESSION['pending_cart'];
    $totalAmount = $_SESSION['pending_total'];
    $paymentMethod = $_SESSION['pending_payment_method'];
    $discountAmount = $_SESSION['pending_discount'] ?? 0;

    require_once '../Database/Database.php';
    require_once 'TransactionManager.php';

    $dbInstance = new Database(); 
    $dbConnection = $dbInstance->getConnection(); 

    $tm = new TransactionManager($dbConnection);
    
    $tm->processCheckout($cart, $paymentMethod, $discountAmount, $totalAmount, $totalAmount, 0);

    unset($_SESSION['pending_cart']);
    unset($_SESSION['pending_total']);
    unset($_SESSION['pending_payment_method']);
    unset($_SESSION['pending_discount']);
}

header("Location: ../Inventory_frontend/point_of_sale_menu.php?payment=success");
exit();
?>