<?php
// Admin page to manage user accounts (students and professors)
require_once '../core/init.php';
require_once '../core/functions.php';

// Check if user is logged in and is admin
if (!isAdmin()) {
    redirect('../index.php');
}

$message = '';
$error = '';

// Handle user updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $account_type = $_POST['account_type'];
    
    try {
        $stmt = $pdo->prepare("UPDATE accounts SET username = ?, email = ?, account_type = ? WHERE id = ?");
        if ($stmt->execute([$username, $email, $account_type, $user_id])) {
            $message = 'User updated successfully!';
        } else {
            $error = 'Failed to update user.';
        }
    } catch (PDOException $e) {
        $error = 'Update failed. Please try again.';
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = (int)$_POST['user_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ? AND account_type != 'admin'");
        if ($stmt->execute([$user_id])) {
            $message = 'User deleted successfully!';
        } else {
            $error = 'Failed to delete user.';
        }
    } catch (PDOException $e) {
        $error = 'Deletion failed. Please try again.';
    }
}

// Get all users (excluding professors - they are in separate table)
try {
    $stmt = $pdo->query("SELECT * FROM accounts WHERE account_type IN ('admin', 'user') ORDER BY id ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
}

$page_title = 'Manage Users';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin-styles.css">
</head>
<body>
    <nav class="top-nav admin-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="dashboard.php">Student Management</a>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php" class="active">Manage Users</a></li>
                <li><a href="manage_subjects.php">Manage Subjects</a></li>
                <li><a href="manage_exams.php">Manage Exams</a></li>
                <li><a href="manage_meetings.php">Manage Meetings</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">

<div class="page-header">
    <h1 class="page-title">Manage Users</h1>
    <p class="page-subtitle">Manage student and admin accounts</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-input" style="width: 120px;">
                    </td>
                    <td>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-input" style="width: 200px;">
                    </td>
                    <td>
                            <select name="account_type" class="form-select" style="width: 100px;">
                                <option value="user" <?php echo $user['account_type'] === 'user' ? 'selected' : ''; ?>>Student</option>
                                <option value="admin" <?php echo $user['account_type'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                    </td>
                    <td><?php echo htmlspecialchars($user['join_date']); ?></td>
                    <td>
                        <button type="submit" name="update_user" class="btn btn-secondary btn-small">Update</button>
                        </form>
                        
                        <?php if ($user['account_type'] !== 'admin'): ?>
                            <form method="POST" style="display: inline; margin-left: 5px;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-small" style="display: inline-block !important; visibility: visible !important; opacity: 1 !important;" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="text-center" style="margin-top: 2rem;">
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

    </main>
</body>
</html>
