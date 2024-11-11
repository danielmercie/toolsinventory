<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
        $supp_id = filter_input(INPUT_POST, 'supp_id', FILTER_SANITIZE_NUMBER_INT);
        $site_id = filter_input(INPUT_POST, 'site_id', FILTER_SANITIZE_NUMBER_INT);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $dop = filter_input(INPUT_POST, 'dop', FILTER_SANITIZE_SPECIAL_CHARS);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

        // Check if a new image is uploaded
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $target = "../uploads/" . basename($image);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);

            $stmt = $conn->prepare("UPDATE tools SET name = ?, category_id = ?, supp_id = ?, site_id = ?, price = ?, dop = ?, quantity = ?, description = ?, image = ? WHERE id = ?");
            $stmt->bind_param("siiidsissi", $name, $category_id, $supp_id, $site_id, $price, $dop, $quantity, $description, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE tools SET name = ?, category_id = ?, supp_id = ?, site_id = ?, price = ?, dop = ?, quantity = ?, description = ? WHERE id = ?");
            $stmt->bind_param("siiidsisi", $name, $category_id, $supp_id, $site_id, $price, $dop, $quantity, $description, $id);
        }

        if ($stmt->execute()) {
            $successMessage = "Tool " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . " updated successfully.";
        } else {
            echo "Error executing statement: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8') . "<br>";
        }

        $stmt->close();
    } else {
        echo "No tool ID provided.<br>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" || $successMessage) {
    if (isset($_GET['id']) || isset($_POST['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) ?? filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $sql = "SELECT tools.*, categories.category_name, suppliers.supp_name, sites.site_name 
                FROM tools 
                LEFT JOIN categories ON tools.category_id = categories.id 
                LEFT JOIN suppliers ON tools.supp_id = suppliers.supp_id 
                LEFT JOIN sites ON tools.site_id = sites.site_id 
                WHERE tools.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tool = $result->fetch_assoc();
        $stmt->close();

        // Fetch all categories, suppliers, and sites for the dropdowns
        $categories = $conn->query("SELECT id, category_name FROM categories")->fetch_all(MYSQLI_ASSOC);
        $suppliers = $conn->query("SELECT supp_id, supp_name FROM suppliers")->fetch_all(MYSQLI_ASSOC);
        $sites = $conn->query("SELECT site_id, site_name FROM sites")->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "No tool ID provided.<br>";
        exit;
    }
}

$conn->close();

echo $twig->render('edit_tool.twig', [
    'successMessage' => htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'),
    'tool' => array_map('htmlspecialchars', $tool),
    'categories' => array_map(function($category) {
        return [
            'id' => htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'),
            'category_name' => htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8')
        ];
    }, $categories),
    'suppliers' => array_map(function($supplier) {
        return [
            'supp_id' => htmlspecialchars($supplier['supp_id'], ENT_QUOTES, 'UTF-8'),
            'supp_name' => htmlspecialchars($supplier['supp_name'], ENT_QUOTES, 'UTF-8')
        ];
    }, $suppliers),
    'sites' => array_map(function($site) {
        return [
            'site_id' => htmlspecialchars($site['site_id'], ENT_QUOTES, 'UTF-8'),
            'site_name' => htmlspecialchars($site['site_name'], ENT_QUOTES, 'UTF-8')
        ];
    }, $sites),
    'image_required' => empty($tool['image']), // Check if image is already uploaded
    'pageTitle' => 'Edit Tools'
]);
?>
