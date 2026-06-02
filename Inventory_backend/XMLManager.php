<?php
require_once '../Database/Database.php';

class XMLManager
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function exportAll()
    {
        $stmt = $this->db->query("SELECT p.id, p.name, p.price, p.stock, c.name AS category_name 
                                  FROM products p 
                                  LEFT JOIN categories c ON p.category_id = c.id 
                                  ORDER BY p.id ASC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->generateXML($products);
    }

    public function exportIndividual($id)
    {
        $stmt = $this->db->prepare("SELECT p.id, p.name, p.price, p.stock, c.name AS category_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    WHERE p.id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return false;
        }

        return $this->generateXML([$product]);
    }

    private function generateXML($dataArray)
    {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $inventoryNode = $xml->createElement('inventory');
        $xml->appendChild($inventoryNode);

        foreach ($dataArray as $row) {
            $productNode = $xml->createElement('product');

            foreach ($row as $key => $value) {
                $node = $xml->createElement($key, htmlspecialchars($value ?? ''));
                $productNode->appendChild($node);
            }

            $inventoryNode->appendChild($productNode);
        }

        return $xml->saveXML();
    }

    public function exportAllTablesToFolder($exportDir = '../XML_files/')
    {
        $tables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];

        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
        }

        try {
            foreach ($tables as $table) {
                $dom = new DOMDocument("1.0", "UTF-8");
                $dom->formatOutput = true;
                $root = $dom->createElement($table);
                $dom->appendChild($root);

                $stmt = $this->db->query("SELECT * FROM $table");

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $item = $dom->createElement("row");
                    foreach ($row as $column => $value) {
                        $node = $dom->createElement($column, htmlspecialchars($value ?? ''));
                        $item->appendChild($node);
                    }
                    $root->appendChild($item);
                }

                $filePath = $exportDir . $table . ".xml";
                $dom->save($filePath);
            }
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function importXML($fileTmpPath)
    {
        $dom = new DOMDocument();

        if (!$dom->load($fileTmpPath)) {
            throw new Exception("Failed to load or parse the XML file.");
        }

        $rootElement = $dom->documentElement;
        $tableName = $rootElement->nodeName;

        $allowedTables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];
        if (!in_array($tableName, $allowedTables)) {
            throw new Exception("Invalid XML format or unauthorized table: " . $tableName);
        }

        $rows = $dom->getElementsByTagName('row');
        if ($rows->length == 0) {
            throw new Exception("No data found in the XML file.");
        }

        try {
            $this->db->beginTransaction();

            foreach ($rows as $row) {
                $columns = [];
                $values = [];
                $placeholders = [];

                foreach ($row->childNodes as $node) {
                    if ($node->nodeType == XML_ELEMENT_NODE) {
                        $columns[] = $node->nodeName;
                        $values[] = $node->nodeValue;
                        $placeholders[] = '?';
                    }
                }

                if (!empty($columns)) {
                    $colString = implode(', ', $columns);
                    $placeholderString = implode(', ', $placeholders);

                    $sql = "INSERT IGNORE INTO $tableName ($colString) VALUES ($placeholderString)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($values);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Database error during import: " . $e->getMessage());
        }
    }
}
