<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../src/Database.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $code = isset($input['code']) ? trim(strtoupper($input['code'])) : '';

    if (empty($code)) {
        echo json_encode(['success' => false, 'error' => 'Promo code is required.']);
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT * FROM promos WHERE UPPER(code) = :code AND is_active = 1 LIMIT 1");
    $stmt->execute([':code' => $code]);
    $promo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($promo) {
        echo json_encode([
            'success' => true,
            'code' => $promo['code'],
            'discount_value' => (float)$promo['discount_value'],
            'discount_type' => $promo['discount_type']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or expired promo code.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Server Error: ' . $e->getMessage()
    ]);
}
?>
