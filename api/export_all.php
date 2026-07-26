<?php
require_once '../src/Database.php';
require_once '../src/XMLManager.php';

$database = new Database();
$pdo = $database->getConnection();
$xmlManager = new XMLManager($pdo);

header('Content-Type: text/xml');
header('Content-Disposition: attachment; filename="database_export.xml"');

echo $xmlManager->exportAll();
?>