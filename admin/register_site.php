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

$siteResult = $conn->query("SELECT site_id, site_name, active FROM sites"); 
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $site_name = $_POST['site_name'];
    $is_active = $_POST['is_active'];

    // Check if the site already exists
    $stmt = $conn->prepare("SELECT site_id FROM sites WHERE site_name = ?");
    $stmt->bind_param("s", $site_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
        <form>
            <div class="alert alert-success">
                <?php echo "Site $site_name already exists."; ?>
                <br><br>
                <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
            </div>
        </form>
        <?php   
    } else {
        // Prepare the SQL statement with placeholders
        $stmt = $conn->prepare("INSERT INTO sites (site_name, active) VALUES (?, ?)");
        $stmt->bind_param("si", $site_name, $is_active);

        if ($stmt->execute()) { 
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
        <form>
            <div class="alert alert-success">
                <?php echo "Site $site_name added successfully."; ?>
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
