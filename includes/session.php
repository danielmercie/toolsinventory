<?php
session_start();
include '../includes/db.php';

// Set session timeout duration (e.g., 300 seconds)
$timeout_duration = 3600;

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../index.php");
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
?>
