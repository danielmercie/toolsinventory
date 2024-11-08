<?php
include_once __DIR__ . '/../includes/db.php';

function getClientIp() {
    $ip = file_get_contents('https://api.ipify.org');
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
