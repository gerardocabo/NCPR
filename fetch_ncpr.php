<?php
session_start();
require 'connection.php'; // Database connection

header('Content-Type: application/json');

// Function to get pending approvals based on the user's role
function getPendingApprovals($user_role)
{
    // Default query: Fetch all NCPRs that haven't been disposed yet
    $query = "SELECT id, ncpr_num, initiator, process, part_number, part_name, status, `date`, urgent, created_at
                FROM ncpr_table 
                WHERE status = 'open' AND dispo_id IS NULL
                ORDER BY ncpr_num DESC";

    // If the user is a QA MANAGER or QA SUPERVISOR, modify the query to show only NCPRs approved by QA ENGINEER
    if ($user_role === 'QA ENGINEER') {
        $query = "SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, ncpr.part_name, ncpr.issue, ncpr.status as status, ncpr.date, ncpr.urgent, ncpr.created_at, 
                    dispo.status as statuses, dispo.approver_role
                    FROM ncpr_table AS ncpr
                    LEFT JOIN dispo_approval AS dispo 
                    ON ncpr.ncpr_num = dispo.ncpr_num 
                    AND dispo.approver_role = 'QA ENGINEER'
                    WHERE ncpr.status = 'open' 
                    AND ncpr.dispo_id IS NULL OR ncpr.dispo_id = dispo.id
                    AND (
                        dispo.status = 'Approved'
                        OR ncpr.ncpr_num NOT IN (
                            SELECT ncpr_num FROM dispo_approval 
                        WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR')
                        )
                )
                ORDER BY ncpr_num DESC";
    }
    // if role is PCO, this query only chemical material from the database
    elseif ($user_role === 'PCO') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.process, ncpr_table.part_number, ncpr_table.name, ncpr_table.issue, ncpr_table.status, ncpr_table.`date`, ncpr_table.urgent
                              FROM ncpr_table
                              JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                              WHERE dispo_approval.approver_role = 'QA ENGINEER' 
                              AND dispo_approval.status = 'Approved'
                              AND ncpr_table.dispo_id IS NOT NULL
                              AND ncpr_table.ncpr_num NOT IN (
                                SELECT ncpr_num FROM dispo_approval WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR'))
                                ORDER BY ncpr_num DESC";
    }
    //
    elseif ($user_role === 'QA MANAGER' || $user_role === 'QA SUPERVISOR') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.process, ncpr_table.part_number, ncpr_table.part_name, ncpr_table.issue, ncpr_table.status, ncpr_table.`date`, ncpr_table.urgent
                  FROM ncpr_table
                  JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                  WHERE dispo_approval.approver_role = 'QA ENGINEER' 
                  AND dispo_approval.status = 'Approved'
                  AND ncpr_table.dispo_id IS NOT NULL
                  AND ncpr_table.ncpr_num NOT IN (
                    SELECT ncpr_num FROM dispo_approval WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR'))
                    ORDER BY ncpr_num DESC";
    }
    // If the user is a SHELDAHL REPRESENTATIVE, modify the query to show NCPRs approved by QA MANAGER or QA SUPERVISOR
    elseif ($user_role === 'SHELDAHL REPRESENTATIVE') {
        $query = "SELECT ncpr_table.id, ncpr_table.ncpr_num, ncpr_table.initiator, ncpr_table.process, ncpr_table.part_number, ncpr_table.part_name, ncpr_table.issue, ncpr_table.status, ncpr_table.`date`, ncpr_table.urgent
                  FROM ncpr_table
                  JOIN dispo_approval ON ncpr_table.ncpr_num = dispo_approval.ncpr_num
                  WHERE dispo_approval.approver_role IN ('QA MANAGER', 'QA SUPERVISOR') 
                  AND dispo_approval.status = 'Approved'
                  AND ncpr_table.status = 'open'
                  ORDER BY ncpr_num DESC";
    }

    return $query;
}

try {
    // Establish database connection
    $pdo = require 'connection.php';

    // Define the valid roles that can access the system
    $valid_roles = ['QA STAFF', 'QEMS OFFICER', 'PCO', 'QA ENGINEER', 'QA SUPERVISOR', 'QA MANAGER', 'SHELDAHL REPRESENTATIVE'];

    // Get the user role from session, default to 'QA STAFF' if not set
    $user_role = $_SESSION['role'] ?? 'QA STAFF';

    // Validate the user role
    if (!in_array($user_role, $valid_roles)) {
        throw new Exception("Invalid user role: " . htmlspecialchars($user_role));
    }

    // Retrieve the last seen NCPR ID from the database for the logged-in user
    $stmt = $pdo->prepare("SELECT last_seen_id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['user']]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $lastSeenId = $userRow['last_seen_id'] ?? 0; // Default to 0 if no record is found

    // Get the SQL query based on user role
    $sql = getPendingApprovals($user_role);

    // Execute the query and fetch results
    $stmt = $pdo->query($sql);
    $ncprs = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch associative arrays only

    // Return JSON response including last seen ID for tracking unseen NCPRs
    echo json_encode(["lastSeenId" => $lastSeenId, "ncprs" => $ncprs], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Handle database errors
    echo json_encode(["error" => $e->getMessage()]);
} catch (Exception $e) {
    // Handle other exceptions (invalid role, etc.)
    echo json_encode(["error" => $e->getMessage()]);
}
