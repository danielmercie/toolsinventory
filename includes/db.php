<?php
$servername = "localhost";
$username = "root";
$password = "xIn5ONPU_uBwIv3G";
$dbname = "tools_inventory";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php