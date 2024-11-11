<?php
include_once __DIR__ . '/../includes/db.php';

function getClientIp() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function logActivity($username, $action, $status) {
    global $conn;
    $ip_address = getClientIp();
    $stmt = $conn->prepare("INSERT INTO user_logs (username, action, ip_address, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $action, $ip_address, $status);
    $stmt->execute();
    $stmt->close();
}
?>
