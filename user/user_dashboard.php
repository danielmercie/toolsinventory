<!DOCTYPE html>
<html>
<?php
include 'authenticate.php'; 
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<head>
<title>Tools Inventory</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/Site.css">
</head>

<header>
        <div class="container">
            <div id="branding">
                <h1>Welcome to Tools Inventory</h1>
            </div>
            <nav>
                <ul>
                    <li>Hello, <?php echo $_SESSION['username'] ?> !</li>
                    <li>Your role: <?php echo $_SESSION['role'] ?></li>
                    <li><a href="logout.php" class="button">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
<body>
    <h1>User Dashboard</h1>


    <h2>Tools Inventory</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Supplier</th>
            <th>Price</th>
            <th>Date of Purchase</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Description</th>
        </tr>
    
   

        <?php
        $sql = "SELECT * FROM tools";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["category"] . "</td>";
                echo "<td>" . $row["supplier"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>" . $row["dop"] . "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td><img src='images/" . $row["image"] . "' width='100'></td>";
                echo "<td>" . $row["description"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "data not found";
        }
    
    ?>


