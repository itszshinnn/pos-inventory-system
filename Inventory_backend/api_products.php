<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require '../Database/config.php';

$method = $_SERVER['REQUEST_METHOD'];

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

    // POST — add product with Multipart Form image uploading
    case 'POST':
        // Read from standard POST superglobals since content type is multipart/form-data
        $name        = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price       = floatval($_POST['price'] ?? 0);
        $stock       = intval($_POST['stock'] ?? 0);
        
        // Fallback default placeholder if no custom asset is attached
        $imageName = 'default_product.png'; 

        if (!$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required and must be valid']);
            break;
        }

        // Check if an image file was physically attached to the request
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName    = $_FILES['image']['name'];
            
            // Isolate file extension to filter out executable files
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                // Generate a unique timestamped title to avoid cross-over bugs
                $imageName = time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadFileDir = '../Images/';
                $dest_path = $uploadFileDir . $imageName;

                // Move file safely from memory partition into your persistent directory
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = 'default_product.png'; // Roll back to fallback string if upload fails
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid file extension style. Only JPG, PNG, WEBP, and GIF allowed.']);
                break;
            }
        }

        // Insert including the newly created dynamic image filename string
        $stmt = $pdo->prepare('INSERT INTO products (name, category_id, price, stock, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $category_id, $price, $stock, $imageName]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    // PUT — edit product
    case 'PUT':
        // Keep using input stream reading for PUT since it remains standard JSON payload data
        $input = json_decode(file_get_contents('php://input'), true);
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
        $input = json_decode(file_get_contents('php://input'), true);
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
?>