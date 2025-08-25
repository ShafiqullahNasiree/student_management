<?php
// Student page for viewing and booking exams
require_once 'core/init.php';
require_once 'core/functions.php';

// Check if user is logged in and is not admin
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

$message = '';
$error = '';

// Handle exam booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_exam'])) {
    $exam_id = (int)$_POST['exam_id'];
    
    try {
        // Check if already booked
        $check_stmt = $pdo->prepare("SELECT id FROM exam_bookings WHERE student_id = ? AND exam_id = ?");
        $check_stmt->execute([$_SESSION['user_id'], $exam_id]);
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'You have already booked this exam.';
        } else {
            // Check if student is enrolled in the subject
            $enrollment_check = $pdo->prepare("
                SELECT e.id FROM enrollments e 
                JOIN exams ex ON e.subject_id = ex.subject_id 
                WHERE e.student_id = ? AND ex.id = ?
            ");
            $enrollment_check->execute([$_SESSION['user_id'], $exam_id]);
            
            if ($enrollment_check->rowCount() > 0) {
                // Book the exam
                $book_stmt = $pdo->prepare("INSERT INTO exam_bookings (student_id, exam_id) VALUES (?, ?)");
                if ($book_stmt->execute([$_SESSION['user_id'], $exam_id])) {
                    $message = 'Exam booked successfully!';
                } else {
                    $error = 'Booking failed. Please try again.';
                }
            } else {
                $error = 'Please enroll first.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Booking failed. Please try again.';
    }
}

// Get all exams with subject information
try {
    $stmt = $pdo->query("
        SELECT e.*, s.subject_name 
        FROM exams e 
        JOIN subjects s ON e.subject_id = s.id 
        ORDER BY e.exam_date, e.exam_time
    ");
    $exams = $stmt->fetchAll();
} catch (PDOException $e) {
    $exams = [];
}

// Get booked exams for current student
try {
    $stmt = $pdo->prepare("SELECT exam_id FROM exam_bookings WHERE student_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $booked_exams = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $booked_exams = [];
}

// Get enrolled subjects for current student
try {
    $stmt = $pdo->prepare("SELECT subject_id FROM enrollments WHERE student_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $enrolled_subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $enrolled_subjects = [];
}

$page_title = 'Available Exams';
?>
<?php include 'templates/header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Available Exams</h1>
    <p class="page-subtitle">View and book exams for your enrolled subjects</p>
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
                <th>Exam Name</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($exams)): ?>
                <tr>
                    <td colspan="5" class="text-center">No exams available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($exam['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($exam['exam_date']); ?></td>
                        <td><?php echo htmlspecialchars($exam['exam_time']); ?></td>
                        <td>
                            <?php if (in_array($exam['id'], $booked_exams)): ?>
                                <span class="booked-badge">Booked</span>
                            <?php else: ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="exam_id" value="<?php echo $exam['id']; ?>">
                                    <button type="submit" name="book_exam" class="btn btn-book">Book Exam</button>
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

