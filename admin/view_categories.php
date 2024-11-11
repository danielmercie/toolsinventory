<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';

// Pagination setup
$records_per_page = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$start_from = ($page - 1) * $records_per_page;

// Search setup
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?: (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
if (isset($_GET['search'])) {
    setcookie('search', $search, time() + 3600, "/"); // 3600 = 1 hour
}

// Fetch records with limit for pagination
$sql = "SELECT * FROM categories 
        WHERE category_name LIKE ? 
        OR comment LIKE ? 
        ORDER BY id 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("ssii", $search_param, $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$categories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Pagination logic
$sql = "SELECT COUNT(id) AS total FROM categories WHERE category_name LIKE ? OR comment LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('view_categories.twig', [
    'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
    'categories' => array_map(function($category) {
        return array_map(function($value) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }, $category);
    }, $categories),
    'page' => $page,
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'pageTitle' => 'View Categories'
]);
?>
