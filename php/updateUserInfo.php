<?php
require_once '../php/helpers/sessionConfig.php';
require_once '../database/db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Regex patterns for validation
$nameRegex = '/^[a-zA-Z\s]{2,50}$/';
$phoneRegex = '/^[0-9]{11}$/';
$locationRegex = '/^[a-zA-Z0-9\s,.-]{2,100}$/';
$passwordRegex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?!.*[<>\'"\/;`%])(?!.*\s).{8,}$/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'] ?? '';

    try {
        switch ($field) {
            case 'name':
                $name = sanitizeInput($_POST['value']);
                if (!preg_match($nameRegex, $name)) {
                    throw new InvalidArgumentException("Name must be between 2 and 50 characters and contain only letters and spaces.");
                }
                updateUserField($pdo, $user_id, 'name', $name);
                echo json_encode(['name' => $name]);
                break;

            case 'phone':
                $phone = sanitizeInput($_POST['value']);
                if (!preg_match($phoneRegex, $phone)) {
                    throw new InvalidArgumentException("Phone number must be 11 digits.");
                }
                updateUserField($pdo, $user_id, 'phone_number', $phone);
                echo json_encode(['phone' => $phone]);
                break;

            case 'address':
                $address = sanitizeInput($_POST['value']);
                if (!preg_match($locationRegex, $address)) {
                    throw new InvalidArgumentException("Invalid address format. Please use only letters, numbers, spaces, commas, periods, and hyphens.");
                }
                updateUserField($pdo, $user_id, 'address', $address);
                echo json_encode(['address' => $address]);
                break;

            case 'profile_image':
                if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
                    throw new InvalidArgumentException("No file uploaded or upload error occurred.");
                }
                $profilePicPath = uploadProfileImage($pdo, $user_id, $_FILES['profile_image']);
                updateUserField($pdo, $user_id, 'profile_image', $profilePicPath);
                echo json_encode(['profile_image' => $profilePicPath]);
                break;

            case 'password':
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (!preg_match($passwordRegex, $newPassword)) {
                    throw new InvalidArgumentException("Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*). It must not contain any spaces, angle brackets, or other special characters.");
                }

                if ($newPassword !== $confirmPassword) {
                    throw new InvalidArgumentException("New passwords do not match.");
                }

                changePassword($pdo, $user_id, $currentPassword, $newPassword);
                echo json_encode(['message' => 'Password updated successfully']);
                break;

            default:
                throw new InvalidArgumentException('Invalid field.');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function updateUserField($pdo, $user_id, $field, $value) {
    $stmt = $pdo->prepare("UPDATE users SET $field = :value WHERE user_id = :user_id");
    if (!$stmt->execute(['value' => $value, 'user_id' => $user_id])) {
        throw new Exception('Error updating database.');
    }
}

function uploadProfileImage($pdo, $user_id, $file) {
    $stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $username = $stmt->fetchColumn();

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $uploadedExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($uploadedExtension, $allowedExtensions)) {
        throw new InvalidArgumentException("Invalid file format. Allowed formats: " . implode(', ', $allowedExtensions));
    }

    $uploadDir = "../assets/imgs/users/$user_id/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
    date_default_timezone_set('Africa/Cairo');
    $date = date('Y.m.d.H.i');
    $newFilename = "{$username}_{$date}.{$uploadedExtension}";
    $destination = $uploadDir . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to upload image.');
    }

    return "assets/imgs/users/$user_id/$newFilename";
}

function changePassword($pdo, $user_id, $currentPassword, $newPassword) {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $storedPassword = $stmt->fetchColumn();

    if (!password_verify($currentPassword, $storedPassword)) {
        throw new InvalidArgumentException("Current password is incorrect.");
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    updateUserField($pdo, $user_id, 'password', $hashedPassword);
}