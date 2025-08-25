<?php
// Student dashboard - main page after login
require_once 'core/init.php';
require_once 'core/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

$current_user = [
    'fullname' => $_SESSION['fullname'],
    'username' => $_SESSION['username']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Management System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/user-styles.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Welcome, <?php echo htmlspecialchars($current_user['fullname']); ?>!</h1>
            <p class="page-subtitle">Here is your academic overview</p>
        </div>
        
        <div class="dashboard-content">
            <p>All functionality is accessible through the navigation menu above.</p>
        </div>
    </div>
    
    <?php include 'templates/footer.php'; ?>
</body>
</html>


