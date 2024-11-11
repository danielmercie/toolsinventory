<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';

// Pagination setup
$records_per_page = 50;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$start_from = ($page - 1) * $records_per_page;

// Search setup
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?: (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
if (isset($_GET['search'])) {
    setcookie('search', $search, time() + 3600, "/"); // 3600 = 1 hour
}

// Fetch records with limit for pagination
$sql = "SELECT user_logs.*
        FROM user_logs 
        WHERE user_logs.username LIKE ? 
        OR user_logs.action LIKE ? 
        OR user_logs.ip_address LIKE ? 
        OR user_logs.status LIKE ? 
        ORDER BY timestamp DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("ssssii", $search_param, $search_param, $search_param, $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$logs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}

// Pagination logic
$sql = "SELECT COUNT(user_logs.id) AS total 
        FROM user_logs 
        WHERE user_logs.username LIKE ? 
        OR user_logs.action LIKE ? 
        OR user_logs.ip_address LIKE ? 
        OR user_logs.status LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('view_logs.twig', [
    'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
    'logs' => array_map(function($log) {
        return array_map(function($value) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }, $log);
    }, $logs),
    'page' => $page,
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'pageTitle' => 'View Logs'
]);
?>
