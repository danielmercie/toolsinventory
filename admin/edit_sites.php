<?php

require_once '../includes/session.php';
include '../includes/db.php';

$successMessage = "";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$site = [];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT site_name, active FROM sites WHERE site_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $site = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $site_name = $_POST['site_name'];
        $active = isset($_POST['active']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE sites SET site_name = ?, active = ? WHERE site_id = ?");
        $stmt->bind_param("sii", $site_name, $active, $id);
    
        if ($stmt->execute()) {
            ?>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../css/Site.css">
                <form>
                <div class="alert alert-danger">
                    <?php echo "Site $site_name updated successfully."; ?>
                    <br><br>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='../admin_dashboard.php'">Go Back</button>
                </div>
                </form>
                
            <?php      
        
        } else {
            echo "Error: " . $stmt->error;
        }
    
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/Site.css">
    <title>Edit Sites</title>
</head>
<body>
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
            <br><br>
            <a href="admin_dashboard.php" class="btn btn-primary">Go Back</a>
        </div>
    <?php else: ?>
        <form action="edit_sites.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="site_name">Site Name:</label>
            <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($site['site_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>            
            <label for="active">Active:</label>
            <input type="checkbox" id="active" name="active" <?php if (isset($site['active']) && $site['active']) echo 'checked'; ?>><br><br>
                       
            <input type="submit" value="Update">
        </form>
        <center>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    </center>
    <?php endif; ?>
</body>
</html>
