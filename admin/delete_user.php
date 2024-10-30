<!DOCTYPE html>
<html>
<?php

require_once '../includes/session.php';
include '../includes/db.php';


$id = $_GET['id'];

// Retrieve the username before deleting
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/Site.css">
    <form>
        <div class="alert alert-success">
        <?php echo "User $username Deleted successfully."; ?>
        <br><br>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    </div>
</form>
    <?php
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
