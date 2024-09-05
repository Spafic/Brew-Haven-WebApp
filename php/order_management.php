<?php
require_once '../php/helpers/sessionConfig.php';
require_once '../database/db_connection.php';

// Function to create a new order
function createOrder($userId, $items, $contactInfo) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, status, contact_info) VALUES (?, NOW(), 'pending', ?)");
        $stmt->execute([$userId, $contactInfo]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }
        
        $pdo->commit();
        return ['success' => true, 'order_id' => $orderId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Function to get order details
function getOrderDetails($orderId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.order_date, o.status, o.contact_info,
               oi.item_id, oi.quantity, oi.price, i.name AS item_name
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN items i ON oi.item_id = i.item_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to update order status
function updateOrderStatus($orderId, $status) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $result = $stmt->execute([$status, $orderId]);
    return ['success' => $result];
}

// Main logic to handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'create_order':
            $userId = $data['user_id'] ?? null;
            $items = $data['items'] ?? [];
            $contactInfo = $data['contact_info'] ?? '';
            echo json_encode(createOrder($userId, $items, $contactInfo));
            break;
        
        case 'get_order_details':
            $orderId = $data['order_id'] ?? null;
            echo json_encode(getOrderDetails($orderId));
            break;
        
        case 'update_order_status':
            $orderId = $data['order_id'] ?? null;
            $status = $data['status'] ?? null;
            echo json_encode(updateOrderStatus($orderId, $status));
            break;
        
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}