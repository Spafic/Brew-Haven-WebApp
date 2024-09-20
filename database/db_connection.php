<?php
// Start output buffering to prevent issues with premature output
ob_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_project";

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Clean the output buffer to prevent headers already sent errors
    ob_end_clean();

    // Log error for debugging
    error_log("Database connection error: " . $e->getMessage(), 0);

    $msg = "The website is currently undergoing maintenance. Please try again later.";

    // Redirect to an error page with the message
    echo "<script>location.href = '../pages/server/500.php?msg=" . urlencode($msg) . "';</script>";
    exit;
}

// End output buffering
ob_end_flush();
