<?php
// Admin page to manage all meetings and appointments
require_once '../core/init.php';
require_once '../core/functions.php';

// Check if user is logged in and is admin
if (!isAdmin()) {
    redirect('login.php');
}

$message = '';
$error = '';

// Handle meeting status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status']) && isset($_POST['new_status'])) {
    $meeting_id = (int)$_POST['meeting_id'];
    $new_status = $_POST['new_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE meetings SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $meeting_id])) {
            $message = 'Meeting status updated successfully!';
        } else {
            $error = 'Failed to update meeting status.';
        }
    } catch (PDOException $e) {
        $error = 'Update failed. Please try again.';
    }
}

// Handle meeting deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_meeting'])) {
    $meeting_id = (int)$_POST['meeting_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM meetings WHERE id = ?");
        if ($stmt->execute([$meeting_id])) {
            $message = 'Meeting deleted successfully!';
        } else {
            $error = 'Failed to delete meeting.';
        }
    } catch (PDOException $e) {
        $error = 'Deletion failed. Please try again.';
    }
}

// Get all meetings with student and professor information
try {
    $stmt = $pdo->prepare("
        SELECT m.id, m.meeting_datetime, m.status,
               s.fullname as student_name,
               p.fullname as professor_name
        FROM meetings m
        JOIN accounts s ON m.student_id = s.id
        JOIN professors p ON m.professor_id = p.id
        ORDER BY m.meeting_datetime DESC
    ");
    $stmt->execute();
    $meetings = $stmt->fetchAll();
} catch (PDOException $e) {
    $meetings = [];
}

$page_title = 'Manage Meetings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meetings - Student Management System</title>
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
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_subjects.php">Manage Subjects</a></li>
                <li><a href="manage_exams.php">Manage Exams</a></li>
                <li><a href="manage_meetings.php" class="active">Manage Meetings</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">

<div class="page-header">
    <h1 class="page-title">Manage Meetings</h1>
    <p class="page-subtitle">View and manage all student meetings</p>
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
                <th>Student</th>
                <th>Professor</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($meetings)): ?>
                <tr>
                    <td colspan="5" class="text-center">No meetings found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($meetings as $meeting): ?>
                    <tr>
                        <td style="font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($meeting['student_name']); ?></td>
                        <td style="font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($meeting['professor_name']); ?></td>
                        <td style="font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($meeting['meeting_datetime']); ?></td>
                        <td>
                            <span class="status-<?php echo htmlspecialchars($meeting['status'] ?? 'pending'); ?>" style="font-size: 0.9rem; font-weight: 500;">
                                <?php echo ucfirst(htmlspecialchars($meeting['status'] ?? 'pending')); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="meeting_id" value="<?php echo $meeting['id']; ?>">
                                    <select name="new_status" class="form-select" style="width: 100px; font-size: 0.9rem; padding: 0.6rem;">
                                        <option value="pending" <?php echo ($meeting['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo ($meeting['status'] ?? 'pending') === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo ($meeting['status'] ?? 'pending') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    
                                    <button type="submit" name="update_status" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.6rem 1rem; font-weight: 500;">Update</button>
                                </form>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="meeting_id" value="<?php echo $meeting['id']; ?>">
                                    <button type="submit" name="delete_meeting" class="btn btn-danger" style="font-size: 0.9rem; padding: 0.6rem 1rem; font-weight: 500;" onclick="return confirm('Delete meeting?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="text-center" style="margin-top: 2rem;">
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

    </main>
</body>
</html>

