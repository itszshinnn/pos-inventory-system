<?php
require '../Database/config.php';

$tables = ['categories', 'inventory_logs', 'orders', 'order_items', 'products', 'product_batches', 'users'];


try {

    foreach ($tables as $table) {
        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->formatOutput = true;

        $root = $dom->createElement(ucfirst($table) . "Data");
        $dom->appendChild($root);

        $stmt = $pdo->query("SELECT * FROM $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $item = $dom->createElement($table);

            foreach ($row as $column => $value) {
                $node = $dom->createElement($column, htmlspecialchars($value ?? ''));
                $item->appendChild($node);
            }
            $root->appendChild($item);
        }

        $dom->save($table . "_export.xml");
        echo "Generated: " . $table . "_export.xml<br>";
    }

    header("Location: ../Inventory_frontend/xml.php?success=exported");
    exit;

} catch (Exception $e) {

    $error = urlencode($e->getMessage());

    header("Location: ../Inventory_frontend/xml.php?error=$error");
    exit;
}