<?php
/**
 * HabitForge — Add Habit Handler
 * 
 * Processes the add habit form submission.
 * Inserts a new habit into the database for the logged-in user.
 */

session_start();
require_once 'db.php';
require_once 'functions.php';

// Must be logged in
if (!is_logged_in()) {
    redirect('../auth/login.php', 'error', 'Please log in first.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../dashboard.php');
}

$userId = $_SESSION['user_id'];
$name = sanitize_input($_POST['name'] ?? '');
$category = sanitize_input($_POST['category'] ?? '');
$emoji = $_POST['emoji'] ?? '✨'; // Emojis don't need HTML sanitization
$frequency = sanitize_input($_POST['frequency'] ?? 'daily');
$goal = sanitize_input($_POST['goal'] ?? '');

// Validate required fields
if (empty($name) || empty($category)) {
    redirect('../dashboard.php', 'error', 'Habit name and category are required.');
}

// Insert into database
$stmt = $conn->prepare(
    "INSERT INTO habits (user_id, name, category, emoji, frequency, goal, current_streak, created_at) 
     VALUES (?, ?, ?, ?, ?, ?, 0, NOW())"
);

if ($stmt) {
    $stmt->bind_param("isssss", $userId, $name, $category, $emoji, $frequency, $goal);
    
    if ($stmt->execute()) {
        redirect('../dashboard.php?habit_added=1');
    } else {
        redirect('../dashboard.php', 'error', 'Failed to add habit. Please try again.');
    }
    
    $stmt->close();
} else {
    redirect('../dashboard.php', 'error', 'Database error. Please try again.');
}

$conn->close();
?>
