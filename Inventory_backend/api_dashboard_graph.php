<?php
header('Content-Type: application/json');

require_once '../Database/Database.php';

try {

    $database = new Database();
    $db = $database->getConnection();

    $sql = "
        SELECT
            DATE(created_at) AS day,
            ROUND(COALESCE(SUM(total_amount),0),2) AS revenue,
            ROUND(COALESCE(SUM(cost_of_goods_sold),0),2) AS cost,
            ROUND(COALESCE(SUM(total_amount - cost_of_goods_sold),0),2) AS profit
        FROM orders
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) ASC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $graphData = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $graphData[] = [
            'day'     => $row['day'],
            'revenue' => (float)$row['revenue'],
            'cost'    => (float)$row['cost'],
            'profit'  => (float)$row['profit']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $graphData
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

}