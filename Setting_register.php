<?php
ob_start();
session_start();
require "connection.php"; // Database connection

header("Content-Type: application/json");

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "register_user") {
        $username = trim($_POST["username"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $role_id  = intval($_POST["role"] ?? 0);

        // Check for empty fields
        if (empty($username) || empty($password) || empty($role_id)) {
            echo json_encode(["status" => "error", "message" => "All fields are required."]);
            exit;
        }

        // Validate role existence
        $stmt = $pdo->prepare("SELECT id FROM users_roles WHERE id = ?");
        $stmt->execute([$role_id]);
        if ($stmt->rowCount() === 0) {
            echo json_encode(["status" => "error", "message" => "Invalid role selected."]);
            exit;
        }

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "error", "message" => "Username already exists."]);
            exit;
        }

        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user with role_id
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $hashedPassword, $role_id])) {
            echo json_encode(["status" => "success", "message" => "Account created successfully!", "redirect" => "Setting_SAdmin.php"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to create account."]);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
        // Fetch all user accounts
        $stmt = $pdo->query("SELECT users.id, users.username, users_roles.role_name AS role, users.status FROM users JOIN users_roles ON users.role_id = users_roles.id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

ob_end_flush();
