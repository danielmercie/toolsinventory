<?php
session_start();
include '../includes/db.php';

// Set session timeout duration (e.g., 5 minutes)
$timeout_duration = 300;

// Ensure session uses secure cookies
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// Regenerate session ID periodically
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} elseif (time() - $_SESSION['CREATED'] > $timeout_duration) {
    // Session started more than $timeout_duration seconds ago
    session_regenerate_id(true);    // Change session ID for the current session and invalidate old session ID
    $_SESSION['CREATED'] = time();  // Update creation time
}

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../includes/sessiontimeout.php");
    exit;
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last request was more than $timeout_duration seconds ago
    session_unset();     // Unset $_SESSION variable for the run-time
    session_destroy();   // Destroy session data in storage
    header("Location: ../includes/sessiontimeout.php");
    exit;
}

// Update last activity time stamp
$_SESSION['LAST_ACTIVITY'] = time();
echo json_encode(['timeout' => false]);
?>
