<?php
require '../Database/Database.php';

$database = new Database();
$pdo = $database->getConnection();
require_once 'XMLManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xml_file'])) {
    
    $database = new Database();
    $pdo = $database->getConnection();
    $xmlManager = new XMLManager($pdo);

    try {
        $file = $_FILES['xml_file']['tmp_name'];
        $xmlManager->importXML($file);
        
        header("Location: ../Inventory_frontend/xml.php?success=imported");
        exit;
    } catch (Exception $e) {
        header("Location: ../Inventory_frontend/xml.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: ../Inventory_frontend/xml.php");
    exit;
}
?>