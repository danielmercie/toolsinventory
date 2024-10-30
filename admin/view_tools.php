<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Search setup
$search = isset($_GET['search']) ? $_GET['search'] : (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
if (isset($_GET['search'])) {
    setcookie('search', $search, time() + (3600), "/"); // 3600 = 1 hour
}

// Fetch records with limit for pagination
$sql = "SELECT tools.id, tools.name, categories.category_name, suppliers.supp_name, sites.site_name, tools.price, tools.dop, tools.quantity, tools.image, tools.description 
        FROM tools 
        LEFT JOIN categories ON tools.category_id = categories.id 
        LEFT JOIN suppliers ON tools.supp_id = suppliers.supp_id 
        LEFT JOIN sites ON tools.site_id = sites.site_id 
        WHERE tools.name LIKE '%$search%'
        OR tools.description LIKE '%$search%'
        OR categories.category_name LIKE '%$search%'
        OR suppliers.supp_name LIKE '%$search%'
        OR sites.site_name LIKE '%$search%'
        LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);
$tools = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tools[] = $row;
    }
}

// Pagination logic
$sql = "SELECT COUNT(id) AS total FROM tools WHERE name LIKE '%$search%'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('view_tools.twig', [
    'search' => $search,
    'tools' => $tools,
    'page' => $page,
    'total_records' => $total_records,
    'total_pages' => $total_pages
]);
