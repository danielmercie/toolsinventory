<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// sites dropdown
$siteResult = $conn->query("SELECT site_id, site_name FROM sites");
$sites = [];
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = $row;
}

// categories dropdown
$categoryResult = $conn->query("SELECT id, category_name FROM categories");
$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}

// suppliers dropdown
$supplierResult = $conn->query("SELECT supp_id, supp_name FROM suppliers");
$suppliers = [];
while ($row = $supplierResult->fetch_assoc()) {
    $suppliers[] = $row;
}

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('add_tools.twig', [
    'sites' => $sites,
    'categories' => $categories,
    'suppliers' => $suppliers,
    'pageTitle' => 'Add Tools'
]);