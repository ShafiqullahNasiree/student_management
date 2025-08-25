<?php
// Authentication and utility functions

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Check if current user is admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['account_type'] === 'admin';
}

// Redirect to specified URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>

