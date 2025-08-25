<?php
// Handle user logout and session cleanup
require_once 'core/init.php';
require_once 'core/functions.php';

if (isLoggedIn()) {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

redirect('/student_management/index.php');
?> 