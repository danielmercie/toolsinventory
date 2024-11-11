<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$site = [];

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $site_name = filter_input(INPUT_POST, 'site_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $active = isset($_POST['active']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE sites SET site_name = ?, active = ? WHERE site_id = ?");
        $stmt->bind_param("sii", $site_name, $active, $id);

        if ($stmt->execute()) {
            $successMessage = "Site " . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') . " updated successfully.";
        } else {
            echo "Error: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
        }

        $stmt->close();
    }
} else {
    if (isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("SELECT site_name, active FROM sites WHERE site_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $site = $result->fetch_assoc();
        $stmt->close();
    }
}

$conn->close();

echo $twig->render('edit_sites.twig', [
    'successMessage' => htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'),
    'site' => array_map('htmlspecialchars', $site),
    'id' => $id ?? null,
    'pageTitle' => 'Edit Sites'
]);
?>
