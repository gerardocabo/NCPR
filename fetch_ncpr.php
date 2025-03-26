<?php
session_start();
require 'connection.php'; // Database connection

header('Content-Type: application/json');

// Function to check approval status based on role
function getPendingApprovals($user_role)
{
    $query = "SELECT id, ncpr_num, initiator, status, `date` FROM ncpr_table WHERE dispo_id IS NULL";

    if ($user_role === 'MANAGER' || $user_role === 'SUPERVISOR') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.status, ncpr_table.`date`
                  FROM ncpr_table
                  JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                  WHERE dispo_approval.approver_role = 'ENGINEER' AND dispo_approval.status = 'Approved'
                  AND ncpr_table.dispo_id IS NOT NULL
                  AND ncpr_table.ncpr_num NOT IN (
                    SELECT ncpr_num FROM dispo_approval WHERE approver_role IN ('MANAGER', 'SUPERVISOR'))";
    } elseif ($user_role === 'REPRESENTATIVE') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.status, ncpr_table.`date`
                  FROM ncpr_table
                  JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                  WHERE dispo_approval.approver_role IN ('MANAGER', 'SUPERVISOR') AND dispo_approval.status = 'Approved'";
    }

    return $query;
}

try {
    // Establish database connection
    $pdo = require 'connection.php';

    // Determine the user's role
    $user_role = strtoupper($_SESSION['role'] ?? 'ENGINEER'); // Default to ENGINEER if role not set
    $sql = getPendingApprovals($user_role);

    // Execute query
    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll();

    // Return JSON response
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Return error response if something fails
    echo json_encode(["error" => $e->getMessage()]);
}
?>
