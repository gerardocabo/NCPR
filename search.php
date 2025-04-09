<?php
include 'conn.php';

if (isset($_GET['query'])) {
    $search = $conn->real_escape_string($_GET['query']);
    $sql = "SELECT part_number FROM product_list WHERE part_number LIKE '%$search%' LIMIT 10";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li class='list-group-item' style='cursor: pointer;' onclick='selectValue(this)'>" .
                htmlspecialchars($row["part_number"]) . "</li>";
        }
    } else {
        echo "<li class='list-group-item'>No results found</li>";
    }
}
