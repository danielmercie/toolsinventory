<!DOCTYPE html>
<html>
<?php

include '../includes/db.php';
require_once '../includes/session.php';
require_once '../logging/logactivity.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_name = $_POST['supplier_name'];
    $phone_number = $_POST['phone_number'];

    // Check if the supplier already exists
    $stmt = $conn->prepare("SELECT supp_id FROM suppliers WHERE supp_name = ?");
    $stmt->bind_param("s", $supplier_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
        <form>
            <div class="alert alert-success">
                <?php echo "Supplier $supplier_name already exists."; ?>
                <br><br>
                <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
            </div>
        </form>
        <?php   
    } else {
        // Prepare the SQL statement with placeholders
        $stmt = $conn->prepare("INSERT INTO suppliers (supp_name, phone_number) VALUES (?, ?)");
        $stmt->bind_param("ss", $supplier_name, $phone_number);

        if ($stmt->execute()) { 
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
        <form>
            <div class="alert alert-success">
                <?php echo "Supplier $supplier_name added successfully."; ?>
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
</html>
