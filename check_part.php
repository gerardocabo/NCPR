<?php
include 'conn.php'; // Database connection

if (isset($_GET['part_number'])) {
    $part_number = $_GET['part_number'];

    // Check if the part number exists in product_list
    $query = "SELECT part_name FROM product_list WHERE part_number = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $part_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Part number exists, return description
        echo json_encode(["exists" => true, "part_name" => $row['part_name']]);
    } else {
        // Part number does not exist
        echo json_encode(["exists" => false]);
    }
}
?>
