<?php
session_start();
require 'conn.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Content-Type: application/json");

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

// Log received POST data
error_log("Received Data: " . print_r($_POST, true));

// Role-based permission mapping
$allowed_roles = [
    ROLE_QA_ENGINEER    => "QA Engineer",
    ROLE_QA_SUPERVISOR  => "QA Manager",
    ROLE_QA_MANAGER     => "QA Manager",
    ROLE_REPRESENTATIVE => "Representative"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $role = $_POST['role'] ?? '';
    $ncpr_num = $_POST['ncpr_num'] ?? '';

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
        if ($user_role === "QA ENGINEER" && $action !== "cancel") {
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
        } else {
            error_log("Skipping dispo execution as user role is not ENGINEER.");
        }

        if ($action !== "cancel" && $action !== "reject") {
            // Convert certain actions to past tense
            $action_map = [
                "approve" => "approved",
                "reject" => "rejected",
                "submit" => "submitted",
                "cancel" => "canceled"
            ];

            if (isset($action_map[strtolower($action)])) {
                $action = $action_map[strtolower($action)];
            }

            $status = ucfirst(strtolower($action));

            // Insert into dispo_approval
            $query = "INSERT INTO dispo_approval (ncpr_num, approver_role, approver_id, status, approval_date) 
                      VALUES (?, ?, ?, ?, NOW())";
            $dispo_id = executeQuery($conn, $query, [$ncpr_num, $user_role, $person_id, $status], "ssis");

            // Update dispo_id in ncpr_table
            $query = "UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?";
            executeQuery($conn, $query, [$dispo_id, $ncpr_num], "is");

            // If the user role is REPRESENTATIVE, update the status to Close
            if ($user_role === "SHELDAHL REPRESENTATIVE") {
                $status = "Close";
                $query = "UPDATE ncpr_table SET status = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$status, $ncpr_num], "ss");
            }
        } else {
            // Update status in ncpr_table
            $status = "Close";
            $query = "UPDATE ncpr_table SET status = ? WHERE ncpr_num = ?";
            executeQuery($conn, $query, [$status, $ncpr_num], "ss");
        }

        // Commit transaction
        $conn->commit();

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
