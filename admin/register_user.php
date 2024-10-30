<!DOCTYPE html>
<html>
<?php

include '../includes/db.php';
require_once '../includes/session.php';
require_once '../logging/logactivity.php';

// Fetch sites from the database
$sites = [];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$siteResult = $conn->query("SELECT site_id, site_name FROM sites");
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $site_id = $_POST['site_id'];

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        ?>
         <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
            <form>
            <div class="alert alert-success">
                <?php echo "User $username already exists."; ?>
                <br><br>
                <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
            </div>
            </form>
        
        <?php   
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement with placeholders
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, site_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $hashed_password, $role, $site_id);

        if ($stmt->execute()) { 
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
            <form>
            <div class="alert alert-success">
                <?php echo "User $username added successfully."; ?>
                <br><br>
                <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
            </div>
            </form>
        <?php        
        } else {
            echo '<div class="alert alert-danger">';
            echo "Error: " . $stmt->error;
            echo '</div>';
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>