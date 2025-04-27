<?php
session_start();
require 'conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

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
    $action_check = $_POST['action'] ?? '';
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

            $intervention = include 'insert_intervention.php';
            if (!$intervention) {
                throw new Exception("Execute intervention failed: " . $stmt->error);
            }
        } else {
            error_log("Skipping dispo execution as user role is not ENGINEER.");
        }

        if ($action !== "cancel" && $action !== "reject") {
            // Convert certain actions to past tense
            $action_map = [
                "full_approve" => "approved",
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

            if ($action_check === "full_approve") {

                // Single approver object
                $approver = (object)[
                    'user_role' => 'SHELDAHL REPRESENTATIVE',
                    'person_id' => 9
                ];

                $query = "INSERT INTO dispo_approval (ncpr_num, approver_role, approver_id, status, approval_date) 
                          VALUES (?, ?, ?, ?, NOW())";
                $dispo_id = executeQuery($conn, $query, [$ncpr_num, $approver->user_role, $approver->person_id, $status], "ssis");

                $query = "UPDATE ncpr_table SET dispo_id = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$dispo_id, $ncpr_num], "is");

                $user_role = $approver->user_role;
            }

            // If the user role is REPRESENTATIVE, update the status to Close
            if ($user_role === "SHELDAHL REPRESENTATIVE") {
                $status = "Close";
                $query = "UPDATE ncpr_table SET status = ? WHERE ncpr_num = ?";
                executeQuery($conn, $query, [$status, $ncpr_num], "ss");
            }
        } else {

            // Convert action to past tense for dispo_approval
            $status = isset($action_map[$action]) ? ucfirst($action_map[$action]) : ucfirst($action);

            // Always set status to "Close" in ncpr_table
            $query = "UPDATE ncpr_table SET status = ? WHERE ncpr_num = ?";
            executeQuery($conn, $query, ["Close", $ncpr_num], "ss");

            // Insert into dispo_approval
            $query = "INSERT INTO dispo_approval (ncpr_num, approver_role, approver_id, status, approval_date) 
              VALUES (?, ?, ?, ?, NOW())";
            executeQuery($conn, $query, [$ncpr_num, $user_role, $person_id, $status], "ssis");
        }

        // Commit transaction
        $conn->commit();

        // Email Notification with Debug
        try {
            // Fetch initiator from ncpr_table
            $ncprQuery = $conn->prepare("SELECT initiator FROM ncpr_table WHERE ncpr_num = ?");
            $ncprQuery->bind_param("s", $ncpr_num);
            $ncprQuery->execute();
            $ncprQuery->bind_result($initiator);
            $ncprQuery->fetch();
            $ncprQuery->close();

            if (empty($initiator)) {
                $initiator = "Unknown Initiator";
            }
            // Check if the person who approved has role_id = 10
            $roleCheck = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
            $roleCheck->bind_param("i", $person_id);
            $roleCheck->execute();
            $roleCheck->bind_result($person_role_id);
            $roleCheck->fetch();
            $roleCheck->close();

            if ($person_role_id == 10) {
                $result = $conn->query("SELECT smtpUsername, smtpPass FROM email_settings WHERE id = 1");
                $emailConfig = $result->fetch_assoc();

                if (!$emailConfig) throw new Exception("SMTP configuration not found.");

                $mail = new PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = trim($emailConfig['smtpUsername']);
                $mail->Password = trim($emailConfig['smtpPass']);
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                $mail->SMTPDebug = 2; // 0 = off, 2 = full output
                $mail->Debugoutput = function ($str, $level) {
                    error_log("PHPMailer Debug: $str");
                };

                $mail->setFrom($mail->Username, 'N.T. PHILIPPINES INC.');

                $usersQuery = $conn->query("SELECT email FROM notification_users");
                while ($user = $usersQuery->fetch_assoc()) {
                    $email = trim($user['email']);
                    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->addAddress($email);
                        error_log("Adding email recipient: $email");
                    } else {
                        error_log("Invalid or empty email skipped: $email");
                    }
                }

                $mail->isHTML(true);
                $mail->Subject = "NCPR File Notification - {$ncpr_num}";
                // Fetch approver email and role name
                $approverQuery = $conn->prepare("
        SELECT u.email, ur.role_name 
        FROM users u 
        JOIN users_roles ur ON u.role_id = ur.id 
        WHERE u.id = ?
        ");
                $approverQuery->bind_param("i", $person_id);
                $approverQuery->execute();
                $approverQuery->bind_result($approver_email, $approver_role_name);
                $approverQuery->fetch();
                $approverQuery->close();

                if (empty($approver_email)) {
                    $approver_email = "Unknown Email";
                }
                if (empty($approver_role_name)) {
                    $approver_role_name = "Unknown Role";
                }

                $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
                    <h2 style='color: #004085;'>NCPR Notification</h2>
                   <p>The Non-Conformance Product Report (NCPR) <strong style='color: #0056b3;'>{$ncpr_num}</strong> was filed by <strong style='color: #0056b3;'>{$initiator}</strong>.</p>
                
                    <table style='border-collapse: collapse; margin-top: 15px;'>
                        <tr>
                            <td style='padding: 6px 12px; font-weight: bold;'>Action Taken By:</td>
                            <td style='padding: 6px 12px;'>{$approver_email}</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 12px; font-weight: bold;'>Role:</td>
                            <td style='padding: 6px 12px;'>{$approver_role_name}</td>
                        </tr>
                        <tr>
                            <td style='padding: 6px 12px; font-weight: bold;'>Date/Time:</td>
                            <td style='padding: 6px 12px;'>" . date('F j, Y \a\t g:i A') . "</td>
                        </tr>
                    </table>
                
                    <p style='margin-top: 20px;'>This is an automated message from the NCPR System. Please do not reply directly to this email.</p>
                    <hr style='border: none; border-top: 1px solid #ccc; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #777;'>N.T. PHILIPPINES INC. - Quality Assurance Department</p>
                </div>
                ";

                $mail->send();
                error_log("PHPMailer: Email sent successfully!");
            } else {
                error_log("Email not sent: person_id $person_id has role_id $person_role_id");
            }
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("PHPMailer Exception: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("General Email Exception: " . $e->getMessage());
        }

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
