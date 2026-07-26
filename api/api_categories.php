<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: null');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../src/Database.php';
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

require_once '../src/InventoryManager.php';
$manager = new InventoryManager($pdo);

switch ($method) {
    case 'GET':
        echo json_encode($manager->getAllCategories());
        break;

    case 'POST':
        $name = trim($input['name'] ?? '');
        if (!$name) { http_response_code(400); echo json_encode(['error' => 'Name is required']); break; }
        
        $result = $manager->addCategory($name);
        if (isset($result['error'])) http_response_code($result['code']);
        echo json_encode($result);
        break;

    case 'PUT':
        $id   = intval($input['id'] ?? 0);
        $name = trim($input['name'] ?? '');
        if (!$id || (!$name)) { http_response_code(400); echo json_encode(['error' => 'ID and name required']); break; }
        
        $result = $manager->updateCategory($id, $name);
        if (isset($result['error'])) http_response_code($result['code']);
        echo json_encode($result);
        break;

    case 'DELETE':
        $id = intval($input['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); break; }
        
        $result = $manager->deleteCategory($id);
        if (isset($result['error'])) http_response_code($result['code']);
        echo json_encode($result);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>