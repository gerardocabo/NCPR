<?php
session_start();
require 'connection.php'; // Database connection

header('Content-Type: application/json');

// Function to check approval status based on role
function getPendingApprovals($user_role)
{
    $query = "SELECT id, ncpr_num, initiator, status, `date` FROM ncpr_table WHERE dispo_id IS NULL";

    if ($user_role === 'QA MANAGER' || $user_role === 'QA SUPERVISOR') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.status, ncpr_table.`date`
                  FROM ncpr_table
                  JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                  WHERE dispo_approval.approver_role = 'QA ENGINEER' 
                  AND dispo_approval.status = 'Approved'
                  AND ncpr_table.dispo_id IS NOT NULL
                  AND ncpr_table.ncpr_num NOT IN (
                    SELECT ncpr_num FROM dispo_approval WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR'))";
    } elseif ($user_role === 'SHELDAHL REPRESENTATIVE') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.status, ncpr_table.`date`
                  FROM ncpr_table
                  JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                  WHERE dispo_approval.approver_role IN ('QA MANAGER', 'QA SUPERVISOR') 
                  AND dispo_approval.status = 'Approved'";
    } 

    return $query;
}

try {
    // Establish database connection
    $pdo = require 'connection.php';

    // Validate and assign user role
    $valid_roles = ['QA ENGINEER', 'QA SUPERVISOR', 'QA MANAGER', 'SHELDAHL REPRESENTATIVE'];
    $user_role = $_SESSION['role'] ?? 'QA ENGINEER'; // Default role

    if (!in_array($user_role, $valid_roles)) {
        throw new Exception("Invalid user role: " . htmlspecialchars($user_role));
    }

    // Get SQL query based on role
    $sql = getPendingApprovals($user_role);

    // Execute query
    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch only associative arrays

    // Return JSON response
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
