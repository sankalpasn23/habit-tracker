<?php
/**
 * HabitForge — Database Connection
 * 
 * Establishes a connection to the MySQL database using MySQLi.
 * Configure credentials below to match your XAMPP/WAMP setup.
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // Default XAMPP username
define('DB_PASS', '');             // Default XAMPP password (empty)
define('DB_NAME', 'habit_tracker'); // Our database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // In production, log this instead of displaying
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8 for emoji/unicode support
$conn->set_charset("utf8mb4");
?>
