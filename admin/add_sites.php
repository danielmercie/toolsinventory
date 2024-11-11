<?php

require_once '../vendor/autoload.php';
require_once '../includes/session.php';
include '../includes/db.php';

// Fetch sites from the database
$siteResult = $conn->query("SELECT site_id, site_name, active FROM sites");
$sites = [];
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = $row;
}

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

// Render the template
echo $twig->render('add_sites.twig', [
    'sites' => $sites,
    'pageTitle' => 'Add Sites'
]);
