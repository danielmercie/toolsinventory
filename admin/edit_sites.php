<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$site = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $site_name = $_POST['site_name'];
        $active = isset($_POST['active']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE sites SET site_name = ?, active = ? WHERE site_id = ?");
        $stmt->bind_param("sii", $site_name, $active, $id);

        if ($stmt->execute()) {
            $successMessage = "Site $site_name updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
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
    'successMessage' => $successMessage,
    'site' => $site,
    'id' => $id ?? null,
    'pageTitle' => 'Edit Sites'
]);
?>
