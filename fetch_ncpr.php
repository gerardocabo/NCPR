<?php
session_start();
require 'connection.php'; // Database connection

header('Content-Type: application/json');

// Function to get pending approvals based on the user's role
function getPendingApprovals($user_role)
{
    switch ($user_role) {
        case 'QA ENGINEER':
            $query = "
                SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, 
                       ncpr.part_name, ncpr.issue, ncpr.status AS status, ncpr.date, 
                       ncpr.urgent, dispo.status AS statuses, dispo.approver_role, 
                       ncprstatus.is_rejected AS reject
                FROM ncpr_table AS ncpr
                LEFT JOIN ncpr_status_table AS ncprstatus
                        ON ncpr.ncpr_num = ncprstatus.ncpr_num
                LEFT JOIN dispo_approval AS dispo 
                        ON ncpr.ncpr_num = dispo.ncpr_num 
                        AND dispo.approver_role = 'QA ENGINEER'
                WHERE ncprstatus.status = 'Open'
                        AND ncprstatus.is_chem = '0'
                        AND ncpr.ncpr_num NOT IN (
                            SELECT ncpr_num 
                            FROM dispo_approval 
                            WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR')
                        )
                        OR
                        ncprstatus.status = 'Open'
                        AND ncprstatus.is_chem = 0 
                        AND ncprstatus.is_rejected = 1
                ORDER BY ncpr.ncpr_num DESC";
            break;

        case 'PCO':
            $query = "
                SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, 
                       ncpr.part_name, ncpr.issue, ncpr.status AS status, ncpr.date, 
                       ncpr.urgent, dispo.status AS statuses, dispo.approver_role,
                       ncprstatus.is_rejected AS reject
                FROM ncpr_table AS ncpr
                LEFT JOIN ncpr_status_table AS ncprstatus
                        ON ncpr.ncpr_num = ncprstatus.ncpr_num
                LEFT JOIN dispo_approval AS dispo 
                        ON ncpr.ncpr_num = dispo.ncpr_num 
                        AND dispo.approver_role = 'QA ENGINEER'
                WHERE ncprstatus.status = 'Open'
                        AND ncprstatus.is_chem = 1
                        AND ncpr.ncpr_num NOT IN (
                            SELECT ncpr_num 
                            FROM dispo_approval 
                            WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR')
                        )
                        OR
                        ncprstatus.status = 'Open'
                        AND ncprstatus.is_chem = 1 
                        AND ncprstatus.is_rejected = 1
                ORDER BY ncpr.ncpr_num DESC";
            break;

        case 'QA MANAGER':
        case 'QA SUPERVISOR':
            $query = "
                SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, 
                       ncpr.part_name, ncpr.issue, ncpr.status AS status, ncpr.date, 
                       ncpr.urgent, dispo.status AS statuses, dispo.approver_role,
                       ncprstatus.is_rejected AS reject
                FROM ncpr_table AS ncpr
                LEFT JOIN ncpr_status_table AS ncprstatus
                        ON ncpr.ncpr_num = ncprstatus.ncpr_num
                LEFT JOIN dispo_approval AS dispo 
                        ON ncpr.ncpr_num = dispo.ncpr_num 
                WHERE (
                        (dispo.approver_role IN ('QA ENGINEER', 'PCO')
                        AND (
                            ncpr.ncpr_num NOT IN (
                                SELECT ncpr_num 
                                FROM dispo_approval 
                                WHERE approver_role IN ('QA MANAGER', 'QA SUPERVISOR')
                            )
                            OR ncprstatus.is_rejected = 1
                        ))
                        OR (ncpr.status = 'Cancel')
                    )
                ORDER BY ncpr.ncpr_num DESC";
            break;

        case 'SHELDAHL REPRESENTATIVE':
            $query = "
                SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, 
                       ncpr.part_name, ncpr.issue, ncpr.status AS status, ncpr.date, 
                       ncpr.urgent, dispo.status AS statuses, dispo.approver_role
                FROM ncpr_table AS ncpr
                LEFT JOIN ncpr_status_table AS ncprstatus
                        ON ncpr.ncpr_num = ncprstatus.ncpr_num
                LEFT JOIN dispo_approval AS dispo 
                        ON ncpr.ncpr_num = dispo.ncpr_num 
                WHERE ncprstatus.status = 'Open'
                    AND dispo.approver_role IN ('QA MANAGER', 'QA SUPERVISOR')
                    AND NOT EXISTS (
                        SELECT ncpr_num 
                        FROM ncpr_status_table 
                        WHERE ncprstatus.is_rejected = 1
                    )
                ORDER BY ncpr.ncpr_num DESC";
            break;

        default:
            $query = "
                SELECT ncpr.id, ncpr.ncpr_num, ncpr.initiator, ncpr.process, ncpr.part_number, 
                       ncpr.part_name, ncpr.issue, ncpr.status AS status, ncpr.date, 
                       ncpr.urgent
                FROM ncpr_table AS ncpr
                LEFT JOIN ncpr_status_table AS ncprstatus
                        ON ncpr.ncpr_num = ncprstatus.ncpr_num
                WHERE ncprstatus.status = 'Open' 
                      AND dispo_id IS NULL
                ORDER BY ncpr_num DESC";
            break;
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

    foreach ($ncprs as &$ncpr) {
        if (isset($ncpr['reject']) && $ncpr['reject'] == 1) {
            $ncpr['status'] = 'Rejected';
        }
    }
    unset($ncpr);

    // Return JSON response including last seen ID for tracking unseen NCPRs
    echo json_encode(["lastSeenId" => $lastSeenId, "ncprs" => $ncprs], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // Handle database errors
    echo json_encode(["error" => $e->getMessage()]);
} catch (Exception $e) {
    // Handle other exceptions (invalid role, etc.)
    echo json_encode(["error" => $e->getMessage()]);
}
