<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Search setup
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : (isset($_COOKIE['search']) ? htmlspecialchars($_COOKIE['search'], ENT_QUOTES, 'UTF-8') : '');
if (isset($_GET['search'])) {
    setcookie('search', $search, time() + 3600, "/", "", true, true); // Secure and HTTP-only flags
}

// Fetch the ID of "Stores Head Office"
$head_office_store_id = null;
$stmt = $conn->prepare("SELECT site_id FROM sites WHERE site_name = ?");
$stmt->bind_param("s", $head_office_store_name);
$head_office_store_name = 'Stores Head Office';
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $head_office_store_id = $row['site_id'];
}
$stmt->close();

// Update site_id for all inactive sites
if ($head_office_store_id !== null) {
    $update_sql = "UPDATE tools 
                   LEFT JOIN sites ON tools.site_id = sites.site_id 
                   SET tools.site_id = ? 
                   WHERE sites.active = 0";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $head_office_store_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch records with limit for pagination
$sql = "SELECT tools.id, tools.name, categories.category_name, suppliers.supp_name, sites.site_id, sites.site_name, sites.active, tools.price, tools.dop, tools.quantity, tools.image, tools.description 
        FROM tools 
        LEFT JOIN categories ON tools.category_id = categories.id 
        LEFT JOIN suppliers ON tools.supp_id = suppliers.supp_id 
        LEFT JOIN sites ON tools.site_id = sites.site_id 
        WHERE tools.name LIKE ? 
        OR tools.description LIKE ? 
        OR categories.category_name LIKE ? 
        OR suppliers.supp_name LIKE ? 
        OR sites.site_name LIKE ? 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("ssssiii", $search_param, $search_param, $search_param, $search_param, $search_param, $start_from, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
$tools = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Update the site_name to "Head Office Store" if the site_id was changed
        if ($row['site_id'] == $head_office_store_id) {
            $row['site_name'] = 'Stores Head Office';
        }
        $tools[] = $row;
    }
}
$stmt->close();

// Pagination logic
$sql = "SELECT COUNT(id) AS total FROM tools WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search_param);
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
echo $twig->render('view_tools.twig', [
    'search' => $search,
    'tools' => $tools,
    'page' => $page,
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'pageTitle' => 'View Tools'
]);
?>
