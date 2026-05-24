<?php
require '../Database/config.php';

$tables = ['categories','users','products','product_batches','inventory_logs','orders','order_items'];

$exportDir = "../XML_files/";

if (!is_dir($exportDir)) {
    mkdir($exportDir, 0777, true);
}

try {

    foreach ($tables as $table) {

        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->formatOutput = true;
        $root = $dom->createElement($table);
        $dom->appendChild($root);

        $stmt = $pdo->query("SELECT * FROM $table");

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $item = $dom->createElement("row");

            foreach ($row as $column => $value) {
                $node = $dom->createElement(
                    $column,
                    htmlspecialchars($value ?? '')
                );
                $item->appendChild($node);
            }

            $root->appendChild($item);
        }

        $filePath = $exportDir . $table . ".xml";
        $dom->save($filePath);

    }

    header("Location: ../Inventory_frontend/xml.php?success=exported");
    exit;

} catch (Exception $e) {

    header("Location: ../Inventory_frontend/xml.php?error=" . urlencode($e->getMessage()));
    exit;
}