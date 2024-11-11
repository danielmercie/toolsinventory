<?php

require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Fetch sites from the database using prepared statements
$stmt = $conn->prepare("SELECT site_id, site_name, active FROM sites");
$stmt->execute();
$result = $stmt->get_result();
$sites = [];
while ($row = $result->fetch_assoc()) {
    $sites[] = $row;
}
$stmt->close();

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Sanitize output
$sanitizedSites = array_map(function($site) {
    return [
        'site_id' => htmlspecialchars($site['site_id'], ENT_QUOTES, 'UTF-8'),
        'site_name' => htmlspecialchars($site['site_name'], ENT_QUOTES, 'UTF-8'),
        'active' => htmlspecialchars($site['active'], ENT_QUOTES, 'UTF-8')
    ];
}, $sites);

// Render the template
echo $twig->render('add_sites.twig', [
    'sites' => $sanitizedSites,
    'pageTitle' => 'Add Sites'
]);
?>
