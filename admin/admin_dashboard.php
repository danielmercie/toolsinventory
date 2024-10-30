<!DOCTYPE html>
<html>
<?php
$pageTitle = "Tools Inventory";
require_once '../includes/session.php';
include '../includes/db.php';
include '../includes/header.php';

?>

<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Welcome to Tools Inventory</h1>
            </div>
            <nav>
                <ul>
                    <li>Hello, <?php echo $_SESSION['user'] ?> !</li>
                    <li>Your role: <?php echo $_SESSION['role'] ?></li>
                    <li><a href="../logout.php" class="button">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="dashboard">
            <a href="add_tools.php"><i class="fas fa-wrench"></i> Add Tools</a>
            <a href="view_tools.php"><i class="fas fa-edit"></i> View/Edit Tools</a>
            <a href="add_user.php"><i class="fas fa-user-plus"></i> Add User</a>
            <a href="view_users.php"><i class="fas fa-users"></i> View/Edit Users</a>
            <a href="add_suppliers.php"><i class="fas fa-truck-medical"></i> Add Suppliers</a>
            <a href="view_suppliers.php"><i class="fas fa-truck"></i> View/Edit Suppliers</a>
            <a href="add_categories.php"><i class="fas fa-folder-tree"></i> Add Categories</a>
            <a href="view_categories.php"><i class="fas fa-list"></i> View/Edit Categories</a>
            <a href="view_logs.php"><i class="fas fa-file-alt"></i> View Logs</a>
        </div>
    </div>
</body>
</html>