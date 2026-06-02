<?php
require_once '../Database/Database.php';
require_once 'XMLManager.php';

$database = new Database();
$pdo = $database->getConnection();
$xmlManager = new XMLManager($pdo);

try {
    $xmlManager->exportAllTablesToFolder('../XML_files/');
    header("Location: ../Inventory_frontend/xml.php?success=exported");
    exit;
} catch (Exception $e) {
    header("Location: ../Inventory_frontend/xml.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>