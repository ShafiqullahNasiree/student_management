<?php
// Authentication check and redirect
if (!isLoggedIn()) {
    $is_admin_area = (strpos($_SERVER['REQUEST_URI'], '/admin') !== false);
    if ($is_admin_area) {
        redirect('login.php');
    } else {
        redirect('/student_management/index.php');
    }
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo isset($page_title) ? $page_title : 'Student Management System'; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo isAdmin() ? '../assets/admin-styles.css' : '/student_management/assets/user-styles.css'; ?>">
    

</head>
<body>
    <nav class="top-nav <?php echo isAdmin() ? 'admin-nav' : 'user-nav'; ?>">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="<?php echo isAdmin() ? 'dashboard.php' : '/student_management/dashboard.php'; ?>">
                    Student Management System
                </a>
            </div>
            
            <ul class="nav-menu">
                <?php if (isAdmin()): ?>
                    <li><a href="dashboard.php" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="manage_users.php" class="<?php echo $current_page === 'manage_users' ? 'active' : ''; ?>">Manage Users</a></li>
                    <li><a href="manage_subjects.php" class="<?php echo $current_page === 'manage_subjects' ? 'active' : ''; ?>">Manage Subjects</a></li>
                    <li><a href="manage_exams.php" class="<?php echo $current_page === 'manage_exams' ? 'active' : ''; ?>">Manage Exams</a></li>
                    <li><a href="manage_meetings.php" class="<?php echo $current_page === 'manage_meetings' ? 'active' : ''; ?>">Manage Meetings</a></li>
                <?php else: ?>
                    <li><a href="/student_management/dashboard.php" class="<?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="/student_management/subjects.php" class="<?php echo $current_page === 'subjects' ? 'active' : ''; ?>">Subjects</a></li>
                    <li><a href="/student_management/exams.php" class="<?php echo $current_page === 'exams' ? 'active' : ''; ?>">Exams</a></li>
                    <li><a href="/student_management/meetings.php" class="<?php echo $current_page === 'meetings' ? 'active' : ''; ?>">Meetings</a></li>
                <?php endif; ?>
                
                <li><a href="<?php echo isAdmin() ? 'logout.php' : '/student_management/logout.php'; ?>" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">
