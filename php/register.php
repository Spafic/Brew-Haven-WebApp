<?php
require_once '../php/helpers/sessionConfig.php'; 
require_once '../database/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Initialize error array
    $errors = [];

    // Validate name
    if (!preg_match("/^[a-zA-Z\s]{2,50}$/", $name)) {
        $errors['name'] = "Name must be 2-50 characters long and contain only letters and spaces.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/@.+\.com$/", $email)) {
        $errors['email'] = "Please enter a valid email address ending with .com";
    }

    // Validate phone
    if (!preg_match("/^[0-9]{11}$/", $phone)) {
        $errors['phone'] = "Phone number must be exactly 11 digits.";
    }

    // Validate location
    if (empty($location)) {
        $errors['location'] = "Location is required.";
    }

    // Validate password
    if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?!.*[<>'\"\/;`%])(?!.*\s).{8,}$/", $password)) {
        $errors['password'] = "Password must be at least 8 characters long, include uppercase, lowercase, number, special character, and no spaces.";
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match.";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors['email'] = "Email already exists. Please use a different email.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert user data first to get the user_id
                $stmt = $pdo->prepare("INSERT INTO users (name, password, email, phone_number, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
                $stmt->execute([$name, $hashedPassword, $email, $phone, $location]);
                $userId = $pdo->lastInsertId();

                // Handle profile image upload
                $profileImagePath = 'assets/imgs/default-user.png'; // Default image path
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $uploadedExtension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

                    if (in_array($uploadedExtension, $allowedExtensions)) {
                        $uploadDir = '../assets/imgs/users/' . $userId . '/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Generate special filename
                        $username = preg_replace('/[^a-zA-Z0-9]/', '', $name); // Remove non-alphanumeric characters
                        date_default_timezone_set('Africa/Cairo');
                        $date = date('Y.m.d.H.i');
                        $newFilename = $username . '_' . $date . '.' . $uploadedExtension;
                        $destination = $uploadDir . $newFilename;

                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                            $profileImagePath = 'assets/imgs/users/' . $userId . '/' . $newFilename;

                            // Update the user record with the profile image path
                            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
                            $stmt->execute([$profileImagePath, $userId]);
                        } else {
                            $errors['profile_image'] = "Failed to upload image.";
                        }
                    } else {
                        $errors['profile_image'] = "Invalid file format. Allowed formats: " . implode(', ', $allowedExtensions);
                    }
                }

                if (empty($errors)) {
                    // Set session variables
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['profile_image'] = $profileImagePath;
                    $_SESSION['role'] = 'customer';

                    // Send success response
                    echo json_encode(['success' => true, 'message' => 'Registration successful']);
                    exit;
                }
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors['general'] = 'An error occurred during registration';
        }
    }

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }

    // Close the database connection
    $pdo = null;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>