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
    die("Connection failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

// Fetch sites from the database
$siteResult = $conn->query("SELECT site_id, site_name FROM sites");
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = [
        'site_id' => htmlspecialchars($row['site_id'], ENT_QUOTES, 'UTF-8'),
        'site_name' => htmlspecialchars($row['site_name'], ENT_QUOTES, 'UTF-8')
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = $_POST['password'];
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);
        $site_id = filter_input(INPUT_POST, 'site_id', FILTER_SANITIZE_NUMBER_INT);
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
            $successMessage = "User " . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . " updated successfully.";
        } else {
            echo "Error: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
        }

        $stmt->close();
    } else {
        echo "No user ID provided.";
    }
} else {
    if (isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $sql = "SELECT users.*, sites.site_name FROM users LEFT JOIN sites ON users.site_id = sites.site_id WHERE users.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Sanitize user data
        $user = array_map('htmlspecialchars', $user);
    } else {
        echo "No user ID provided.";
        exit;
    }
}

$conn->close();

echo $twig->render('edit_user.twig', [
    'successMessage' => htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'),
    'user' => $user,
    'sites' => $sites,
    'id' => $id ?? null,
    'pageTitle' => 'Edit Users'
]);
?>
