<?php
/**
 * HabitForge — Contact Form Handler
 * 
 * Processes contact form submissions and stores messages
 * in the database. Redirects back to index with status.
 */

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#contact');
    exit();
}

$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? 'No Subject');
$message = sanitize_input($_POST['message'] ?? '');

// Server-side validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required.";
}

if (!is_valid_email($email)) {
    $errors[] = "A valid email address is required.";
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = "Message must be at least 10 characters long.";
}

if (!empty($errors)) {
    $errorStr = implode(' ', $errors);
    header('Location: index.php?error=' . urlencode($errorStr) . '#contact');
    exit();
}

// Store in database
$stmt = $conn->prepare(
    "INSERT INTO messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())"
);

if ($stmt) {
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        header('Location: index.php?success=1#contact');
    } else {
        header('Location: index.php?error=' . urlencode('Failed to send message. Please try again.') . '#contact');
    }
    
    $stmt->close();
} else {
    header('Location: index.php?error=' . urlencode('Database error. Please try again later.') . '#contact');
}

$conn->close();
?>
