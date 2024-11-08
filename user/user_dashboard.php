<?php
require_once '../vendor/autoload.php';
require_once '../includes/sessionuser.php';
include '../includes/db.php';

// Ensure user is logged in and get user ID
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle the error
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch the user's site_id
$sql = "SELECT site_id FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_site_id = $user['site_id'];

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
        WHERE tools.site_id = ? AND (
            tools.name LIKE ? 
            OR tools.description LIKE ? 
            OR categories.category_name LIKE ? 
            OR suppliers.supp_name LIKE ? 
            OR sites.site_name LIKE ?
        )
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param('issssssi', $user_site_id, $search_param, $search_param, $search_param, $search_param, $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$tools = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tools[] = $row;
    }
}

// Pagination logic
$sql = "SELECT COUNT(id) AS total FROM tools WHERE site_id = ? AND name LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $user_site_id, $search_param);
$stmt->execute();
$result = $stmt->get_result();
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
