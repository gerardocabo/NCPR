<?php
include 'conn.php';

header('Content-Type: application/json');

$response = ['status' => '', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $part_number = $_POST['part_number'];
    $part_name = $_POST['part_name'];

    // Check for duplicates (case-insensitive)
    $check_sql = "SELECT * FROM product_list WHERE LOWER(part_number) = LOWER(?) AND LOWER(part_name) = LOWER(?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $part_number, $part_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $response['status'] = 'exists';
        $response['message'] = 'This part number and name already exist (case-insensitive check).';
    } else {
        $sql = "INSERT INTO product_list (part_number, part_name) VALUES (?, ?)";
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
