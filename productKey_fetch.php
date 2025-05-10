<?php
require_once 'conn.php'; // Your DB connection

$limit = isset($_GET['length']) ? (int)$_GET['length'] : 10;
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$draw = isset($_GET['draw']) ? (int)$_GET['draw'] : 1;

// Handle sorting
$orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
$orderDirection = $_GET['order'][0]['dir'] ?? 'desc'; // Default to descending

// Adjust columns to use 'product_id'
$columns = ['product_id', 'part_number', 'part_name'];
$orderColumn = $columns[$orderColumnIndex] ?? 'product_id'; // Fallback to product_id

// Search
$searchValue = $_GET['search']['value'] ?? '';
$searchSQL = '';
$params = [];
$types = '';

// Total record count (no search filter)
$totalQuery = "SELECT COUNT(*) AS total FROM product_list";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'] ?? 0;

// If search value exists, build WHERE clause
if (!empty($searchValue)) {
    $searchSQL = "WHERE part_number LIKE ? OR part_name LIKE ?";
    $searchValueLike = "%$searchValue%";
    $params[] = $searchValueLike;
    $params[] = $searchValueLike;
    $types = 'ss';
}

// Filtered record count
$filteredQuery = "SELECT COUNT(*) AS total FROM product_list $searchSQL";
$filteredStmt = $conn->prepare($filteredQuery);
if (!empty($searchSQL)) {
    $filteredStmt->bind_param($types, ...$params);
}
$filteredStmt->execute();
$filteredResult = $filteredStmt->get_result();
$filteredRow = $filteredResult->fetch_assoc();
$filteredRecords = $filteredRow['total'] ?? 0;

// Data query with search, sorting and pagination
$dataQuery = "SELECT product_id, part_number, part_name FROM product_list $searchSQL ORDER BY $orderColumn $orderDirection LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;
$types .= 'ii';

$dataStmt = $conn->prepare($dataQuery);
$dataStmt->bind_param($types, ...$params);
$dataStmt->execute();
$result = $dataStmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
]);
