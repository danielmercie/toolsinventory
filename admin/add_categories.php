<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Fetch existing categories
$categoryResult = $conn->query("SELECT id, category_name FROM categories");
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('add_categories.twig', [
    'categories' => $categories,
    'pageTitle' => 'Add Categories'
]);
