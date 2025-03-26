<?php
session_start();
require 'connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header("Content-Type: application/json");

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
    "ENGINEER" => "QA Engineer",
    "SUPERVISOR" => "QA Manager",
    "MANAGER" => "QA Manager",
    "REPRESENTATIVE" => "NT Representative"
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
        
        $inputs_sakses = include 'insert_dispo_input.php';
        if(!$inputs_sakses){
            throw new Exception("Execute dispo-input failed: " . $stmt->error);
        }

        $dispo_sakses = include 'insert_dispo.php';
        if(!$dispo_sakses){
            throw new Exception("Execute dispo failed: " . $stmt->error);
        }
        
        $radio_sakses = include 'insert_radio_dispo.php';
        if(!$radio_sakses){
            throw new Exception("Execute radio failed: " . $stmt->error);
        }

        // Insert approval action into dispo_approval
        $status = ucfirst(strtolower($action));
        $stmt = $conn->prepare("INSERT INTO dispo_approval (ncpr_num, approver_role, approver_id, status, approval_date) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param("ssis", $ncpr_num, $user_role, $person_id, $status);
        $dispo_id = $stmt->insert_id;
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        $stmt->close();

        // Update dispo_id in ncpr_table
        $stmt = $conn->prepare("UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
        $stmt->bind_param("is", $dispo_id, $ncpr_num);
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        $stmt->close();

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
