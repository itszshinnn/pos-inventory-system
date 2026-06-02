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
    echo json_encode(['error' => 'Authentication required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if (in_array($method, ['POST', 'PUT', 'DELETE']) && ($_SESSION['role'] !== 'admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied. Administrative privileges required.']);
    exit;
}

switch ($method) {
    case 'GET':
        require_once 'InventoryManager.php';
        $manager = new InventoryManager($pdo);
        $products = $manager->getAllProducts();
        echo json_encode($products);
        break;

    case 'POST':
        $name        = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $price       = floatval($_POST['price'] ?? 0);
        $stock       = intval($_POST['stock'] ?? 0);
        $imageName   = 'default_product.png';

        if (!$name || !$category_id || $price < 0 || $stock < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required and must be valid']);
            break;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($fileTmpPath);

            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif'
            ];

            if (array_key_exists($mimeType, $allowedMimeTypes)) {
                $fileExtension = $allowedMimeTypes[$mimeType];
                $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                $dest_path = '../Images/' . $imageName;

                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $imageName = 'default_product.png';
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid image format. Only JPG, PNG, WEBP, and GIF allowed.']);
                break;
            }
        }

        require_once 'InventoryManager.php';
        require_once 'Product.php';
        $manager = new InventoryManager($pdo);
        $newProduct = new Product($name, $category_id, $price, $stock);
        $result = $manager->addProduct($newProduct, $imageName, $_SESSION['username']);
        if (isset($result['error'])) {
            http_response_code(500);
        }
        echo json_encode($result);
        break;

    case 'PUT':
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

        require_once 'InventoryManager.php';
        require_once 'Product.php';

        $manager = new InventoryManager($pdo);

        $updatedProduct = new Product($name, $category_id, $price, $stock);

        // 3. Hand the ID and the Object over to the manager
        $result = $manager->updateProduct($id, $updatedProduct, $_SESSION['username']);

        if (isset($result['error'])) {
            http_response_code($result['code']);
        }
        echo json_encode($result);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID required']);
            break;
        }

        require_once 'InventoryManager.php';
        $manager = new InventoryManager($pdo);

        $result = $manager->deleteProduct($id, $_SESSION['username']);

        if (isset($result['error'])) {
            http_response_code($result['code']);
        }
        echo json_encode($result);
        break;
}
