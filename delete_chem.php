<?php
include 'conn.php';
header('Content-Type: application/json');

$response = ['status' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $chem_id = $_POST['id'];

    $sql = "DELETE FROM chem_material_table WHERE chem_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $chem_id);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Chemical product deleted successfully.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to delete the record.';
    }

    $stmt->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request.';
}

$conn->close();
echo json_encode($response);
