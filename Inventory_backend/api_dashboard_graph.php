<?php
header('Content-Type: application/json');

require_once '../Database/Database.php';

try {

    $database = new Database();
    $db = $database->getConnection();
    $sales = [];

    $salesSql = "
        SELECT
            DATE(created_at) AS day,
            SUM(total_amount) AS revenue,
            SUM(cost_of_goods_sold) AS cost,
            SUM(total_amount - cost_of_goods_sold) AS profit
        FROM orders
        GROUP BY DATE(created_at)
    ";

    $stmt = $db->query($salesSql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sales[$row['day']] = [
            'revenue' => (float)$row['revenue'],
            'cost'    => (float)$row['cost'],
            'profit'  => (float)$row['profit']
        ];
    }

    $purchases = [];

    $purchaseSql = "
        SELECT
            DATE(po.created_at) AS day,
            SUM(pi.order_qty * pi.unit_cost) AS purchases
        FROM purchase_orders po
        INNER JOIN po_items pi
            ON po.id = pi.po_id
        WHERE po.status='Received'
        GROUP BY DATE(po.created_at)
    ";

    $stmt = $db->query($purchaseSql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $purchases[$row['day']] = (float)$row['purchases'];
    }

    $allDates = array_unique(array_merge(
        array_keys($sales),
        array_keys($purchases)
    ));

    sort($allDates);

    $graphData = [];

    foreach ($allDates as $day) {

        $graphData[] = [
            'day' => $day,
            'revenue' => $sales[$day]['revenue'] ?? 0,
            'cost' => $sales[$day]['cost'] ?? 0,
            'profit' => $sales[$day]['profit'] ?? 0,
            'purchases' => $purchases[$day] ?? 0
        ];
    }

    $topProductsSql = "
        SELECT
            p.name,
            SUM(oi.quantity) AS sold
        FROM order_items oi
        INNER JOIN products p
            ON oi.product_id = p.id
        GROUP BY oi.product_id
        ORDER BY sold DESC
        LIMIT 5
    ";

    $stmt = $db->query($topProductsSql);

    $topProducts = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $topProducts[] = [
            'name' => $row['name'],
            'sold' => (int)$row['sold']
        ];
    }
    
    $topCashiersSql = "
        SELECT
            u.username,
            COUNT(o.id) AS sales
        FROM orders o
        INNER JOIN users u
            ON o.user_id = u.id
        WHERE MONTH(o.created_at) = MONTH(CURDATE())
        AND YEAR(o.created_at) = YEAR(CURDATE())
        GROUP BY u.id, u.username
        ORDER BY sales DESC
        LIMIT 5
    ";

    $stmt = $db->query($topCashiersSql);

    $topCashiers = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $topCashiers[] = [
            'username' => $row['username'],
            'sales' => (int)$row['sales']
        ];
    }

    echo json_encode([
        'success' => true,
        'profitGraph' => $graphData,
        'topProducts' => $topProducts,
        'topCashiers' => $topCashiers
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

}



?>