<?php
session_start();
require "connection.php"; // Database connection
header("Content-Type: application/json");

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "delete_user") {
        $userId = intval($_POST["userId"] ?? 0);

        if ($userId <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid user ID."]);
            exit;
        }

        // Optional: Prevent deletion of the currently logged-in user
        if (isset($_SESSION['user']) && $_SESSION['user'] === $userId) {
            echo json_encode(["status" => "error", "message" => "You cannot delete your own account."]);
            exit;
        }

        // Check if user exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $checkStmt->execute([$userId]);

        if ($checkStmt->rowCount() === 0) {
            echo json_encode(["status" => "error", "message" => "User not found."]);
            exit;
        }

        // Delete the user
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($deleteStmt->execute([$userId])) {
            echo json_encode(["status" => "success", "message" => "User deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete user."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
