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
$sql = "SELECT supp_id, supp_name, phone_number 
        FROM suppliers 
        WHERE supp_name LIKE ? 
        OR phone_number LIKE ? 
        ORDER BY supp_id 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("ssii", $search_param, $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$suppliers = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $suppliers[] = $row;
    }
}
$stmt->close();

// Pagination logic
$sql = "SELECT COUNT(supp_id) AS total FROM suppliers WHERE supp_name LIKE ? OR phone_number LIKE ?";
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
echo $twig->render('view_suppliers.twig', [
    'search' => $search,
    'suppliers' => $suppliers,
    'page' => $page,
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'pageTitle' => 'View Suppliers'
]);
?>