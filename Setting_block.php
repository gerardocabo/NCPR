<?php
require 'conn.php'; // or wherever your DB connection is

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['userId'])) {
    $userId = intval($_POST['userId']);
    $action = $_POST['action'];

    // Fetch current status
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($currentStatus);
    $stmt->fetch();
    $stmt->close();

    $newStatus = ($currentStatus === 'blocked') ? 'active' : 'blocked';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $userId);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "User has been " . ($newStatus === 'blocked' ? "blocked" : "unblocked") . ".",
            "newStatus" => $newStatus
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update user."]);
    }
    $stmt->close();
}
