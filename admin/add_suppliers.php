<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Fetch existing suppliers
$supplierResult = $conn->query("SELECT supp_id, supp_name FROM suppliers");
$suppliers = [];
while ($row = $supplierResult->fetch_assoc()) {
    $suppliers[] = $row;
}

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('add_suppliers.twig', [
    'suppliers' => $suppliers,
    'pageTitle' => 'Add Suppliers'
]);
