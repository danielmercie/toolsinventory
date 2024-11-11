<?php
require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Fetch existing suppliers using prepared statements
$stmt = $conn->prepare("SELECT supp_id, supp_name FROM suppliers");
$stmt->execute();
$result = $stmt->get_result();
$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}
$stmt->close();

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Sanitize output
$sanitizedSuppliers = array_map(function($supplier) {
    return [
        'supp_id' => htmlspecialchars($supplier['supp_id'], ENT_QUOTES, 'UTF-8'),
        'supp_name' => htmlspecialchars($supplier['supp_name'], ENT_QUOTES, 'UTF-8')
    ];
}, $suppliers);

// Render the template
echo $twig->render('add_suppliers.twig', [
    'suppliers' => $sanitizedSuppliers,
    'pageTitle' => 'Add Suppliers'
]);
?>
