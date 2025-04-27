<?php
session_start();
include_once 'conn.php';

$otp = $_POST['otp'] ?? '';
$newPassword = $_POST['new_password'] ?? '';

$sql = "SELECT * FROM users WHERE otp = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $otp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE users SET password = ? WHERE otp = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $hashedPassword, $otp);
    $stmtUpdate->execute();

    if ($stmtUpdate->affected_rows > 0) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Success!',
            'message' => 'Password updated successfully!',
            'redirect' => 'loginform.php' // Change this to your actual login page
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Update Failed',
            'message' => 'Could not update the password. Please try again.'
        ];
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'error',
        'title' => 'Invalid OTP',
        'message' => 'OTP is invalid or expired.'
    ];
}

$stmt->close();
$conn->close();

header("Location: reset_password.php");
exit();
