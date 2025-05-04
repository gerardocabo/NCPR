<?php
include 'conn.php'; // Database connection

if (isset($_GET['part_number'])) {
    $part_number = $_GET['part_number'];

    // Query both tables using UNION
    $query = "
        (SELECT part_name FROM product_list WHERE part_number = ?)
        UNION
        (SELECT item_description AS part_name FROM chem_material_table WHERE part_number = ?)
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $part_number, $part_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(["exists" => true, "part_name" => $row['part_name']]);
    } else {
        echo json_encode(["exists" => false]);
    }
}
?>
