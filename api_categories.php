<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$input  = json_decode(file_get_contents('php://input'), true);

switch ($method) {

    // GET all categories
    case 'GET':
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY id ASC');
        echo json_encode($stmt->fetchAll());
        break;

    // POST — add new category
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

    // PUT — edit category
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

    // DELETE — remove category (cascades to products)
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
