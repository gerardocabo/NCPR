<?php
include 'conn.php'; // Database connection

header('Content-Type: application/json'); // Respond with JSON

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST['product_id'];
    $part_number = $_POST['part_number'];
    $part_name = $_POST['part_name'];

    // Check if part_number + part_name exists on another product
    $check_sql = "SELECT * FROM product_list WHERE part_number = ? AND part_name = ? AND product_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssi", $part_number, $part_name, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode([
            "status" => "exists",
            "message" => "This part number and name already exist."
        ]);
    } else {
        $update_sql = "UPDATE product_list SET part_number = ?, part_name = ? WHERE product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $part_number, $part_name, $product_id);

        if ($update_stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Product updated successfully."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to update product."
            ]);
        }

        $update_stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
