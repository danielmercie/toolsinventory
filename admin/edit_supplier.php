<?php

require_once '../includes/session.php';
include '../includes/db.php';

$successMessage = "";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$supplier = [];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT supp_name, phone_number FROM suppliers WHERE supp_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $supp_name = $_POST['supp_name'];
        $phone_number = $_POST['phone_number'];

        $stmt = $conn->prepare("UPDATE suppliers SET supp_name = ?, phone_number = ? WHERE supp_id = ?");
        $stmt->bind_param("ssi", $supp_name, $phone_number, $id);
    
        if ($stmt->execute()) {
            ?>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../css/Site.css">
                <form>
                <div class="alert alert-success">
                    <?php echo "Supplier $supp_name updated successfully."; ?>
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/Site.css">
    <title>Edit Supplier</title>
</head>
<body>
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
            <br><br>
            <a href="admin_dashboard.php" class="btn btn-primary">Go Back</a>
        </div>
    <?php else: ?>
        <form action="edit_supplier.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="supp_name">Supplier Name:</label>
            <input type="text" id="supp_name" name="supp_name" value="<?php echo htmlspecialchars($supplier['supp_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($supplier['phone_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
                       
            <input type="submit" value="Update">
        </form>
        <center>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    </center>
    <?php endif; ?>
</body>
</html>
