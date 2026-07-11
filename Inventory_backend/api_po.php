<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/Database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied.']);
    exit;
}

$username = $_SESSION['username'] ?? 'Admin';
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query("
                SELECT po.id, po.reference_no, po.status, po.created_at, 
                       COUNT(poi.id) as total_items
                FROM purchase_orders po
                LEFT JOIN po_items poi ON po.id = poi.po_id
                WHERE po.status = 'Pending'
                GROUP BY po.id
                ORDER BY po.created_at DESC
            ");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'POST':
        if (!isset($input['items']) || empty($input['items'])) {
            echo json_encode(['error' => 'No items provided for the order.']);
            break;
        }

        try {
            $pdo->beginTransaction();

            $refStmt = $pdo->query('SELECT COUNT(*) FROM purchase_orders');
            $nextId = $refStmt->fetchColumn() + 1;
            $refNumber = 'PO-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $poStmt = $pdo->prepare("INSERT INTO purchase_orders (reference_no, status) VALUES (?, 'Pending')");
            $poStmt->execute([$refNumber]);
            $poId = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("INSERT INTO po_items (po_id, product_id, order_qty, unit_cost) VALUES (?, ?, ?, ?)");
            $costStmt = $pdo->prepare("SELECT price_bought FROM products WHERE id = ?");

            foreach ($input['items'] as $item) {
                $costStmt->execute([$item['product_id']]);
                $currentCost = $costStmt->fetchColumn();

                $itemStmt->execute([$poId, $item['product_id'], $item['order_qty'], $currentCost]);
            }

            $pdo->commit();
            echo json_encode(['success' => true, 'reference_no' => $refNumber]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['error' => 'Failed to create PO: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        $poId = intval($input['po_id'] ?? 0);
        if (!$poId) {
            echo json_encode(['error' => 'Purchase Order ID required.']);
            break;
        }

        try {
            $pdo->beginTransaction();

            $updatePO = $pdo->prepare("UPDATE purchase_orders SET status = 'Received', received_by = ? WHERE id = ?");
            $updatePO->execute([$username, $poId]);

            $getItems = $pdo->prepare("
                SELECT poi.product_id, poi.order_qty, poi.unit_cost, p.name, p.stock 
                FROM po_items poi
                JOIN products p ON poi.product_id = p.id
                WHERE poi.po_id = ?
            ");
            $getItems->execute([$poId]);
            $items = $getItems->fetchAll(PDO::FETCH_ASSOC);

            $updateStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            $addBatch = $pdo->prepare("INSERT INTO product_batches (product_id, quantity_received, quantity_remaining, unit_cost) VALUES (?, ?, ?, ?)");
            $logAction = $pdo->prepare("INSERT INTO inventory_logs (product_id, product_name, action_type, old_stock, new_stock, changed_by) VALUES (?, ?, 'Restocked', ?, ?, ?)");

            foreach ($items as $item) {
                $newTotalStock = $item['stock'] + $item['order_qty'];

                $updateStock->execute([$item['order_qty'], $item['product_id']]);

                $addBatch->execute([$item['product_id'], $item['order_qty'], $item['order_qty'], $item['unit_cost']]);

                $logAction->execute([$item['product_id'], $item['name'], $item['stock'], $newTotalStock, $username]);
            }

            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['error' => 'Failed to receive PO: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>