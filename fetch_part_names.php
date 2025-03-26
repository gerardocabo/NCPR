<?php
include 'conn.php'; // Ensure database connection

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT part_name FROM product_list WHERE part_name LIKE ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['part_name'];
    }

    echo json_encode($suggestions);
}
