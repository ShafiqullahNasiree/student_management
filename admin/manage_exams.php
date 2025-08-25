<?php
// Exam management (CRUD operations)
require_once '../core/init.php';
require_once '../core/functions.php';

// Check admin access
if (!isAdmin()) {
    redirect('login.php');
}

$message = "";

// Add new exam
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $exam_name = trim($_POST['exam_name']);
    $subject_id = intval($_POST['subject_id']);
    $exam_date = $_POST['exam_date'];
    $exam_time = $_POST['exam_time'];
    
    if (empty($exam_name) || empty($subject_id) || empty($exam_date) || empty($exam_time)) {
        $message = "Error: All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO exams (exam_name, subject_id, exam_date, exam_time) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$exam_name, $subject_id, $exam_date, $exam_time])) {
                $message = "Exam added successfully!";
            } else {
                $message = "Error: Failed to add exam.";
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Update exam
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['edit_id']);
    $exam_name = trim($_POST['edit_exam_name_' . $id]);
    $exam_date = $_POST['edit_exam_date_' . $id];
    $exam_time = $_POST['edit_exam_time_' . $id];
    
    if (empty($exam_name) || empty($exam_date) || empty($exam_time)) {
        $message = "Error: All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE exams SET exam_name = ?, exam_date = ?, exam_time = ? WHERE id = ?");
            if ($stmt->execute([$exam_name, $exam_date, $exam_time, $id])) {
                $message = "Exam updated successfully!";
            } else {
                $message = "Error: Failed to update exam.";
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Delete exam
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['delete_id']);
    try {
    $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = "Exam deleted successfully!";
        } else {
            $message = "Error: Failed to delete exam.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}

// Fetch all subjects for dropdown
try {
    $stmt = $pdo->prepare("SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_code ASC");
    $stmt->execute();
    $subjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $subjects = [];
}

// Fetch all exams with subject information
try {
    $stmt = $pdo->prepare("
        SELECT e.*, s.subject_code, s.subject_name 
        FROM exams e 
        JOIN subjects s ON e.subject_id = s.id 
        ORDER BY e.exam_date ASC, e.exam_time ASC
    ");
    $stmt->execute();
    $exams = $stmt->fetchAll();
} catch (PDOException $e) {
    $exams = [];
    $message = "Error fetching exams: " . $e->getMessage();
}

// Set navigation base for header
$nav_base = '';
$page_title = 'Manage Exams';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin-styles.css">
</head>
<body>
    <!-- Admin Top Navigation (Professional Gray Theme) -->
    <nav class="top-nav admin-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="dashboard.php">Student Management</a>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_subjects.php">Manage Subjects</a></li>
                <li><a href="manage_exams.php" class="active">Manage Exams</a></li>
                <li><a href="manage_meetings.php">Manage Meetings</a></li>
                <li><a href="../logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="main-content">

<div class="page-header">
    <h1 class="page-title">Manage Exams</h1>
    <p class="page-subtitle">Schedule and manage exams for different subjects</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- Add New Exam Form -->
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="section-header">
        <h2>Add New Exam</h2>
    </div>
    <form method="POST" class="form-container">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="exam_name" class="form-label">Exam Name</label>
            <input type="text" id="exam_name" name="exam_name" class="form-input" required maxlength="100">
        </div>
        
        <div class="form-group">
            <label for="subject_id" class="form-label">Subject</label>
            <select id="subject_id" name="subject_id" class="form-select" required>
                <option value="">Select a subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo $subject['id']; ?>">
                        <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="exam_date" class="form-label">Exam Date</label>
            <input type="date" id="exam_date" name="exam_date" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label for="exam_time" class="form-label">Exam Time</label>
            <input type="time" id="exam_time" name="exam_time" class="form-input" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Add Exam</button>
        </div>
    </form>
</div>

<!-- Exams List -->
<div class="card">
    <div class="section-header">
        <h2>Current Exams</h2>
    </div>
    
    <?php if (empty($exams)): ?>
        <p>No exams found.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                            <th>ID</th>
                    <th>Exam Name</th>
                    <th>Subject</th>
                            <th>Date</th>
                            <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                                        <form method="POST">
                        <tr>
                            <td><?php echo htmlspecialchars($exam['id']); ?></td>
                            <td>
                                <input type="text" name="edit_exam_name_<?php echo $exam['id']; ?>" value="<?php echo htmlspecialchars($exam['exam_name']); ?>" class="form-input" style="width: 150px; padding: 0.5rem; margin: 0;">
                            </td>
                            <td><?php echo htmlspecialchars($exam['subject_code'] . ' - ' . $exam['subject_name']); ?></td>
                            <td>
                                <input type="date" name="edit_exam_date_<?php echo $exam['id']; ?>" value="<?php echo htmlspecialchars($exam['exam_date']); ?>" class="form-input" style="width: 120px; padding: 0.5rem; margin: 0;">
                            </td>
                            <td>
                                <input type="time" name="edit_exam_time_<?php echo $exam['id']; ?>" value="<?php echo htmlspecialchars($exam['exam_time']); ?>" class="form-input" style="width: 120px; padding: 0.5rem; margin: 0;">
                            </td>
                            <td>
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="edit_id" value="<?php echo $exam['id']; ?>">
                                <button type="submit" class="btn btn-small">Update</button>
                            </form>
                            <form method="POST" style="display: inline; margin-left: 5px;" onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="delete_id" value="<?php echo $exam['id']; ?>">
                                <button type="submit" class="btn btn-small btn-danger">Delete</button>
                            </form>
                        </tr>
                    </form>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Edit Exam Modal -->
<div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Edit Exam</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id" name="edit_id">
            
            <div class="form-group">
                <label for="edit_exam_name" class="form-label">Exam Name</label>
                <input type="text" id="edit_exam_name" name="edit_exam_name" class="form-input" required maxlength="100">
            </div>
            
            <div class="form-group">
                <label for="edit_subject_id" class="form-label">Subject</label>
                <select id="edit_subject_id" name="edit_subject_id" class="form-select" required>
                    <option value="">Select a subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo $subject['id']; ?>">
                            <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_exam_date" class="form-label">Exam Date</label>
                <input type="date" id="edit_exam_date" name="edit_exam_date" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="edit_exam_time" class="form-label">Exam Time</label>
                <input type="time" id="edit_exam_time" name="edit_exam_time" class="form-input" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Update Exam</button>
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
        </div>

<script>
function editExam(id, examName, subjectId, examDate, examTime) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_exam_name').value = examName;
    document.getElementById('edit_subject_id').value = subjectId;
    document.getElementById('edit_exam_date').value = examDate;
    document.getElementById('edit_exam_time').value = examTime;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

    </main>
</body>
</html>
