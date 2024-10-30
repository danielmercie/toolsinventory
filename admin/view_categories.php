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
    setcookie('search', $search, time() + (3600), "/"); // 86400 = 1 day
}

// Fetch records with limit for pagination
$sql = "SELECT * FROM categories 
        WHERE category_name LIKE '%$search%' 
        OR comment LIKE '%$search%' 
        ORDER BY id 
        LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
$categories = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Pagination logic
$sql = "SELECT COUNT(id) AS total FROM categories WHERE category_name LIKE '%$search%' OR comment LIKE '%$search%'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('view_categories.twig', [
    'search' => $search,
    'categories' => $categories,
    'page' => $page,
    'total_records' => $total_records,
    'total_pages' => $total_pages
]);
