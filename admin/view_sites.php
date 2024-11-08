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
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : (isset($_COOKIE['search']) ? htmlspecialchars($_COOKIE['search'], ENT_QUOTES, 'UTF-8') : '');
if (isset($_GET['search'])) {
    setcookie('search', $search, time() + 3600, "/", "", true, true); // Secure and HTTP-only flags
}

// Fetch records with limit for pagination
$sql = "SELECT site_id, site_name, active FROM sites 
        WHERE site_name LIKE ? 
        ORDER BY site_id 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("sii", $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$sites = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $sites[] = $row;
    }
}
$stmt->close();

// Pagination logic
$sql = "SELECT COUNT(site_id) AS total FROM sites WHERE site_name LIKE ? OR active LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $records_per_page);
$stmt->close();

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('view_sites.twig', [
    'search' => $search,
    'sites' => $sites,
    'page' => $page,
    'total_pages' => $total_pages
]);
?>
