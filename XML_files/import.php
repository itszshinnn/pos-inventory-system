<?php
require '../Database/config.php';

$tables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];

$hasError = false;
$errorMessages = [];

try {

    foreach ($tables as $table) {
        $filename = $table . "_export.xml";

        if (!file_exists($filename)) {
            continue;
        }

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
        } catch (Exception $e) {

            $pdo->rollBack();
            $hasError = true;
            $errorMessages[] = "$table: " . $e->getMessage();
        }
    }

    if ($hasError) {
        $error = urlencode($errorMessages[0]);
        header("Location: ../Inventory_frontend/xml.php?error=$error");
        exit;
    }
    header("Location: ../Inventory_frontend/xml.php?success=imported");
    exit;

} catch (Exception $e) {

    $error = urlencode($e->getMessage());
    header("Location: ../Inventory_frontend/xml.php?error=$error");
    exit;
}