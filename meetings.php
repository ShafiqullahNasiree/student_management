<?php
// Student page for scheduling and viewing meetings
require_once 'core/init.php';
require_once 'core/functions.php';

// Check if user is logged in and is not admin
if (!isLoggedIn() || isAdmin()) {
    redirect('index.php');
}

$message = '';
$error = '';

// Handle meeting booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_meeting'])) {
    $professor_id = (int)$_POST['professor_id'];
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];
    
    if (empty($meeting_date) || empty($meeting_time)) {
        $error = 'Please select both meeting date and time.';
    } else {
        // Combine date and time
        $meeting_datetime = $meeting_date . ' ' . $meeting_time . ':00';
        
        try {
            // Check if time slot is available
            $check_stmt = $pdo->prepare("
                SELECT id FROM meetings 
                WHERE professor_id = ? AND meeting_datetime = ? AND status != 'cancelled'
            ");
            $check_stmt->execute([$professor_id, $meeting_datetime]);
            
            if ($check_stmt->rowCount() > 0) {
                $error = 'This time slot is already booked. Please choose another time.';
            } else {
                // Book the meeting
                $book_stmt = $pdo->prepare("
                    INSERT INTO meetings (student_id, professor_id, meeting_datetime, status) 
                    VALUES (?, ?, ?, 'pending')
                ");
                if ($book_stmt->execute([$_SESSION['user_id'], $professor_id, $meeting_datetime])) {
                    $message = 'Meeting booked successfully! Waiting for professor confirmation.';
                } else {
                    $error = 'Booking failed. Please try again.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Booking failed. Please try again.';
        }
    }
}

// Get all professors
try {
    $stmt = $pdo->query("SELECT id, fullname FROM professors ORDER BY fullname");
    $professors = $stmt->fetchAll();
} catch (PDOException $e) {
    $professors = [];
}

// Get meetings for current student
try {
    $stmt = $pdo->prepare("
        SELECT m.*, p.fullname as professor_name 
        FROM meetings m 
        JOIN professors p ON m.professor_id = p.id 
        WHERE m.student_id = ? 
        ORDER BY m.meeting_datetime DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $meetings = $stmt->fetchAll();
} catch (PDOException $e) {
    $meetings = [];
}

$page_title = 'Meetings';
?>
<?php include 'templates/header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Meetings</h1>
    <p class="page-subtitle">Schedule meetings with professors</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="meetings-container">
    <!-- Book New Meeting Form -->
    <div class="card" style="max-width: 500px; margin: 0 auto; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 1.5rem;">
        <h2 class="card-title" style="font-size: 1.3rem; font-weight: 600; margin-bottom: 1.5rem; text-align: center; color: #333;">Book New Meeting</h2>
        <form method="POST" class="meeting-form">
            <div class="form-group">
                <label for="professor_id" class="form-label" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #333;">Professor</label>
                <select id="professor_id" name="professor_id" class="form-select" required>
                    <option value="">Choose a professor...</option>
                    <?php foreach ($professors as $professor): ?>
                        <option value="<?php echo $professor['id']; ?>">
                            <?php echo htmlspecialchars($professor['fullname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="meeting_date" class="form-label" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #333;">Date</label>
                <input type="date" id="meeting_date" name="meeting_date" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="meeting_time" class="form-label" style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; color: #333;">Time</label>
                <input type="time" id="meeting_time" name="meeting_time" class="form-input" required>
            </div>
            
            <button type="submit" name="book_meeting" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 1.5rem; margin-top: 1rem; width: 100%;">Book Meeting</button>
        </form>
    </div>

    <!-- My Meetings -->
    <div class="card" style="max-width: 100%; margin: 0 auto;">
        <h2 class="card-title">My Meetings</h2>
        <?php if (empty($meetings)): ?>
            <p>You have no scheduled meetings.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($meetings as $meeting): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($meeting['professor_name']); ?></td>
                            <td><?php echo htmlspecialchars($meeting['meeting_datetime']); ?></td>
                            <td>
                                <span class="status-<?php echo $meeting['status']; ?>">
                                    <?php echo ucfirst(htmlspecialchars($meeting['status'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="text-center" style="margin-top: 2rem;">
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<?php include 'templates/footer.php'; ?>
