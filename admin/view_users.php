<?php

require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Search setup
$search = isset($_GET['search']) ? $_GET['search'] : (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
if (isset($_GET['search'])) {
    setcookie('search', $search, time() + (3600), "/");
}

// Fetch records with limit for pagination
$sql = "SELECT users.id, users.username, users.password, users.role, sites.site_name 
        FROM users 
        LEFT JOIN sites ON users.site_id = sites.site_id 
        WHERE users.username LIKE '%$search%'
        OR users.role LIKE '%$search%'
        OR sites.site_name LIKE '%$search%'
        ORDER BY users.id 
        LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Pagination logic
$sql = "SELECT COUNT(id) AS total FROM users WHERE username LIKE '%$search%' OR role LIKE '%$search%' OR site_id IN (SELECT site_id FROM sites WHERE site_name LIKE '%$search%')";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('view_users.twig', [
    'search' => $search,
    'users' => $users,
    'page' => $page,
    'total_pages' => $total_pages
]);
