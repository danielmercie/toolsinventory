<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$sites = [];
$user = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sites from the database
$siteResult = $conn->query("SELECT site_id, site_name FROM sites");
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $site_id = $_POST['site_id'];
        $active = isset($_POST['active']) ? 1 : 0;

        // Fetch the current password if the new password is not provided
        if (empty($password)) {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($current_password);
            $stmt->fetch();
            $stmt->close();
        } else {
            $current_password = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, site_id = ?, active = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $username, $current_password, $role, $site_id, $active, $id);

        if ($stmt->execute()) {
            $successMessage = "User $username updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();



    } else {
        echo "No user ID provided.";
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT users.*, sites.site_name FROM users LEFT JOIN sites ON users.site_id = sites.site_id WHERE users.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "No user ID provided.";
        exit;
    }
}

$conn->close();

echo $twig->render('edit_user.twig', [
    'successMessage' => $successMessage,
    'user' => $user,
    'sites' => $sites,
    'id' => $id ?? null,
    'pageTitle' => 'Edit Users'
]);
?>
