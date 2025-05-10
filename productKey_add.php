<?php
include 'conn.php';
header('Content-Type: application/json');

$response = ['status' => '', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $part_number = $_POST['part_number'];
    $part_name = $_POST['item_description'];

    // Check for duplicates (case-insensitive)
    $check_sql = "SELECT * FROM chem_material_table WHERE LOWER(part_number) = LOWER(?) AND LOWER(item_description) = LOWER(?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $part_number, $part_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $response['status'] = 'exists';
        $response['message'] = 'This part number and name already exist (case-insensitive check).';
    } else {
        $sql = "INSERT INTO chem_material_table (part_number, item_description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $part_number, $part_name);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Product added successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Database error occurred.';
        }

        $stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
echo json_encode($response);
