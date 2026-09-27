<?php
/**
 * HabitForge — Helper Functions
 * 
 * Common utility functions for input sanitization,
 * validation, and session management.
 */

/**
 * Sanitize user input — strips tags and trims whitespace.
 * 
 * @param string $data Raw user input
 * @return string Sanitized string
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email format.
 * 
 * @param string $email Email to validate
 * @return bool True if valid
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if a username meets requirements.
 * Must be 3-30 chars, alphanumeric + underscores only.
 * 
 * @param string $username Username to validate
 * @return bool|string True if valid, error message if not
 */
function validate_username($username) {
    if (strlen($username) < 3) {
        return "Username must be at least 3 characters long.";
    }
    if (strlen($username) > 30) {
        return "Username must be 30 characters or less.";
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return "Username can only contain letters, numbers, and underscores.";
    }
    return true;
}

/**
 * Check if password meets strength requirements.
 * Must be 8+ chars with at least one letter and one number.
 * 
 * @param string $password Password to validate
 * @return bool|string True if valid, error message if not
 */
function validate_password($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[a-zA-Z]/', $password)) {
        return "Password must contain at least one letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number.";
    }
    return true;
}

/**
 * Redirect to a URL with an optional message parameter.
 * 
 * @param string $url Target URL
 * @param string $param Query parameter name
 * @param string $value Query parameter value
 */
function redirect($url, $param = '', $value = '') {
    if ($param && $value) {
        $separator = strpos($url, '?') !== false ? '&' : '?';
        $url .= $separator . urlencode($param) . '=' . urlencode($value);
    }
    header("Location: $url");
    exit();
}

/**
 * Check if the current user is logged in.
 * 
 * @return bool True if logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require the user to be logged in, or redirect to login.
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('../auth/login.php', 'error', 'Please log in to continue.');
    }
}

/**
 * Generate a CSRF token and store it in the session.
 * 
 * @return string The CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a submitted CSRF token matches the session token.
 * 
 * @param string $token The submitted token
 * @return bool True if valid
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Display a formatted date string.
 * 
 * @param string $datetime MySQL datetime string
 * @param string $format PHP date format
 * @return string Formatted date
 */
function format_date($datetime, $format = 'M d, Y') {
    return date($format, strtotime($datetime));
}

/**
 * Get a time-based greeting message.
 * 
 * @return string Greeting (Good morning/afternoon/evening)
 */
function get_greeting() {
    $hour = date('H');
    if ($hour < 12) return 'Good morning';
    if ($hour < 17) return 'Good afternoon';
    return 'Good evening';
}
?>
