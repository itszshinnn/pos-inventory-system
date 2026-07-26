<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/Database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required. Please login.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

if (in_array($method, ['POST', 'PUT', 'DELETE']) && ($_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Administrative privileges required.']);
    exit;
}

switch ($method) {
    case 'GET':
        $stmt = $pdo->prepare("SELECT * FROM promos ORDER BY created_at DESC");
        $stmt->execute();
        $promos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($promos);
        break;

    case 'POST':
        $code = trim(strtoupper($input['code'] ?? ''));
        $val = floatval($input['discount_value'] ?? 0);
        $type = trim($input['discount_type'] ?? 'percent');
        $active = intval($input['is_active'] ?? 1);

        if (empty($code) || $val <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Promo code and a valid discount value are required.']);
            break;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO promos (code, discount_value, discount_type, is_active) VALUES (:code, :val, :type, :active)");
            $stmt->execute([
                ':code' => $code,
                ':val' => $val,
                ':type' => $type,
                ':active' => $active
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        $id = intval($input['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID is required.']);
            break;
        }

        if (isset($input['toggle_active'])) {
            $active = intval($input['is_active'] ?? 1);
            try {
                $stmt = $pdo->prepare("UPDATE promos SET is_active = :active WHERE id = :id");
                $stmt->execute([':active' => $active, ':id' => $id]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            $code = trim(strtoupper($input['code'] ?? ''));
            $val = floatval($input['discount_value'] ?? 0);
            $type = trim($input['discount_type'] ?? 'percent');
            $active = intval($input['is_active'] ?? 1);

            if (empty($code) || $val <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Promo code and a valid discount value are required.']);
                break;
            }

            try {
                $stmt = $pdo->prepare("UPDATE promos SET code = :code, discount_value = :val, discount_type = :type, is_active = :active WHERE id = :id");
                $stmt->execute([
                    ':code' => $code,
                    ':val' => $val,
                    ':type' => $type,
                    ':active' => $active,
                    ':id' => $id
                ]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
        }
        break;

    case 'DELETE':
        $id = intval($input['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM promos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
