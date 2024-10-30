<!DOCTYPE html>
<html>
<?php

require_once '../includes/session.php';
include '../includes/db.php';

$successMessage = "";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = [];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT category_name, comment FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $category_name = $_POST['category_name'];
        $comment = $_POST['comment'];

        $stmt = $conn->prepare("UPDATE categories SET category_name = ?, comment = ? WHERE id = ?");
        $stmt->bind_param("ssi", $category_name, $comment, $id);
    
        if ($stmt->execute()) {
            $successMessage = "Category updated successfully.";
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
    <title>Edit Categories</title>
</head>
<body>
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
            <br><br>
            <a href="admin_dashboard.php" class="btn btn-primary">Go Back</a>
        </div>
    <?php else: ?>
        <form action="edit_categories.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="category_name">Category:</label>
            <input type="text" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required><br><br>
            
            <label for="comment">Comment:</label>
            <input type="text" id="comment" name="comment" value="<?php echo htmlspecialchars($category['comment']); ?>" required><br><br>
            
            <input type="submit" value="Update">
        </form>
        <center>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    </center>
    <?php endif; ?>
</body>
</html>
