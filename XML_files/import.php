<?php
require '../Database/config_test.php';

$importMap = [
    'categories_export.xml'    => ['table' => 'categories',     'tag' => 'Categorie'],
    'users_export.xml'         => ['table' => 'users',          'tag' => 'User'],
    'products_export.xml'      => ['table' => 'products',       'tag' => 'Product'],
    'product_batches_export.xml' => ['table' => 'product_batches', 'tag' => 'Product_batch'],
    'inventory_logs_export.xml'=> ['table' => 'inventory_logs', 'tag' => 'Inventory_log'],
    'orders_export.xml'        => ['table' => 'orders',         'tag' => 'Order'],
    'order_items_export.xml'   => ['table' => 'order_items',    'tag' => 'Order_item']
];

foreach ($importMap as $filename => $config) {
    if (!file_exists($filename)) {
        echo "Skipping: $filename (File not found)<br>";
        continue;
    }

    $dom = new DOMDocument();
    $dom->load($filename);
    $items = $dom->getElementsByTagName($config['tag']);
    $table = $config['table'];

    $pdo->beginTransaction();
    try {
        foreach ($items as $item) {
            $data = [];
            foreach ($item->childNodes as $node) {
                if ($node->nodeType == 1) { // 1 = Element node
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
?>