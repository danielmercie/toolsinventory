<?php

require_once '../includes/session.php';
include '../includes/db.php';
require_once '../logging/logactivity.php';

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "SELECT tools.id, tools.name, categories.category_name, suppliers.supp_name, sites.site_name, tools.price, tools.dop, tools.quantity, tools.image, tools.description 
        FROM tools 
        LEFT JOIN categories ON tools.category_id = categories.id 
        LEFT JOIN suppliers ON tools.supp_id = suppliers.supp_id 
        LEFT JOIN sites ON tools.site_id = sites.site_id 
        WHERE tools.name LIKE ? 
        OR tools.description LIKE ? 
        OR categories.category_name LIKE ? 
        OR suppliers.supp_name LIKE ? 
        OR sites.site_name LIKE ? 
        LIMIT 10"; // Adjust the limit as needed

$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row["category_name"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row["supp_name"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row["site_name"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>€" . htmlspecialchars($row["price"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row["dop"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row["quantity"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td><img src='" . htmlspecialchars($row["image"], ENT_QUOTES, 'UTF-8') . "' width='100'></td>";
        echo "<td>" . htmlspecialchars($row["description"], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>";
        echo "<form action='edit_tool.php' method='get' style='display:inline;'>";
        echo "<input type='hidden' name='id' value='" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "'>";
        echo "<button type='submit' class='btn btn-primary'>Edit</button>";
        echo "</form> ";
        echo "<form action='delete_tool.php' method='get' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this tool?\");'>";
        echo "<input type='hidden' name='id' value='" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "'>";
        echo "<button type='submit' class='btn btn-danger'>Delete</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='11'>Data not found</td></tr>";
}

$stmt->close();
$conn->close();
?>
