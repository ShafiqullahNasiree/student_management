<?php
// Student page for browsing and enrolling in subjects
require_once 'core/init.php';
require_once 'core/functions.php';

// Check if user is logged in and is not admin
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

$message = '';
$error = '';

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $subject_id = (int)$_POST['subject_id'];
    
    try {
        // Check if already enrolled
        $check_stmt = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND subject_id = ?");
        $check_stmt->execute([$_SESSION['user_id'], $subject_id]);
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'You are already enrolled in this subject.';
        } else {
            // Enroll the student
            $enroll_stmt = $pdo->prepare("INSERT INTO enrollments (student_id, subject_id) VALUES (?, ?)");
            if ($enroll_stmt->execute([$_SESSION['user_id'], $subject_id])) {
                $message = 'Enrollment successful! You can now book exams for this subject.';
            } else {
                $error = 'Enrollment failed. Please try again.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Enrollment failed. Please try again.';
    }
}

// Get all subjects
try {
    $stmt = $pdo->query("SELECT * FROM subjects ORDER BY subject_code");
    $subjects = $stmt->fetchAll();
} catch (PDOException $e) {
    $subjects = [];
}

// Get enrolled subjects for current student
try {
    $stmt = $pdo->prepare("SELECT subject_id FROM enrollments WHERE student_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $enrolled_subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $enrolled_subjects = [];
}

$page_title = 'Available Subjects';
?>
<?php include 'templates/header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Available Subjects</h1>
    <p class="page-subtitle">Browse and enroll in subjects</p>
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
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subjects)): ?>
                <tr>
                    <td colspan="4" class="text-center">No subjects available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($subject['description']); ?></td>
                        <td>
                            <?php if (in_array($subject['id'], $enrolled_subjects)): ?>
                                <span class="booked-badge">Enrolled</span>
                            <?php else: ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="subject_id" value="<?php echo $subject['id']; ?>">
                                    <button type="submit" name="enroll" class="btn btn-enroll">Enroll</button>
                                </form>
                            <?php endif; ?>
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

<?php include 'templates/footer.php'; ?>
