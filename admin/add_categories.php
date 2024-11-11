<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Fetch existing categories using prepared statements
$stmt = $conn->prepare("SELECT id, category_name FROM categories");
$stmt->execute();
$result = $stmt->get_result();
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Sanitize output
$sanitizedCategories = array_map(function($category) {
    return [
        'id' => htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'),
        'category_name' => htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8')
    ];
}, $categories);

// Render the template
echo $twig->render('add_categories.twig', [
    'categories' => $sanitizedCategories,
    'pageTitle' => 'Add Categories'
]);
?>
