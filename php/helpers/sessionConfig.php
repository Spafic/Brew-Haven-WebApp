<?php
// Start output buffering to prevent any premature output
ob_start();

// Ensure no output before this point
session_name('Cafe_Access'); // Avoid special characters in the session name

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => 24 * 60 * 60,
    'path' => '/',
    'domain' => '', // Set domain to your domain if needed, otherwise leave it empty for localhost
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax' // Prevent CSRF
]);

// Start the session and check for errors
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID immediately after starting the session
session_regenerate_id(true);

// Check if the session has expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
    session_unset();
    session_destroy();
    /* Redirect */
    header('Location: ../pages/');
    exit(); // Ensure script stops executing after redirection
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Set the initialized flag if it's not set
if (!isset($_SESSION['initialized'])) {
    $_SESSION['initialized'] = true;
}

// End output buffering and flush output
ob_end_flush();
?>
