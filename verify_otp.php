<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE otp = ?");
    $stmt->bind_param("s", $enteredOtp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['otp'] = $enteredOtp;
        header("Location: reset_password.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid OTP. Please try again.";
        header("Location: enterOTP.php");
        exit();
    }
}
