<?php
require '../Database/config.php';

$tables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];

foreach ($tables as $table) {
    $filename = $table . "_export.xml";
    if (!file_exists($filename)) continue;

    $dom = new DOMDocument();
    $dom->load($filename);
    $items = $dom->getElementsByTagName($table);

    $pdo->beginTransaction();
    try {
        foreach ($items as $item) {
            $data = [];
            foreach ($item->childNodes as $node) {
                if ($node->nodeType == 1) {
                    $data[$node->nodeName] = $node->nodeValue;
                }
            }

            $columns = implode(", ", array_keys($data));
            $placeholders = implode(", ", array_fill(0, count($data), "?"));

            $stmt = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
            $stmt->execute(array_values($data));
        }
        $pdo->commit();
        echo "Successfully imported: $table <br>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error importing $table: " . $e->getMessage() . "<br>";
    }
}
