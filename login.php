<?php
ob_start();
session_start();
require "connection.php"; // Database connection

header("Content-Type: application/json"); // Set response type to JSON

// Guest Login Function
function handleGuestLogin($guestRole) {
    $allowedGuestRoles = ["guest1", "guest2", "guest3"];

    if (!in_array($guestRole, $allowedGuestRoles)) {
        return json_encode(["status" => "error", "message" => "Invalid guest role selected."]);
    }

    $_SESSION["user"] = ucfirst($guestRole);
    $_SESSION["role"] = "GUEST";

    return json_encode(["status" => "success", "message" => "Guest login successful.", "redirect" => "guest_dashboard.php"]);
}

// Admin Login Function
function handleAdminLogin($username, $password, $pdo) {
    if (empty($username) || empty($password)) {
        return json_encode(["status" => "error", "message" => "Username or password cannot be empty."]);
    }

    try {
        // Prepare the SQL statement using PDO
        $stmt = $pdo->prepare("SELECT users.password, users_roles.name FROM users 
                               JOIN users_roles ON users.role_id = users_roles.id 
                               WHERE users.username = :username");
        
        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return json_encode(["status" => "error", "message" => "User not found or incorrect credentials."]);
        }

        // Verify the password
        if (!password_verify($password, $user["password"])) {
            return json_encode(["status" => "error", "message" => "Incorrect password."]);
        }

        $_SESSION["user"] = $username;
        $_SESSION["role"] = $user["name"];

        $redirectPages = [
            "SUPERADMIN" => "superadmin_dashboard.php",
            "ADMIN" => "admin_dashboard.php",
            "STAFF" => "admin_dashboard.php",
            "ENGINEER" => "engineer_dashboard.php",
            "SUPERVISOR" => "supv&mgrDashboard.php",
            "MANAGER" => "supv&mgrDashboard.php",
            "REPRESENTATIVE" => "representative_dashboard.php",
            "GUEST" => "guest_dashboard.php"
        ];

        return json_encode(["status" => "success", "message" => ucfirst(strtolower($user["name"])) . " login successful.", "redirect" => $redirectPages[$user["name"]] ?? "error.php"]);
    
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return json_encode(["status" => "error", "message" => "An error occurred while processing your request."]);
    }
}

// Handle Requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["guest_role"])) {
        echo handleGuestLogin($_POST["guest_role"]);
        exit();
    }
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        echo handleAdminLogin($_POST["username"], $_POST["password"], $pdo);
        exit();
    }
}

// Default Error Response
echo json_encode(["status" => "error", "message" => "Invalid request."]);
ob_end_flush();
?>
