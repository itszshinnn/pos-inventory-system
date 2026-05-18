<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

switch ($method) {

    // GET all products (joined with category name)
    case 'GET':
        $sql = 'SELECT p.id, p.name, p.price, p.stock, p.category_id,
                       c.name AS category
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY p.id ASC';
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll());
        break;

    // POST — add product
    case 'POST':
        $name        = trim($input['name'] ?? '');
        $category_id = intval($input['category_id'] ?? 0);
        $price       = floatval($input['price'] ?? 0);
        $stock       = intval($input['stock'] ?? 0);
        if (!$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required and must be valid']);
            break;
        }
        $stmt = $pdo->prepare('INSERT INTO products (name, category_id, price, stock) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $category_id, $price, $stock]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    // PUT — edit product
    case 'PUT':
        $id          = intval($input['id'] ?? 0);
        $name        = trim($input['name'] ?? '');
        $category_id = intval($input['category_id'] ?? 0);
        $price       = floatval($input['price'] ?? 0);
        $stock       = intval($input['stock'] ?? 0);
        if (!$id || !$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE products SET name=?, category_id=?, price=?, stock=? WHERE id=?');
        $stmt->execute([$name, $category_id, $price, $stock, $id]);
        echo json_encode(['success' => true]);
        break;

    // DELETE — remove product
    case 'DELETE':
        $id = intval($input['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); break; }
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
