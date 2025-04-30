<?php
//require_once 'vfry_tkn.php'; // 💡 Always validate before doing anything else

ob_start();
session_start();
require "connection.php"; // Database connection

header("Content-Type: application/json"); // Set response type to JSON

// Guest Login Function
function handleGuestLogin($guestRole)
{
    // Allow only the "GUEST" role
    if ($guestRole !== "GUEST") {
        return json_encode(["status" => "error", "message" => "Invalid Guest Role Selected."]);
    }

    // Set the session variables for the guest login
    $_SESSION["user"] = "GUEST"; // Assuming you always set the user to GUEST
    $_SESSION["role"] = "GUEST"; // Set role to GUEST

    return json_encode(["status" => "success", "message" => "Guest", "redirect" => "guest_dashboard.php"]);
}

// Admin Login Function
function handleAdminLogin($username, $password, $pdo)
{
    if (empty($username) || empty($password)) {
        return json_encode(["status" => "error", "message" => "Username or Password cannot be empty."]);
    }

    try {
        // Get password, role, and status from the database
        $stmt = $pdo->prepare("SELECT users.password, users.username, users.email, users.status, users_roles.role_name 
                               FROM users 
                               JOIN users_roles ON users.role_id = users_roles.id 
                               WHERE users.username = :username OR users.email = :username");

        $stmt->execute(["username" => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return json_encode(["status" => "error", "message" => "User not found or incorrect credentials."]);
        }

        // Check if the user is blocked
        if ($user['status'] === 'blocked') {
            return json_encode(["status" => "error", "message" => "Your account has been blocked. Please contact the administrator."]);
        }

        // Verify the password
        if (!password_verify($password, $user["password"])) {
            return json_encode(["status" => "error", "message" => "Incorrect Password."]);
        }

        $_SESSION["user"] = $user["username"];
        $_SESSION["role"] = $user["role_name"];

        $redirectPages = [
            "SUPERADMIN"                => "SuperAdmin_dashboard.php",
            "ADMIN"                     => "admin_dashboard.php",
            "QA STAFF"                  => "admin_dashboard.php",
            "PCO"                       => "engineer_dashboard.php",
            "QEMS OFFICER"              => "admin_dashboard.php",
            "QA ENGINEER"               => "engineer_dashboard.php",
            "QA SUPERVISOR"             => "supv_mgr_dashboard.php",
            "QA MANAGER"                => "supv_mgr_dashboard.php",
            "SHELDAHL REPRESENTATIVE"   => "representative_dashboard.php",
            "GUEST"                     => "guest_ncprfiling.php",
        ];
        $_SESSION["page"] = $redirectPages[$user["role_name"]] ?? "error.php";

        return json_encode([
            "status" => "success",
            "message" => ucfirst(strtolower($_SESSION["user"])),
            "redirect" => $_SESSION['page']
        ]);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return json_encode(["status" => "error", "message" => "An error occurred while processing your request."]);
    }
}



// Handle Requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {

        if (isset($_POST["guest_role"])) {
            echo handleGuestLogin($_POST["guest_role"]);
            exit();
        }
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            echo handleAdminLogin($_POST["username"], $_POST["password"], $pdo);
            exit();
        }
    } else {
        die("Invalid CSRF token.");
    }
}

// Default Error Response
echo json_encode(["status" => "error", "message" => "Invalid Request."]);
ob_end_flush();
