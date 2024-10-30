<?php
session_start();
include '../includes/db.php';

// Set session timeout duration (e.g., 60 Seconds )
$timeout_duration = 60;

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
