<!DOCTYPE html>
<html>
<?php

include '../includes/db.php';
require_once '../includes/session.php';
require_once '../logging/logactivity.php';

// Fetch sites from the database
$sites = [];

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}

$siteResult = $conn->query("SELECT site_id, site_name, active FROM sites"); 
while ($row = $siteResult->fetch_assoc()) {
    $sites[] = [
        'site_id' => htmlspecialchars($row['site_id'], ENT_QUOTES, 'UTF-8'),
        'site_name' => htmlspecialchars($row['site_name'], ENT_QUOTES, 'UTF-8'),
        'active' => htmlspecialchars($row['active'], ENT_QUOTES, 'UTF-8')
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $site_name = filter_input(INPUT_POST, 'site_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $is_active = filter_input(INPUT_POST, 'is_active', FILTER_SANITIZE_NUMBER_INT);

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
                <?php echo htmlspecialchars("Site $site_name already exists.", ENT_QUOTES, 'UTF-8'); ?>
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
                <?php echo htmlspecialchars("Site $site_name added successfully.", ENT_QUOTES, 'UTF-8'); ?>
                <br><br>
                <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
            </div>
        </form>
        <?php        
        } else {
            echo '<div class="alert alert-danger">';
            echo htmlspecialchars("Error: " . $stmt->error, ENT_QUOTES, 'UTF-8');
            echo '</div>';
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>
</html>
