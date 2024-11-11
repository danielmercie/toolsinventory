<!DOCTYPE html>
<html>
<?php

include '../includes/db.php';
require_once '../includes/session.php';
require_once '../logging/logactivity.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS);

    // Check if the category already exists
    $stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
    $stmt->bind_param("s", $category_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
        <form>
            <div class="alert alert-success">
                <?php echo htmlspecialchars("Category $category_name already exists.", ENT_QUOTES, 'UTF-8'); ?>
                <br><br>
                <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
            </div>
        </form>
        <?php   
    } else {
        // Prepare the SQL statement with placeholders
        $stmt = $conn->prepare("INSERT INTO categories (category_name, comment) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $comment);

        if ($stmt->execute()) { 
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/Site.css">
        <form>
            <div class="alert alert-success">
                <?php echo htmlspecialchars("Category $category_name added successfully.", ENT_QUOTES, 'UTF-8'); ?>
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
