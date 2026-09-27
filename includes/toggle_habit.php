<?php
/**
 * HabitForge — Toggle Habit Completion (AJAX)
 * 
 * Handles toggling a habit as completed/uncompleted for today.
 * Updates streaks accordingly. Returns JSON response.
 */

session_start();
require_once 'db.php';
require_once 'functions.php';

// Set JSON response header
header('Content-Type: application/json');

// Must be logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$habitId = intval($input['habit_id'] ?? 0);
$completed = $input['completed'] ?? false;
$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

if ($habitId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid habit ID']);
    exit();
}

// Verify the habit belongs to this user
$stmt = $conn->prepare("SELECT id, current_streak FROM habits WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $habitId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$habit = $result->fetch_assoc();
$stmt->close();

if (!$habit) {
    echo json_encode(['success' => false, 'error' => 'Habit not found']);
    exit();
}

$currentStreak = $habit['current_streak'];

if ($completed) {
    // Check if already logged today
    $stmt = $conn->prepare("SELECT id FROM habit_logs WHERE habit_id = ? AND user_id = ? AND log_date = ?");
    $stmt->bind_param("iis", $habitId, $userId, $today);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$exists) {
        // Insert completion log
        $stmt = $conn->prepare("INSERT INTO habit_logs (habit_id, user_id, log_date, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $habitId, $userId, $today);
        $stmt->execute();
        $stmt->close();

        // Update streak: check if yesterday was completed
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $stmt = $conn->prepare("SELECT id FROM habit_logs WHERE habit_id = ? AND user_id = ? AND log_date = ?");
        $stmt->bind_param("iis", $habitId, $userId, $yesterday);
        $stmt->execute();
        $hadYesterday = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        if ($hadYesterday) {
            $currentStreak += 1;
        } else {
            $currentStreak = 1; // Reset streak
        }

        // Update streak in habits table
        $stmt = $conn->prepare("UPDATE habits SET current_streak = ? WHERE id = ?");
        $stmt->bind_param("ii", $currentStreak, $habitId);
        $stmt->execute();
        $stmt->close();
    }
} else {
    // Remove today's completion log
    $stmt = $conn->prepare("DELETE FROM habit_logs WHERE habit_id = ? AND user_id = ? AND log_date = ?");
    $stmt->bind_param("iis", $habitId, $userId, $today);
    $stmt->execute();
    $stmt->close();

    // Decrease streak (minimum 0)
    $currentStreak = max(0, $currentStreak - 1);
    $stmt = $conn->prepare("UPDATE habits SET current_streak = ? WHERE id = ?");
    $stmt->bind_param("ii", $currentStreak, $habitId);
    $stmt->execute();
    $stmt->close();
}

echo json_encode([
    'success' => true,
    'completed' => $completed,
    'streak' => $currentStreak
]);

$conn->close();
?>
