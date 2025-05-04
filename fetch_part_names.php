<?php
include 'conn.php'; // Ensure database connection

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $searchTerm = "%$query%";

    // SQL with UNION
    $sql = "
    (SELECT part_name FROM product_list WHERE part_name LIKE ?)
    UNION
    (SELECT item_description FROM chem_material_table WHERE item_description LIKE ?)
    LIMIT 5
";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['part_name'];
    }


    echo json_encode($suggestions);
}
