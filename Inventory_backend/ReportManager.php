<?php
require_once '../Database/Database.php';

class ReportManager
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function getDashboardMetrics($range = 'today', $startDate = null, $endDate = null)
    {
        $metrics = [];
        $dateFieldSql = "";
        $poDateFieldSql = "";
        $logDateFieldSql = "";
        $params = [];

        switch ($range) {
            case 'today':
                $dateFieldSql = "DATE(created_at) = CURDATE()";
                $poDateFieldSql = "DATE(po.created_at) = CURDATE()";
                $logDateFieldSql = "DATE(created_at) = CURDATE()";
                break;
            case 'yesterday':
                $dateFieldSql = "DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                $poDateFieldSql = "DATE(po.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                $logDateFieldSql = "DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'week':
                $dateFieldSql = "DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                $poDateFieldSql = "DATE(po.created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                $logDateFieldSql = "DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $dateFieldSql = "DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                $poDateFieldSql = "DATE(po.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                $logDateFieldSql = "DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $dateFieldSql = "DATE(created_at) BETWEEN :start AND :end";
                    $poDateFieldSql = "DATE(po.created_at) BETWEEN :start AND :end";
                    $logDateFieldSql = "DATE(created_at) BETWEEN :start AND :end";
                    $params[':start'] = $startDate;
                    $params[':end'] = $endDate;
                } else {
                    $dateFieldSql = "DATE(created_at) = CURDATE()";
                    $poDateFieldSql = "DATE(po.created_at) = CURDATE()";
                    $logDateFieldSql = "DATE(created_at) = CURDATE()";
                }
                break;
            case 'alltime':
            default:
                $dateFieldSql = "1=1";
                $poDateFieldSql = "1=1";
                $logDateFieldSql = "1=1";
                break;
        }

        try {
            $logsStmt = $this->db->prepare("SELECT product_name, action_type, changed_by, created_at FROM inventory_logs ORDER BY id DESC LIMIT 5");
            $logsStmt->execute();
            $metrics['productLogs'] = $logsStmt->fetchAll(PDO::FETCH_ASSOC);

            $salesStmt = $this->db->prepare("SELECT order_no, total_amount, created_at FROM orders ORDER BY id DESC LIMIT 5");
            $salesStmt->execute();
            $metrics['salesLogs'] = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

            $metrics['lowStocks']   = $this->db->query("SELECT id, name, stock FROM products WHERE stock <= 3 ORDER BY stock ASC")->fetchAll(PDO::FETCH_ASSOC);

            $usersStmt = $this->db->prepare("SELECT username, role, created_at FROM users ORDER BY id DESC LIMIT 5");
            $usersStmt->execute();
            $metrics['newUsers'] = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

            $metrics['totalProducts']   = $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
            $metrics['totalUnits']      = $this->db->query('SELECT COALESCE(SUM(stock), 0) FROM products')->fetchColumn();
            $metrics['totalCategories'] = $this->db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
            $metrics['lowStock']        = $this->db->query('SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 3')->fetchColumn();
            $metrics['outOfStock']      = $this->db->query('SELECT COUNT(*) FROM products WHERE stock = 0')->fetchColumn();

            $revenueStmt = $this->db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE $dateFieldSql");
            $revenueStmt->execute($params);
            $metrics['totalRevenue'] = $revenueStmt->fetchColumn();

            $transStmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE $dateFieldSql");
            $transStmt->execute($params);
            $metrics['transactions'] = $transStmt->fetchColumn();

            $soldStmt = $this->db->prepare("SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE " . str_replace("created_at", "o.created_at", $dateFieldSql));
            $soldStmt->execute($params);
            $metrics['itemsSold'] = $soldStmt->fetchColumn();

            $perfStmt = $this->db->prepare("
                SELECT
                    COALESCE(SUM(total_amount), 0) AS revenue,
                    COALESCE(SUM(cost_of_goods_sold), 0) AS cogs,
                    COALESCE(SUM(total_amount - cost_of_goods_sold), 0) AS profit,
                    COUNT(*) AS transactions
                FROM orders
                WHERE $dateFieldSql
            ");
            $perfStmt->execute($params);
            $today = $perfStmt->fetch(PDO::FETCH_ASSOC);

            $metrics['todayRevenue']      = $today['revenue'];
            $metrics['todayCOGS']         = $today['cogs'];
            $metrics['todayProfit']       = $today['profit'];
            $metrics['todayTransactions'] = $today['transactions'];

            $purchasesStmt = $this->db->prepare("
                SELECT COALESCE(SUM(poi.order_qty * poi.unit_cost), 0)
                FROM purchase_orders po
                JOIN po_items poi ON po.id = poi.po_id
                WHERE $poDateFieldSql
            ");
            $purchasesStmt->execute($params);
            $metrics['todayPurchases'] = $purchasesStmt->fetchColumn();

            $totalLogsStmt = $this->db->prepare("SELECT COUNT(*) FROM inventory_logs WHERE $logDateFieldSql");
            $totalLogsStmt->execute($params);
            $metrics['totalLogs'] = $totalLogsStmt->fetchColumn();

            $totalAddedStmt = $this->db->prepare("SELECT COUNT(*) FROM inventory_logs WHERE action_type = 'Added' AND $logDateFieldSql");
            $totalAddedStmt->execute($params);
            $metrics['totalAdded'] = $totalAddedStmt->fetchColumn();

            $totalDeletedStmt = $this->db->prepare("SELECT COUNT(*) FROM inventory_logs WHERE action_type = 'Deleted' AND $logDateFieldSql");
            $totalDeletedStmt->execute($params);
            $metrics['totalDeleted'] = $totalDeletedStmt->fetchColumn();

            $topProductsStmt = $this->db->prepare("
                SELECT p.name, COALESCE(SUM(oi.quantity), 0) AS sold
                FROM order_items oi
                INNER JOIN products p ON oi.product_id = p.id
                INNER JOIN orders o ON oi.order_id = o.id
                GROUP BY oi.product_id, p.name
                ORDER BY sold DESC
                LIMIT 5
            ");
            $topProductsStmt->execute();
            $metrics['topProducts'] = $topProductsStmt->fetchAll(PDO::FETCH_ASSOC);

            $metrics['errorMsg'] = null;
        } catch (Exception $e) {
            $metrics['productLogs'] = $metrics['salesLogs'] = $metrics['lowStocks'] = $metrics['newUsers'] = [];
            $metrics['totalProducts'] = $metrics['totalUnits'] = $metrics['totalCategories'] = $metrics['lowStock'] = $metrics['outOfStock'] = 0;
            $metrics['totalRevenue'] = 0.00;
            $metrics['transactions'] = $metrics['itemsSold'] = $metrics['totalLogs'] = $metrics['totalAdded'] = $metrics['totalDeleted'] = 0;
            $metrics['todayRevenue'] = 0;
            $metrics['todayCOGS'] = 0;
            $metrics['todayPurchases'] = 0;
            $metrics['todayProfit'] = 0;
            $metrics['todayTransactions'] = 0;
            $metrics['topProducts'] = [];
            $metrics['errorMsg'] = $e->getMessage();
        }

        return $metrics;
    }

    public function getSalesHistory()
    {
        try {
            $query = 'SELECT
                        o.order_no,
                        u.username AS cashier,
                        o.payment_method AS payment,
                        o.discount_amount AS discount,
                        o.discount_type,
                        o.total_amount AS total,
                        o.cash_received,
                        o.change_amount,
                        o.created_at AS date,
                        GROUP_CONCAT(CONCAT(p.name, " x", oi.quantity) SEPARATOR ", ") AS item
                    FROM orders o
                    LEFT JOIN users u
                        ON o.user_id = u.id
                    LEFT JOIN order_items oi
                        ON o.id = oi.order_id
                    LEFT JOIN products p
                        ON oi.product_id = p.id
                    GROUP BY o.id
                    ORDER BY o.id DESC';

            return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getFullInventoryLogs()
    {
        try {
            $query = "SELECT * FROM inventory_logs ORDER BY id DESC";
            return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getLoginLogs()
    {
        try {
            $query = "
                SELECT *
                FROM login_logs
                ORDER BY login_time DESC
            ";

            return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    public function getTopCashiers()
    {
        $stmt = $this->db->query("
            SELECT
                u.username,
                COUNT(o.id) AS sales
            FROM orders o
            INNER JOIN users u ON o.user_id = u.id
            WHERE MONTH(o.created_at) = MONTH(CURDATE())
            AND YEAR(o.created_at) = YEAR(CURDATE())
            GROUP BY u.id, u.username
            ORDER BY sales DESC
            LIMIT 5
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


