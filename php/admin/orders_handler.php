<?php
require_once __DIR__ . '/../../database/db_connection.php';

function fetchOrders($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT o.*, u.name AS user_name 
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            ORDER BY o.order_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchOrders: " . $e->getMessage());
        return [];
    }
}

function fetchOrderDetails($pdo, $orderId) {
    try {
        $stmt = $pdo->prepare("
            SELECT o.*, u.name AS user_name, u.email AS user_email, u.phone_number AS user_phone
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE o.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT oi.*, i.name AS item_name
            FROM order_items oi
            JOIN items i ON oi.item_id = i.item_id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $order;
    } catch (PDOException $e) {
        error_log("Database error in fetchOrderDetails: " . $e->getMessage());
        return false;
    }
}

function updateOrderStatus($pdo, $orderId, $status) {
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :order_id");
        $stmt->execute([':status' => $status, ':order_id' => $orderId]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Database error in updateOrderStatus: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_orders':
            echo json_encode(fetchOrders($pdo));
            break;
        case 'get_order_details':
            echo json_encode(fetchOrderDetails($pdo, $_GET['id']));
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_order_status') {
        $result = updateOrderStatus($pdo, $_POST['order_id'], $_POST['status']);
        echo json_encode(['success' => $result !== false]);
    }
}