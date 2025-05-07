<?php
ob_start();
session_start();
require "connection.php"; // Database connection

header("Content-Type: application/json");

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "register_user") {
        $fname = trim($_POST["fname"] ?? "");
        $lname = trim($_POST["lname"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $email    = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $role_id  = intval($_POST["role"] ?? 0);

        // Validate all fields
        if (empty($fname) || empty($lname) || empty($username) || empty($email) || empty($password) || empty($role_id)) {
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

        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "error", "message" => "Username or email already exists."]);
            exit;
        }

        // Insert into key_person
        $stmt = $pdo->prepare("INSERT INTO key_person (fname, lname) VALUES (?, ?)");
        if (!$stmt->execute([$fname, $lname])) {
            echo json_encode(["status" => "error", "message" => "Failed to create person record."]);
            exit;
        }
        $person_id = $pdo->lastInsertId();

        // Hash password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into users with person_id
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role_id, person_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword, $role_id, $person_id])) {
            echo json_encode(["status" => "success", "message" => "Account created successfully!", "redirect" => "Setting_SAdmin.php"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to create user account."]);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
        // Fetch all user accounts
        $stmt = $pdo->query("
        SELECT users.id, users.username, users.email, users.status,
               key_person.fname, key_person.lname,
               users_roles.role_name AS role
        FROM users
        JOIN users_roles ON users.role_id = users_roles.id
        JOIN key_person ON users.person_id = key_person.id
    ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}

ob_end_flush();
