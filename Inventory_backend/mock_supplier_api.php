<?php
header('Content-Type: application/json');

sleep(2); 

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid Purchase Order data.']);
    exit;
}

$total_cost = 0;
foreach ($data['items'] as $item) {
    $total_cost += ($item['price_bought'] * $item['order_qty']);
}

$simulated_corporate_balance = 100000.00;

if ($total_cost > $simulated_corporate_balance) {
    echo json_encode([
        'success' => false, 
        'error' => 'Insufficient corporate funds. Required: ₱' . number_format($total_cost, 2)
    ]);
    exit;
}

$supplier_ref = 'SUP-INV-' . strtoupper(substr(md5(uniqid()), 0, 8));

echo json_encode([
    'success' => true,
    'message' => 'Order received and payment authorized.',
    'supplier_reference' => $supplier_ref,
    'total_deducted' => $total_cost
]);
?>