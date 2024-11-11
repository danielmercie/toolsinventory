<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$successMessage = "";
$supplier = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $supp_name = $_POST['supp_name'];
        $phone_number = $_POST['phone_number'];

        $stmt = $conn->prepare("UPDATE suppliers SET supp_name = ?, phone_number = ? WHERE supp_id = ?");
        $stmt->bind_param("ssi", $supp_name, $phone_number, $id);

        if ($stmt->execute()) {
            $successMessage = "Supplier $supp_name updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
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
    'successMessage' => $successMessage,
    'supplier' => $supplier,
    'id' => $id ?? null,
    'pageTitle' => 'Edit Supplier'
]);
?>
