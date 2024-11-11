<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$supplier = [];

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $supp_name = filter_input(INPUT_POST, 'supp_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_SPECIAL_CHARS);

        $stmt = $conn->prepare("UPDATE suppliers SET supp_name = ?, phone_number = ? WHERE supp_id = ?");
        $stmt->bind_param("ssi", $supp_name, $phone_number, $id);

        if ($stmt->execute()) {
            $successMessage = "Supplier " . htmlspecialchars($supp_name, ENT_QUOTES, 'UTF-8') . " updated successfully.";
        } else {
            echo "Error: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
        }

        $stmt->close();
    }
} else {
    if (isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("SELECT supp_name, phone_number FROM suppliers WHERE supp_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $supplier = $result->fetch_assoc();
        $stmt->close();
    }
}

$conn->close();

echo $twig->render('edit_supplier.twig', [
    'successMessage' => htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'),
    'supplier' => array_map('htmlspecialchars', $supplier),
    'id' => $id ?? null,
    'pageTitle' => 'Edit Supplier'
]);
?>
