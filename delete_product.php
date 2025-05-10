<?php
include 'conn.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST['product_id'];

    $delete_sql = "DELETE FROM product_list WHERE product_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Product deleted successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to delete product."
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method."
    ]);
}
