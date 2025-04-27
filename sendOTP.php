<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = trim($_POST["email"]);
} else {
    die("Error: Email is required.");
}

$otp = rand(100000, 999999);

include 'conn.php';
if (!isset($conn)) {
    die("Error: Database connection failed.");
}

// Check if the email exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update OTP
    $sql_update = "UPDATE users SET otp = ? WHERE email = ?";
    $update_stmt = $conn->prepare($sql_update);
    $update_stmt->bind_param("is", $otp, $email);
    $update_stmt->execute();
    $update_stmt->close();

    // Email sending
    try {
        // Get SMTP credentials
        $result = $conn->query("SELECT smtpUsername, smtpPass FROM email_settings WHERE id = 1");
        $emailConfig = $result->fetch_assoc();

        if (!$emailConfig) throw new Exception("SMTP settings not found.");

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = trim($emailConfig['smtpUsername']);
        $mail->Password = trim($emailConfig['smtpPass']);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->SMTPDebug = 2; // Set to 0 to disable output
        $mail->Debugoutput = function ($str, $level) {
            error_log("PHPMailer Debug: $str");
        };

        $mail->setFrom($mail->Username, 'OTP System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "
            <p>Hello,</p>
            <p>Your One-Time Password (OTP) is: <strong style='font-size: 20px;'>{$otp}</strong></p>
            <p>This OTP is valid for a limited time. Please use it promptly.</p>
        ";

        $mail->send();
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'OTP Sent!',
                text: 'An OTP has been sent to your email.',
                confirmButtonText: 'Enter OTP'
            }).then(() => {
                window.location.href = 'enterOTP.php';
            });
        </script>
        </body>
        </html>
        ";
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: 'Could not send OTP. Please try again later.',
                confirmButtonText: 'OK'
            });
        </script>
        </body>
        </html>
        ";
    }
} else {
    echo "The email address is not registered.";
}

$stmt->close();
$conn->close();
