<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'conn.php';
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch person_id
$stmt = $conn->prepare("SELECT person_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$person = $result->fetch_assoc();

if (!$person) {
    echo json_encode(['status' => 'error', 'message' => 'User record not found.']);
    exit;
}

$person_id = $person['person_id'];

// Collect POST data
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$fname = $_POST['fname'] ?? '';
$lname = $_POST['lname'] ?? '';
$password = $_POST['password'] ?? '';

// Update users
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $user_id);
}
$stmt->execute();

// Update key_person
$stmt = $conn->prepare("UPDATE key_person SET fname = ?, lname = ? WHERE id = ?");
$stmt->bind_param("ssi", $fname, $lname, $person_id);
$stmt->execute();

// ✅ Respond with success
echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully.']);
exit;
