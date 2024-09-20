<?php
require_once __DIR__ . '/../../database/db_connection.php';

function fetchUsers($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT user_id, name, email, phone_number, role, address FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchUsers: " . $e->getMessage());
        return [];
    }
}

function getUser($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT user_id, name, email, phone_number, role, address FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getUser: " . $e->getMessage());
        return false;
    }
}

function updateUser($pdo, $userId, $name, $email, $phone, $role, $address) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, phone_number = :phone, role = :role, address = :address WHERE user_id = :id");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':role' => $role,
            ':address' => $address,
            ':id' => $userId
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Database error in updateUser: " . $e->getMessage());
        return false;
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_users':
            echo json_encode(fetchUsers($pdo));
            break;
        case 'get_user':
            echo json_encode(getUser($pdo, $_GET['id']));
            break;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_user') {
        $result = updateUser($pdo, $_POST['user_id'], $_POST['name'], $_POST['email'], $_POST['phone_number'], $_POST['role'], $_POST['address']);
        echo json_encode(['success' => $result]);
    }
}