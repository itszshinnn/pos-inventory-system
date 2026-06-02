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
}
