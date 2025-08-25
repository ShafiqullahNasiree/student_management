<?php
// Subject management (CRUD operations)
require_once '../core/init.php';
require_once '../core/functions.php';

// Check admin access
if (!isAdmin()) {
    redirect('login.php');
}

$message = "";

// Add new subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $description = trim($_POST['description']);
    
    if (empty($subject_code) || empty($subject_name)) {
        $message = "Error: Subject code and name are required.";
    } else {
        try {
            // Check if subject code already exists
            $check_stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ?");
            $check_stmt->execute([$subject_code]);
            
            if ($check_stmt->rowCount() > 0) {
                $message = "Error: Subject code already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, subject_name, description) VALUES (?, ?, ?)");
                if ($stmt->execute([$subject_code, $subject_name, $description])) {
                    $message = "Subject added successfully!";
                } else {
                    $message = "Error: Failed to add subject.";
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Update subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['edit_id']);
    $subject_code = trim($_POST['edit_subject_code_' . $id]);
    $subject_name = trim($_POST['edit_subject_name_' . $id]);
    $description = trim($_POST['edit_description_' . $id]);
    
    if (empty($subject_code) || empty($subject_name)) {
        $message = "Error: Subject code and name are required.";
    } else {
        try {
            // Check if subject code already exists for another subject
            $check_stmt = $pdo->prepare("SELECT id FROM subjects WHERE subject_code = ? AND id != ?");
            $check_stmt->execute([$subject_code, $id]);
            if ($check_stmt->rowCount() > 0) {
                $message = "Error: Subject code already exists.";
            } else {
                $stmt = $pdo->prepare("UPDATE subjects SET subject_code = ?, subject_name = ?, description = ? WHERE id = ?");
                if ($stmt->execute([$subject_code, $subject_name, $description, $id])) {
                    $message = "Subject updated successfully!";
                } else {
                    $message = "Error: Failed to update subject.";
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Delete subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['delete_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "Subject deleted successfully!";
        } else {
            $message = "Error: Failed to delete subject.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}

// Fetch all subjects
try {
    $stmt = $pdo->prepare("SELECT * FROM subjects ORDER BY subject_code ASC");
    $stmt->execute();
    $subjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $subjects = [];
    $message = "Error fetching subjects: " . $e->getMessage();
}

$page_title = 'Manage Subjects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin-styles.css">

</head>
<body>
    <!-- Admin Side Navigation (Gray Theme) -->
    <nav class="top-nav admin-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="dashboard.php">Student Management</a>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_subjects.php" class="active">Manage Subjects</a></li>
                <li><a href="manage_exams.php">Manage Exams</a></li>
                <li><a href="manage_meetings.php">Manage Meetings</a></li>
                <li><a href="../logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">

<div class="page-header">
    <h1 class="page-title">Manage Subjects</h1>
    <p class="page-subtitle">Add, edit, and delete subjects</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- Add New Subject Form -->
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="section-header">
        <h2>Add New Subject</h2>
    </div>
    <form method="POST" class="form-container">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="subject_code" class="form-label">Subject Code</label>
            <input type="text" id="subject_code" name="subject_code" class="form-input" required maxlength="10">
        </div>
        
        <div class="form-group">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" id="subject_name" name="subject_name" class="form-input" required maxlength="100">
        </div>
        
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-textarea" rows="3"></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Subject</button>
        </div>
    </form>
</div>

<!-- Subjects List -->
<div class="card">
    <div class="section-header">
        <h2>Current Subjects</h2>
    </div>
    
    <?php if (empty($subjects)): ?>
        <p>No subjects found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <form method="POST">
                        <tr>
                            <td><?php echo htmlspecialchars($subject['id']); ?></td>
                            <td>
                                <input type="text" name="edit_subject_code_<?php echo $subject['id']; ?>" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" class="form-input" style="width: 80px; padding: 0.5rem; margin: 0;">
                            </td>
                            <td>
                                <input type="text" name="edit_subject_name_<?php echo $subject['id']; ?>" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" class="form-input" style="width: 150px; padding: 0.5rem; margin: 0;">
                            </td>
                            <td>
                                <input type="text" name="edit_description_<?php echo $subject['id']; ?>" value="<?php echo htmlspecialchars($subject['description']); ?>" class="form-input" style="width: 200px; padding: 0.5rem; margin: 0;">
                            </td>
                            <td><?php echo htmlspecialchars($subject['created_at']); ?></td>
                            <td>
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="edit_id" value="<?php echo $subject['id']; ?>">
                                <button type="submit" class="btn btn-small">Update</button>
                            </form>
                            <form method="POST" style="display: inline; margin-left: 5px;" onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="delete_id" value="<?php echo $subject['id']; ?>">
                                <button type="submit" class="btn btn-small btn-danger" style="display: inline-block !important; visibility: visible !important; opacity: 1 !important;">Delete</button>
                            </form>
                            </td>
                        </tr>
                    </form>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>



    </main>
</body>
</html>

