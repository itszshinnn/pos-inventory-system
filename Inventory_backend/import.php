<?php
require '../Database/config.php';

$tables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];

try {

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    if (!isset($_FILES['xml_file'])) {
        throw new Exception("No file uploaded");
    }

    $file = $_FILES['xml_file']['tmp_name'];
    $dom = new DOMDocument();
    $dom->load($file);
    $rootName = $dom->documentElement->nodeName;

    if ($rootName === "DatabaseExport") {

        foreach (array_reverse($tables) as $table) {
            $pdo->exec("TRUNCATE TABLE $table");
        }

        foreach ($dom->documentElement->childNodes as $tableNode) {

            if ($tableNode->nodeType !== XML_ELEMENT_NODE) continue;
            $table = $tableNode->nodeName;

            if (!in_array($table, $tables)) continue;

            $colStmt = $pdo->query("DESCRIBE $table");
            $dbColumns = $colStmt->fetchAll(PDO::FETCH_COLUMN);
            /** @var DOMElement $tableNode */
            $items = $tableNode->getElementsByTagName("row");

            foreach ($items as $item) {

                $data = [];

                foreach ($item->childNodes as $node) {
                    if ($node->nodeType == 1) {

                        if ($node->nodeName === "id") continue;

                        $data[$node->nodeName] = $node->nodeValue;
                    }
                }

                $validData = array_intersect_key($data, array_flip($dbColumns));

                if (empty($validData)) continue;

                $columns = implode(", ", array_keys($validData));
                $placeholders = implode(", ", array_fill(0, count($validData), "?"));

                $stmt = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
                $stmt->execute(array_values($validData));
            }
        }

    }


    else {

        $table = $rootName;

        if (!in_array($table, $tables)) {
            throw new Exception("Invalid table XML");
        }

        $pdo->exec("TRUNCATE TABLE $table");
        $colStmt = $pdo->query("DESCRIBE $table");
        $dbColumns = $colStmt->fetchAll(PDO::FETCH_COLUMN);
        $items = $dom->getElementsByTagName("row");

        foreach ($items as $item) {

            $data = [];

            foreach ($item->childNodes as $node) {
                if ($node->nodeType == 1) {

                    if ($node->nodeName === "id") continue;

                    $data[$node->nodeName] = $node->nodeValue;
                }
            }

            $validData = array_intersect_key($data, array_flip($dbColumns));

            if (empty($validData)) continue;

            $columns = implode(", ", array_keys($validData));
            $placeholders = implode(", ", array_fill(0, count($validData), "?"));
            $stmt = $pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
            $stmt->execute(array_values($validData));
        }
    }


    header("Location: ../Inventory_frontend/xml.php?success=imported");
    exit;

} catch (Exception $e) {

    header("Location: ../Inventory_frontend/xml.php?error=" . urlencode($e->getMessage()));
    exit;

} finally {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}
?>