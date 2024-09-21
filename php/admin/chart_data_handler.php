<?php

require_once __DIR__ . '/../../database/db_connection.php';

function getMonthlyOverviewData($pdo) {
    $query = "SELECT 
                    DATE_FORMAT(order_date, '%Y-%m') as month, 
                    COUNT(*) as orders, 
                    SUM(total_amount) as revenue
                  FROM orders 
                  WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    AND status = 'completed'
                  GROUP BY DATE_FORMAT(order_date, '%Y-%m')
                  ORDER BY month";
        
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProfitDistributionData($pdo) {
    $query = "SELECT 
                i.category, 
                SUM(oi.quantity * oi.price) as profit
              FROM order_items oi
              JOIN items i ON oi.item_id = i.item_id
              JOIN orders o ON oi.order_id = o.order_id
              WHERE o.status = 'completed'
              GROUP BY i.category";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTopSellingItems($pdo) {
    $query = "SELECT 
                i.name, 
                SUM(oi.quantity) as total_sold
              FROM order_items oi
              JOIN items i ON oi.item_id = i.item_id
              JOIN orders o ON oi.order_id = o.order_id
              WHERE o.status = 'completed'
              GROUP BY i.item_id
              ORDER BY total_sold DESC
              LIMIT 5";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderStatusDistribution($pdo) {
    $query = "SELECT 
                status, 
                COUNT(*) as count
              FROM orders
              GROUP BY status";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    $monthlyOverview = getMonthlyOverviewData($pdo);
    $profitDistribution = getProfitDistributionData($pdo);
    $topSellingItems = getTopSellingItems($pdo);
    $orderStatusDistribution = getOrderStatusDistribution($pdo);

    header('Content-Type: application/json');
    echo json_encode([
        'monthlyOverview' => $monthlyOverview,
        'profitDistribution' => $profitDistribution,
        'topSellingItems' => $topSellingItems,
        'orderStatusDistribution' => $orderStatusDistribution
    ]);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}