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
    setcookie('search', $search, time() + (3600), "/", "", true, true); // Secure and HTTP-only flags
}

// Fetch records with limit for pagination
$sql = "SELECT users.id, users.username, users.password, users.role, users.active, sites.site_name, sites.active AS site_active 
        FROM users 
        LEFT JOIN sites ON users.site_id = sites.site_id 
        WHERE users.username LIKE ? 
        OR users.role LIKE ? 
        OR sites.site_name LIKE ? 
        ORDER BY users.id 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("sssii", $search_param, $search_param, $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Append "(Inactive)" to site_name if the site is inactive
        if ($row['site_active'] == 0) {
            $row['site_name'] .= " (Inactive)";
        }
        $users[] = $row;
    }
}
$stmt->close();

// Pagination logic
$sql = "SELECT COUNT(users.id) AS total 
        FROM users 
        LEFT JOIN sites ON users.site_id = sites.site_id 
        WHERE users.username LIKE ? 
        OR users.role LIKE ? 
        OR sites.site_name LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
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
echo $twig->render('view_users.twig', [
    'search' => $search,
    'users' => $users,
    'page' => $page,
    'total_pages' => $total_pages,
    'pageTitle' => 'View Users'
]);

$conn->close();
?>
