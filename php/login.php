<?php
require_once '../php/helpers/sessionConfig.php'; 
require_once '../database/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrUsername = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (empty($emailOrUsername)) {
        $errors['email'] = 'Email or username is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        try {
            // Check if the user exists
            $stmt = $pdo->prepare("SELECT user_id, name, password, profile_image, email, role FROM users WHERE email = ? OR name = ?");
            $stmt->execute([$emailOrUsername, $emailOrUsername]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['role'] = $user['role'];

                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => '../pages/dashboard.php'
                ]);
                exit;
            } else {
                $errors['general'] = 'Invalid email/username or password';
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $errors['general'] = 'An error occurred during login. Please try again later.';
        }
    }

    // Return errors if any
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }

    // Close the database connection
    $pdo = null;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>