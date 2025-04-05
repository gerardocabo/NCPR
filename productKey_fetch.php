<?php
require_once 'conn.php'; // Your DB connection

$limit = isset($_GET['length']) ? (int)$_GET['length'] : 10;
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;

// Handle sorting
$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDirection = $_GET['order'][0]['dir'] ?? 'asc'; // asc or desc

// Map column index to database column
$columns = ['part_number', 'part_name'];
$orderColumn = $columns[$orderColumnIndex] ?? 'part_number'; // default fallback

// Total record count
$totalQuery = "SELECT COUNT(*) AS total FROM product_list";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'] ?? 0;

// Data query with sorting and pagination
$query = "SELECT part_number, part_name 
          FROM product_list 
          ORDER BY $orderColumn $orderDirection 
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords, // Adjust if using search
    "data" => $data
]);
?>
