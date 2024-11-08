<?php
include '../includes/db.php';

$search = $_GET['search'] ?? '';

if ($search !== '') {
    $stmt = $conn->prepare("SELECT site_name FROM sites WHERE site_name LIKE ?");
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $suggestions = [];

    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['site_name'];
    }

    echo json_encode($suggestions);
}

$stmt->close();
$conn->close();
?>
