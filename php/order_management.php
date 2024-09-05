<?php
// File: order_management.php

require_once '../php/helpers/sessionConfig.php';
require_once '../database/db_connection.php';

// Function to create a new order
function createOrder($userId, $items) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get user's contact information
        $stmt = $pdo->prepare("SELECT phone_number, address FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        $contactInfo = $userInfo['phone_number'] . ' - ' . $userInfo['address'];
        
        // Calculate total amount
        $totalAmount = array_reduce($items, function($carry, $item) {
            return $carry + ($item['quantity'] * $item['price']);
        }, 0);
        
        // Insert into orders table
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, status, contact_info, total_amount) VALUES (?, NOW(), 'pending', ?, ?)");
        $stmt->execute([$userId, $contactInfo, $totalAmount]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            
            // Update item inventory (assuming you have an inventory table)
            $updateInventory = $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE item_id = ?");
            $updateInventory->execute([$item['quantity'], $item['id']]);
        }
        
        $pdo->commit();
        return ['success' => true, 'order_id' => $orderId];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function getOrderDetails($orderId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.order_date, o.status, o.contact_info, o.total_amount,
               oi.item_id, oi.quantity, oi.price, i.name AS item_name
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN items i ON oi.item_id = i.item_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        return null;
    }
    
    $orderDetails = [
        'order_id' => $items[0]['order_id'],
        'order_date' => $items[0]['order_date'],
        'status' => $items[0]['status'],
        'contact_info' => $items[0]['contact_info'],
        'total_amount' => $items[0]['total_amount'],
        'items' => []
    ];

    foreach ($items as $item) {
        $itemTotal = $item['quantity'] * $item['price'];
        $orderDetails['items'][] = [
            'item_id' => $item['item_id'],
            'item_name' => $item['item_name'],
            'quantity' => $item['quantity'],
            'price' => $item['price'],
            'item_total' => $itemTotal
        ];
    }
    
    return $orderDetails;
}

// Function to get user's order history
function getUserOrderHistory($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT order_id, order_date, status, contact_info, total_amount
        FROM orders
        WHERE user_id = ?
        ORDER BY order_date DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Main logic to handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'create_order':
            $userId = $_SESSION['user_id'] ?? null;
            $items = $data['items'] ?? [];
            echo json_encode(createOrder($userId, $items));
            break;
        
        case 'get_order_details':
            $orderId = $data['order_id'] ?? null;
            echo json_encode(getOrderDetails($orderId));
            break;
        
        case 'get_user_order_history':
            $userId = $_SESSION['user_id'] ?? null;
            echo json_encode(getUserOrderHistory($userId));
            break;
        
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}