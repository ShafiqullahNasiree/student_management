<?php
// Admin dashboard with simple welcome message
require_once '../core/init.php';
require_once '../core/functions.php';

// Admin authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['account_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$page_title = 'Admin Dashboard';

include '../templates/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Admin Dashboard</h1>
    <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</p>
</div>

<div class="dashboard-content">
    <div class="simple-welcome">
        <p>Use the navigation menu above to access all management functions.</p>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
