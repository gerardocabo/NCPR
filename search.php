<?php
include 'conn.php';

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
    $isChem = isset($_GET['isChem']) && $_GET['isChem'] == '1';

    if ($isChem) {
        $sql = "SELECT part_number FROM chem_material_table WHERE part_number LIKE ? LIMIT 10";
    } else {
        $sql = "SELECT part_number FROM product_list WHERE part_number LIKE ? LIMIT 10";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li class='list-group-item' style='cursor: pointer;' onclick='selectValue(this)'>" .
                htmlspecialchars($row["part_number"]) . "</li>";
        }
    } else {
        echo "<li class='list-group-item'>No results found</li>";
    }
}
