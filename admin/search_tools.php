<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT tools.id, tools.name, categories.category_name, suppliers.supp_name, sites.site_name, tools.price, tools.dop, tools.quantity, tools.image, tools.description 
        FROM tools 
        LEFT JOIN categories ON tools.category_id = categories.id 
        LEFT JOIN suppliers ON tools.supp_id = suppliers.supp_id 
        LEFT JOIN sites ON tools.site_id = sites.site_id 
        WHERE tools.name LIKE '%$search%'
        OR tools.description LIKE '%$search%'
        OR categories.category_name LIKE '%$search%'
        OR suppliers.supp_name LIKE '%$search%'
        OR sites.site_name LIKE '%$search%'
        LIMIT 10"; // Adjust the limit as needed
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["category_name"] . "</td>";
        echo "<td>" . $row["supp_name"] . "</td>";
        echo "<td>" . $row["site_name"] . "</td>";
        echo "<td>€" . $row["price"] . "</td>";
        echo "<td>" . $row["dop"] . "</td>";
        echo "<td>" . $row["quantity"] . "</td>";
        echo "<td><img src='" . $row["image"] . "' width='100'></td>";
        echo "<td>" . $row["description"] . "</td>";
        echo "<td>";
        echo "<a href='edit_tool.php?id=" . $row["id"] . "' class='btn btn-primary'>Edit</a> ";
        echo "<a href='delete_tool.php?id=" . $row["id"] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this tool?\");'>Delete</a>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='11'>Data not found</td></tr>";
}
?>
