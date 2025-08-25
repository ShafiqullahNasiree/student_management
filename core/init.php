<?php
// Database connection and session management initialization
require_once __DIR__ . '/config.php';

// Error reporting configuration
if (DISPLAY_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Session management with tab isolation
if (session_status() === PHP_SESSION_NONE) {
    $is_admin = (strpos($_SERVER['REQUEST_URI'], '/admin') !== false);
    
    if ($is_admin) {
        session_name(ADMIN_SESSION_NAME);
    } else {
        session_name(USER_SESSION_NAME);
    }
    
    session_start();
    
    if (!isset($_SESSION['initialized'])) {
        $_SESSION['initialized'] = true;
    }
}

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch(PDOException $e) {
    die("Database connection failed. Please try again later.");
}
?>

