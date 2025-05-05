<?php
session_start();
require 'conn.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Content-Type: application/json");

define('ROLE_QA_PCO', 'PCO');
define('ROLE_QA_ENGINEER', 'QA ENGINEER');
define('ROLE_QA_SUPERVISOR', 'QA SUPERVISOR');
define('ROLE_QA_MANAGER', 'QA MANAGER');
define('ROLE_REPRESENTATIVE', 'SHELDAHL REPRESENTATIVE');

// Check user session
if (!isset($_SESSION['role']) || !isset($_SESSION["user"])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$username = $_SESSION["user"];
$user_role = $_SESSION['role'];

// Role-based permission mapping
$allowed_roles = [
    ROLE_QA_PCO         => "QA PCO",
    ROLE_QA_ENGINEER    => "QA Engineer",
    ROLE_QA_SUPERVISOR  => "QA Manager",
    ROLE_QA_MANAGER     => "QA Manager",
    ROLE_REPRESENTATIVE => "Representative"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $action_check = $_POST['action'] ?? '';
    $role = $_POST['role'] ?? '';
    $ncpr_num = $_POST['ncpr_num'] ?? '';
    $reason = $_POST['reason'] ?? '';

    if (empty($action) || empty($role) || empty($ncpr_num)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required parameters.",
            "data" => $_POST
        ]);
        exit;
    }

    // Validate role permission
    if (!isset($allowed_roles[$user_role]) || $allowed_roles[$user_role] !== $role) {
        echo json_encode(["status" => "error", "message" => "You are not authorized to perform this action."]);
        exit;
    }

    try {
        // Fetch person_id of the user
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($person_id);
        $stmt->fetch();
        $stmt->close();

        if (!$person_id) {
            throw new Exception("User not found.");
        }

        // Fetch ncpr_id from ncpr_table
        $stmt = $conn->prepare("SELECT ncpr_num FROM ncpr_table WHERE ncpr_num = ?");
        $stmt->bind_param("s", $ncpr_num);
        $stmt->execute();
        $stmt->bind_result($ncpr_found);
        $stmt->fetch();
        $stmt->close();

        if (!$ncpr_found) {
            throw new Exception("NCPR record not found.");
        }

        // Begin transaction
        $conn->begin_transaction();


        // Execute only if user role is ENGINEER
        if ($user_role === "QA ENGINEER" || $user_role === "PCO") {

            $inputs_sakses = include 'insert_dispo_input.php';
            if (!$inputs_sakses) {
                throw new Exception("Execute dispo-input failed: " . $stmt->error);
            }

            $dispo_sakses = include 'insert_dispo.php';
            if (!$dispo_sakses) {
                throw new Exception("Execute dispo failed: " . $stmt->error);
            }

            $radio_sakses = include 'insert_radio_dispo.php';
            if (!$radio_sakses) {
                throw new Exception("Execute radio failed: " . $stmt->error);
            }

            $intervention = include 'insert_intervention.php';
            if (!$intervention) {
                throw new Exception("Execute intervention failed: " . $stmt->error);
            }
        } else {
            error_log("Skipping dispo execution as user role is not ENGINEER.");
        }

        $normalized_action = strtolower($action);

        switch ($normalized_action) {
            case 'approve':
            case 'full_approve':
                $status = 'Approved';
                break;
            case 'reject':
                $status = 'Rejected';
                break;
            case 'cancel':
                $status = 'Canceled';
                break;
            default:
                $status = ucfirst($normalized_action);
                break;
        }

        // Insert approval record
        $query = "INSERT INTO dispo_approval (ncpr_num, approver_role, approver_id, status, approval_date) 
                  VALUES (?, ?, ?, ?, NOW())";
        $dispo_id = executeQuery($conn, $query, [$ncpr_num, $user_role, $person_id, $status], "ssis");

        // Additional processing per action
        switch ($normalized_action) {
            case 'approve':
                $query = "UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$dispo_id, $ncpr_num], "is");
                break;

            case 'full_approve':
                // Add representative approval
                $approver = (object)[
                    'user_role' => 'SHELDAHL REPRESENTATIVE',
                    'person_id' => 9
                ];

                $query = "INSERT INTO dispo_approval (ncpr_num, approver_role, approver_id, status, approval_date) 
                          VALUES (?, ?, ?, ?, NOW())";
                $dispo_id = executeQuery($conn, $query, [$ncpr_num, $approver->user_role, $approver->person_id, $status], "ssis");

                $query = "UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$dispo_id, $ncpr_num], "is");

                // Close the status if representative is last
                $query = "UPDATE ncpr_table SET status = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, ['Closed', $ncpr_num], "ss");
                break;

            case 'reject':
                /*if ($role === 'Representative') {
                    // Update the reason for the specific record
                    $updateSuccess = updateReason($pdo, $ncpr_num, $reason);
                }*/

                $query = "UPDATE disposition_tbl SET reason = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$reason, $ncpr_num], "ss");
                $query = "UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$dispo_id, $ncpr_num], "is");
                break;

            case 'cancel':
                $query = "UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$dispo_id, $ncpr_num], "is");
                // Handle cancel or reject logic based on user role
                $query = "UPDATE ncpr_table SET status = ? WHERE ncpr_num = ?";

                if ($normalized_action === 'cancel' && ($user_role === 'QA SUPERVISOR' || $user_role === 'QA MANAGER')) {
                    $final_status = 'Closed';  // If QA SUPERVISOR cancels, set status to "Closed"
                } else {
                    $final_status = ucfirst($status);  // Otherwise, set it to "Canceled" or "Rejected"
                }

                executeQuery($conn, $query, [$final_status, $ncpr_num], "ss");
                break;
        }

        // Commit transaction
        $conn->commit();

        //always change or config based on the OS 
        /*
        $phpPath = 'C:\xampp\php\php.exe';
        $scriptPath = 'C:\xampp\htdocs\ncpr-2\NCPR\sendPushNotif.php';

        // Escape parameters
        $escaped_ncpr = escapeshellarg($ncpr_num);
        $escaped_person_id = escapeshellarg($person_id);

        // Run in background using `start /B`
        $command = "start /B \"\" \"$phpPath\" \"$scriptPath\" $escaped_ncpr $escaped_person_id";
        pclose(popen("cmd /c $command", "r"));

        error_log("Triggered email script with: $ncpr_num, $person_id");
        */

        echo json_encode(["status" => "success", "message" => "All data inserted successfully!"]);
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

    $conn->close();
}

function executeQuery($conn, $query, $params, $types)
{
    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param($types, ...array_values($params));

    if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

    $insert_id = $stmt->insert_id; // Capture last inserted ID (if applicable)
    $stmt->close();

    return $insert_id;
}

// The updateReason function as described earlier
function updateReason(PDO $pdo, int $id, ?string $reason = null): bool
{
    $sql = "UPDATE disposition_tbl SET reason = :reason WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':reason' => $reason,
        ':id' => $id
    ]);
}
