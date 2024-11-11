<!DOCTYPE html>
<html>
<?php

require_once '../includes/session.php';
include '../includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Retrieve the supplier name before deleting
$stmt = $conn->prepare("SELECT supp_name FROM suppliers WHERE supp_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

// Delete the supplier
$stmt = $conn->prepare("DELETE FROM suppliers WHERE supp_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/Site.css">
    <form>
        <div class="alert alert-success">
        <?php echo htmlspecialchars("Supplier $name deleted successfully.", ENT_QUOTES, 'UTF-8'); ?>
        <br><br>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    </div>
</form>
    <?php
} else {
    echo "Error: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
}

$stmt->close();
$conn->close();
?>
</html>
