<?php
require_once __DIR__ . '/../../database/db_connection.php';

function fetchStats($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT 
            (SELECT COUNT(*) FROM users) AS users, 
            (SELECT COUNT(*) FROM items) AS items, 
            (SELECT COUNT(*) FROM orders WHERE status='completed') AS orders, 
            (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status='completed') AS orders_profit");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchStats: " . $e->getMessage());
        return ['users' => 0, 'items' => 0, 'orders' => 0, 'orders_profit' => 0];
    }
}