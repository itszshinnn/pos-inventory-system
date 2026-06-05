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
        $tables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $rootNode = $xml->createElement('database');
        $xml->appendChild($rootNode);

        foreach ($tables as $table) {
            $tableNode = $xml->createElement($table);
            $rootNode->appendChild($tableNode);

            try {
                $stmt = $this->db->query("SELECT * FROM $table");

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $rowNode = $xml->createElement('row');

                    foreach ($row as $column => $value) {
                        $colNode = $xml->createElement($column, htmlspecialchars($value ?? ''));
                        $rowNode->appendChild($colNode);
                    }

                    $tableNode->appendChild($rowNode);
                }
            } catch (Exception $e) {
                continue;
            }
        }

        return $xml->saveXML();
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
        $rootName = $rootElement->nodeName;
        $allowedTables = ['categories', 'users', 'products', 'product_batches', 'inventory_logs', 'orders', 'order_items'];

        try {
            $this->db->beginTransaction();

            if ($rootName === 'database') {

                foreach ($rootElement->childNodes as $tableNode) {
                    if ($tableNode->nodeType == XML_ELEMENT_NODE) {
                        $tableName = $tableNode->nodeName;

                        if (in_array($tableName, $allowedTables)) {
                            foreach ($tableNode->childNodes as $row) {
                                if ($row->nodeType == XML_ELEMENT_NODE && $row->nodeName === 'row') {
                                    $this->insertXMLRow($row, $tableName);
                                }
                            }
                        }
                    }
                }
            }
            // SCENARIO 2: It is a SINGLE table export (Root node is the table name)
            else {
                if (!in_array($rootName, $allowedTables)) {
                    throw new Exception("Invalid XML format or unauthorized table: " . $rootName);
                }

                $rows = $dom->getElementsByTagName('row');
                if ($rows->length == 0) {
                    throw new Exception("No data found in the XML file.");
                }

                foreach ($rows as $row) {
                    $this->insertXMLRow($row, $rootName);
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Database error during import: " . $e->getMessage());
        }
    }

    // Helper Function: Safely inserts a single XML row into the specified table
    private function insertXMLRow($rowNode, $tableName)
    {
        $columns = [];
        $values = [];
        $placeholders = [];

        foreach ($rowNode->childNodes as $node) {
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
}
