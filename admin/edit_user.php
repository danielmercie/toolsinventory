<!DOCTYPE html>
<html>
<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';


$successMessage = "";

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
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $site_id = $_POST['site_id'];

        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, site_id = ? WHERE id = ?");
        $stmt->bind_param("sssii", $username, $password, $role, $site_id, $id);

        if ($stmt->execute()) {
            $successMessage = "User updated successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();

        // Redirect to avoid re-submission and prevent session data from being altered
        header("Location: view_users.php");
        exit;
    } else {
        echo "No user ID provided.";
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT users.*, sites.site_name FROM users LEFT JOIN sites ON users.site_id = sites.site_id WHERE users.id = $id";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
    } else {
        echo "No user ID provided.";
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
    <title>Edit User</title>
</head>
<body>
<h2>Edit User</h2>
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo $successMessage; ?>
            <br><br>
            <a href="admin_dashboard.php" class="btn btn-primary">Go Back</a>
        </div>
    <?php else: ?>
        <form action="edit_user.php" method="post">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required><br><br>
            
            <label for="password">Password:</label>
            <input type="text" id="password" name="password" required><br><br>
            
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="ADMIN" <?php if ($user['role'] == 'ADMIN') echo 'selected'; ?>>ADMIN</option>
                <option value="USER" <?php if ($user['role'] == 'USER') echo 'selected'; ?>>USER</option>
            </select><br><br>
            
            <label for="site">Site:</label>
            <select id="site" name="site_id" required>
                <?php foreach ($sites as $site): ?>
                    <option value="<?= $site['site_id'] ?>" <?php if ($user['site_id'] == $site['site_id']) echo 'selected'; ?>><?= $site['site_name'] ?></option>
                <?php endforeach; ?>
            </select><br><br>
            
            <input type="submit" value="Update">
        </form>
        <button type="button" class="btn btn-primary" onclick="window.location.href='admin_dashboard.php'">Go Back</button>
    <?php endif; ?>
</body>
</html>
