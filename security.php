<?php
// Security helpers for Library Management System

// Generate CSRF token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate required fields
function validate_required($fields) {
    foreach ($fields as $field) {
        if (empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

// Check if user is logged in
function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }
}

// Check if user is admin
function require_admin() {
    require_login();
    if ($_SESSION['type'] != 'admin') {
        header("Location: user_dashboard.php");
        exit();
    }
}

// Log security events
function log_security_event($event, $details = '') {
    $log_entry = date('Y-m-d H:i:s') . " - $event - " . $_SERVER['REMOTE_ADDR'] . " - $details\n";
    file_put_contents('security.log', $log_entry, FILE_APPEND);
}

// Rate limiting (simple implementation)
$rate_limit = [];
function check_rate_limit($key, $max_requests = 10, $time_window = 60) {
    global $rate_limit;
    $now = time();

    if (!isset($rate_limit[$key])) {
        $rate_limit[$key] = [];
    }

    // Remove old requests
    $rate_limit[$key] = array_filter($rate_limit[$key], function($timestamp) use ($now, $time_window) {
        return ($now - $timestamp) < $time_window;
    });

    if (count($rate_limit[$key]) >= $max_requests) {
        return false;
    }

    $rate_limit[$key][] = $now;
    return true;
}
?>