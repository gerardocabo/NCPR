<?php

session_start();
require 'conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Read CLI arguments
if ($argc < 3) {
    error_log("Arguments missing in send_email.php");
    exit(1);
}
$ncpr_num = $argv[1];
$person_id = (int)$argv[2];

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

        //use this for actual and real send push notifications to other user
        //$usersQuery = $conn->query("SELECT email FROM users WHERE role_id IN (8,9,10)");

        $usersQuery = $conn->query("SELECT email FROM users WHERE username = 'spmgr ");
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
