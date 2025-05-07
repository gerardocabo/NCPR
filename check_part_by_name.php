<?php
include 'conn.php';

$response = ['exists' => false];

if (isset($_GET['part_name'])) {
    $partName = $conn->real_escape_string($_GET['part_name']);
    $isChem = isset($_GET['isChem']) && $_GET['isChem'] == '1';

    if ($isChem) {
        $sql = "SELECT part_number FROM chem_material_table WHERE item_description LIKE ? LIMIT 1";
    } else {
        $sql = "SELECT part_number FROM product_list WHERE part_name LIKE ? LIMIT 1";
    }

    $stmt = $conn->prepare($sql);
    $searchTerm = "%$partName%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $response['exists'] = true;
        $response['part_number'] = $row['part_number'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
