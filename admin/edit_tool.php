<?php

require_once '../includes/session.php';
include '../includes/db.php';

$successMessage = "";

// Fetch the ID of "Head Office Store"
$head_office_store_id = null;
$result = $conn->query("SELECT site_id FROM sites WHERE site_name = 'Head Office Store'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $head_office_store_id = $row['site_id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $supp_id = $_POST['supp_id'];
        $site_id = $_POST['site_id'];
        $price = $_POST['price'];
        $dop = $_POST['dop'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'];

        // Check if the selected site is inactive
        $stmt = $conn->prepare("SELECT active FROM sites WHERE site_id = ?");
        $stmt->bind_param("i", $site_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $site = $result->fetch_assoc();
        $stmt->close();

        if ($site['active'] == 0 && $head_office_store_id !== null) {
            $site_id = $head_office_store_id;
        }

        $stmt = $conn->prepare("UPDATE tools SET name = ?, category_id = ?, supp_id = ?, site_id = ?, price = ?, dop = ?, quantity = ?, description = ? WHERE id = ?");
        $stmt->bind_param("siiidsisi", $name, $category_id, $supp_id, $site_id, $price, $dop, $quantity, $description, $id);

        if ($stmt->execute()) {
            $successMessage = "Tool updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "No tool ID provided.";
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT tools.*, categories.category_name, suppliers.supp_name, sites.site_name 
                FROM tools 
                LEFT JOIN categories ON tools.category_id = categories.id 
                LEFT JOIN suppliers ON tools.supp_id = suppliers.supp_id 
                LEFT JOIN sites ON tools.site_id = sites.site_id 
                WHERE tools.id = $id";
        $result = $conn->query($sql);
        $tool = $result->fetch_assoc();

        // Fetch all categories, suppliers, and sites for the dropdowns
        $categories = $conn->query("SELECT id, category_name FROM categories");
        $suppliers = $conn->query("SELECT supp_id, supp_name FROM suppliers");
        $sites = $conn->query("SELECT site_id, site_name FROM sites");
    } else {
        echo "No tool ID provided.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/Site.css">
    <title>Edit Tool</title>
</head>
<body>
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
            <br><br>
            <a href="admin_dashboard.php" class="btn btn-primary">Go Back</a>
        </div>
    <?php else: ?>
        <form action="edit_tool.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($tool['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($tool['name'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            
            <label for="category_id">Category:</label>
            <select class='dropdown' id="category_id" name="category_id" required>
                <option value="<?php echo htmlspecialchars($tool['category_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($tool['category_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php while($category = $categories->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endwhile; ?>
            </select><br><br>
            
            <label for="supp_id">Supplier:</label>
            <select class='dropdown' id="supp_id" name="supp_id" required>
                <option value="<?php echo htmlspecialchars($tool['supp_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($tool['supp_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php while($supplier = $suppliers->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($supplier['supp_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($supplier['supp_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endwhile; ?>
            </select><br><br>
            
            <label for="site_id">Site:</label>
            <select class='dropdown' name="site_id" required>
                <option value="<?php echo htmlspecialchars($tool['site_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($tool['site_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php while($site = $sites->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($site['site_id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($site['site_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endwhile; ?>
            </select><br><br>
            
            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($tool['price'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            <center>
            <label for="dop">Date of Purchase:</label>
            <input type="date" id="dop" name="dop" value="<?php echo htmlspecialchars($tool['dop'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($tool['quantity'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
                </center>

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($tool['description'], ENT_QUOTES, 'UTF-8'); ?>" required><br><br>
            
            <input type="submit" value="Update">
        </form>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    <?php endif; ?>
</body>
</html>
