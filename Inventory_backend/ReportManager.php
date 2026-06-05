<?php
require_once '../Database/Database.php';

class ReportManager {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getDashboardMetrics() {
        $metrics = [ ];

        try {
            $metrics['productLogs'] = $this->db->query("SELECT product_name, action_type, changed_by, created_at FROM inventory_logs ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            $metrics['salesLogs']   = $this->db->query("SELECT order_no, total_amount, created_at FROM orders ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            $metrics['lowStocks']   = $this->db->query("SELECT name, stock FROM products WHERE stock <= 3 ORDER BY stock ASC")->fetchAll(PDO::FETCH_ASSOC);
            $metrics['newUsers']    = $this->db->query("SELECT username, role, created_at FROM users ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

            $metrics['totalProducts']   = $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
            $metrics['totalUnits']      = $this->db->query('SELECT COALESCE(SUM(stock), 0) FROM products')->fetchColumn();
            $metrics['totalCategories'] = $this->db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
            $metrics['lowStock']        = $this->db->query('SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 3')->fetchColumn();
            $metrics['outOfStock']      = $this->db->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn();

            $metrics['totalRevenue']    = $this->db->query('SELECT COALESCE(SUM(total_amount), 0) FROM orders')->fetchColumn();
            $metrics['transactions']    = $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
            $metrics['itemsSold']       = $this->db->query('SELECT COALESCE(SUM(quantity), 0) FROM order_items')->fetchColumn();

            $metrics['totalLogs']       = $this->db->query('SELECT COUNT(*) FROM inventory_logs')->fetchColumn();
            $metrics['totalAdded']      = $this->db->query('SELECT COUNT(*) FROM inventory_logs WHERE action_type = "Added"')->fetchColumn();
            $metrics['totalDeleted']    = $this->db->query('SELECT COUNT(*) FROM inventory_logs WHERE action_type = "Deleted"')->fetchColumn();

            $metrics['errorMsg'] = null;
        } catch (Exception $e) {
            $metrics['productLogs'] = $metrics['salesLogs'] = $metrics['lowStocks'] = $metrics['newUsers'] = [];
            $metrics['totalProducts'] = $metrics['totalUnits'] = $metrics['totalCategories'] = $metrics['lowStock'] = $metrics['outOfStock'] = 0;
            $metrics['totalRevenue'] = 0.00;
            $metrics['transactions'] = $metrics['itemsSold'] = $metrics['totalLogs'] = $metrics['totalAdded'] = $metrics['totalDeleted'] = 0;
            $metrics['errorMsg'] = $e->getMessage();
        }

        return $metrics;
    }

    public function getSalesHistory() {
        try {
            $query = 'SELECT o.order_no, 
                             o.payment_method AS payment, 
                             o.discount_amount AS discount, 
                             o.total_amount AS total,
                             o.cash_received,
                             o.change_amount,
                             o.created_at AS date,
                             GROUP_CONCAT(CONCAT(p.name, " x", oi.quantity) SEPARATOR ", ") AS item
                      FROM orders o
                      LEFT JOIN order_items oi ON o.id = oi.order_id
                      LEFT JOIN products p ON oi.product_id = p.id
                      GROUP BY o.id
                      ORDER BY o.id DESC';
                      
            return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getFullInventoryLogs() {
        try {
            $query = "SELECT * FROM inventory_logs ORDER BY id DESC";
            return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>