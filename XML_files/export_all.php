<?php
require '../Database/config.php';

$tables = ['categories', 'inventory_logs', 'orders', 'order_items', 'products', 'product_batches', 'users'];

try {

    $dom = new DOMDocument("1.0", "UTF-8");
    $dom->formatOutput = true;

    $root = $dom->createElement("DatabaseExport");
    $dom->appendChild($root);

    foreach ($tables as $table) {

        $tableNode = $dom->createElement($table);
        $root->appendChild($tableNode);

        $stmt = $pdo->query("SELECT * FROM $table");

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $rowNode = $dom->createElement("row");

            foreach ($row as $column => $value) {
                $colNode = $dom->createElement($column, htmlspecialchars($value ?? ''));
                $rowNode->appendChild($colNode);
            }

            $tableNode->appendChild($rowNode);
        }
    }

    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="database_export.xml"');

    echo $dom->saveXML();
    exit;

} catch (Exception $e) {
    header("Location: ../Inventory_frontend/xml.php?error=" . urlencode($e->getMessage()));
    exit;
}