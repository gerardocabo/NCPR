<?php
include 'conn.php'; // Ensure database connection

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $searchTerm = "%$query%";
    $isChem = isset($_GET['isChem']) && $_GET['isChem'] == '1';

    if ($isChem) {
        $sql = "SELECT item_description AS name FROM chem_material_table WHERE item_description LIKE ? LIMIT 5";
    } else {
        $sql = "SELECT part_name AS name FROM product_list WHERE part_name LIKE ? LIMIT 5";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['name'];
    }

    echo json_encode($suggestions);
}
