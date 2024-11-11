<?php
require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$category = [];

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("SELECT category_name, comment FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);

        $stmt = $conn->prepare("UPDATE categories SET category_name = ?, comment = ? WHERE id = ?");
        $stmt->bind_param("ssi", $category_name, $comment, $id);
    
        if ($stmt->execute()) {
            $successMessage = "Category updated successfully.";
        } else {
            echo "Error: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
        }
    
        $stmt->close();
        $conn->close();
    }
}

echo $twig->render('edit_categories.twig', [
    'successMessage' => htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'),
    'category' => array_map('htmlspecialchars', $category),
    'id' => $id ?? null,
    'pageTitle' => 'Edit Categories'
]);
?>
