<?php
require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$category = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT category_name, comment FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $category_name = $_POST['category_name'];
        $comment = $_POST['comment'];

        $stmt = $conn->prepare("UPDATE categories SET category_name = ?, comment = ? WHERE id = ?");
        $stmt->bind_param("ssi", $category_name, $comment, $id);
    
        if ($stmt->execute()) {
            $successMessage = "Category updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    
        $stmt->close();
        $conn->close();
    }
}

echo $twig->render('edit_categories.twig', [
    'successMessage' => $successMessage,
    'category' => $category,
    'id' => $id ?? null,
    'pageTitle' => 'Edit Categories'
]);
?>
