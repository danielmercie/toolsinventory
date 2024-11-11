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
        $id = $_POST['id'];
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $supp_id = $_POST['supp_id'];
        $site_id = $_POST['site_id'];
        $price = $_POST['price'];
        $dop = $_POST['dop'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'];

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
            $successMessage = "Tool $name updated successfully.";
        } else {
            echo "Error executing statement: " . $stmt->error . "<br>";
        }

        $stmt->close();
    } else {
        echo "No tool ID provided.<br>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" || $successMessage) {
    if (isset($_GET['id']) || isset($_POST['id'])) {
        $id = $_GET['id'] ?? $_POST['id'];
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
    'successMessage' => $successMessage,
    'tool' => $tool,
    'categories' => $categories,
    'suppliers' => $suppliers,
    'sites' => $sites,
    'image_required' => empty($tool['image']), // Check if image is already uploaded
    'pageTitle' => 'Edit Tools'
]);
?>
