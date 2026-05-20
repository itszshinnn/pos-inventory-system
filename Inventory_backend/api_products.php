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
        $sql = 'SELECT p.id, p.name, p.price, p.stock, p.category_id, p.image,
                       c.name AS category
                FROM products p
                JOIN categories c ON p.category_id = c.id
                ORDER BY p.id ASC';
        $stmt = $pdo->query($sql);
        echo json_encode($stmt->fetchAll());
        break;

    // POST — add product with Multipart Form image uploading
    case 'POST':

    session_start();

    $name        = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $price       = floatval($_POST['price'] ?? 0);
    $stock       = intval($_POST['stock'] ?? 0);

    $imageName = 'default_product.png';

    if (!$name || !$category_id || $price < 0 || $stock < 0) {

        http_response_code(400);

        echo json_encode([
            'error' => 'All fields are required and must be valid'
        ]);

        break;
    }

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] === UPLOAD_ERR_OK
    ) {

        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName    = $_FILES['image']['name'];

        $fileExtension = strtolower(
            pathinfo($fileName, PATHINFO_EXTENSION)
        );

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'gif'
        ];

        if (in_array($fileExtension, $allowedExtensions)) {

            $imageName =
                time() .
                '_' .
                uniqid() .
                '.' .
                $fileExtension;

            $uploadFileDir = '../Images/';
            $dest_path = $uploadFileDir . $imageName;

            if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                $imageName = 'default_product.png';
            }

        } else {

            http_response_code(400);

            echo json_encode([
                'error' => 'Invalid image format'
            ]);

            break;
        }
    }

    // INSERT PRODUCT
    $stmt = $pdo->prepare("
        INSERT INTO products
        (
            name,
            category_id,
            price,
            stock,
            image
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $name,
        $category_id,
        $price,
        $stock,
        $imageName
    ]);

    $productId = $pdo->lastInsertId();

    // INSERT LOG
    $log = $pdo->prepare("
        INSERT INTO product_logs
        (
            product_id,
            product_name,
            action_type,
            old_stock,
            new_stock,
            changed_by
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $log->execute([
        $productId,
        $name,
        'Added',
        null,
        $stock,
        $_SESSION['username'] ?? 'Unknown User'
    ]);

    echo json_encode([
        'success' => true,
        'id' => $productId
    ]);

    break;
    // PUT — edit product
    case 'PUT':

    session_start();

    $input = json_decode(
        file_get_contents('php://input'),
        true
    );

    $id          = intval($input['id'] ?? 0);
    $name        = trim($input['name'] ?? '');
    $category_id = intval($input['category_id'] ?? 0);
    $price       = floatval($input['price'] ?? 0);
    $stock       = intval($input['stock'] ?? 0);

    if (
        !$id ||
        !$name ||
        !$category_id ||
        $price < 0 ||
        $stock < 0
    ) {

        http_response_code(400);

        echo json_encode([
            'error' => 'All fields are required'
        ]);

        break;
    }

    // GET OLD DATA
    $getOld = $pdo->prepare("
        SELECT stock, name
        FROM products
        WHERE id = ?
    ");

    $getOld->execute([$id]);

    $oldProduct = $getOld->fetch(PDO::FETCH_ASSOC);

    // UPDATE PRODUCT
    $stmt = $pdo->prepare("
        UPDATE products
        SET
            name = ?,
            category_id = ?,
            price = ?,
            stock = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $category_id,
        $price,
        $stock,
        $id
    ]);

    // INSERT LOG
    $log = $pdo->prepare("
        INSERT INTO product_logs
        (
            product_id,
            product_name,
            action_type,
            old_stock,
            new_stock,
            changed_by
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $log->execute([
        $id,
        $name,
        'Edited',
        $oldProduct['stock'],
        $stock,
        $_SESSION['username'] ?? 'Unknown User'
    ]);

    echo json_encode([
        'success' => true
    ]);

    break;
    // DELETE — remove product
    case 'DELETE':

    session_start();

    $input = json_decode(
        file_get_contents('php://input'),
        true
    );

    $id = intval($input['id'] ?? 0);

    if (!$id) {

        http_response_code(400);

        echo json_encode([
            'error' => 'ID required'
        ]);

        break;
    }

    // GET PRODUCT FIRST
    $getProduct = $pdo->prepare("
        SELECT name, stock
        FROM products
        WHERE id = ?
    ");

    $getProduct->execute([$id]);

    $product = $getProduct->fetch(PDO::FETCH_ASSOC);

    if (!$product) {

        http_response_code(404);

        echo json_encode([
            'error' => 'Product not found'
        ]);

        break;
    }

    // INSERT LOG
    $log = $pdo->prepare("
        INSERT INTO product_logs
        (
            product_id,
            product_name,
            action_type,
            old_stock,
            new_stock,
            changed_by
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $log->execute([
        $id,
        $product['name'],
        'Deleted',
        $product['stock'],
        null,
        $_SESSION['username'] ?? 'Unknown User'
    ]);

    // DELETE PRODUCT
    $stmt = $pdo->prepare("
        DELETE FROM products
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    echo json_encode([
        'success' => true
    ]);

    break;
}
?>