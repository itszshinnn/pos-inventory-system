<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null'); // Restrict open cross-origin access
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

// 1. GATEKEEPER: Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required. Please login.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

// 2. AUTHORIZATION: Restrict database modifications strictly to Admins
if (in_array($method, ['POST', 'PUT', 'DELETE']) && ($_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Administrative privileges required.']);
    exit;
}

switch ($method) {
    case 'GET':
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY id ASC');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $name = trim($input['name'] ?? '');
        if (!$name) { http_response_code(400); echo json_encode(['error' => 'Name is required']); break; }
        try {
            $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
            $stmt->execute([$name]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'name' => $name]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode(['error' => 'Category already exists']);
        }
        break;

    case 'PUT':
        $id   = intval($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        if (!$id || !$name) { http_response_code(400); echo json_encode(['error' => 'ID and name required']); break; }
        try {
            $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
            $stmt->execute([$name, $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode(['error' => 'Name already exists']);
        }
        break;

    case 'DELETE':
        $id = intval($input['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); break; }
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>