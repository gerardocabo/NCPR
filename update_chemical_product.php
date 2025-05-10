<?php
include 'conn.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $chem_id = $_POST['chem_id'];
    $part_number = $_POST['part_number'];
    $part_name = $_POST['item_description'];

    $check_sql = "SELECT * FROM chem_material_table WHERE LOWER(part_number) = LOWER(?) AND LOWER(item_description) = LOWER(?) AND chem_id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssi", $part_number, $part_name, $chem_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "exists", "message" => "This chemical already exists."]);
    } else {
        $update_sql = "UPDATE chem_material_table SET part_number = ?, item_description = ? WHERE chem_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $part_number, $part_name, $chem_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Chemical product updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update chemical product."]);
        }

        $stmt->close();
    }

    $check_stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
