<?php
session_start();

// Prevent browser caching to avoid back button issues after logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Check if user is logged in
$is_logged_in = isset($_SESSION["user"]);
$role = $_SESSION["role"] ?? null;
$is_guest = $role === "GUEST";

// Define dashboard redirections for each role
$role_dashboard = [
    "SUPERADMIN"     => "superadmin_dashboard.php",
    "ADMIN"          => "admin_dashboard.php",
    "STAFF"          => "admin_dashboard.php",
    "ENGINEER"       => "engineer_dashboard.php",
    "SUPERVISOR"     => "supv&mgrDashboard.php",
    "MANAGER"        => "supv&mgrDashboard.php",
    "REPRESENTATIVE" => "representative_Dashboard.php",
    "GUEST"          => "guest_ncprfiling.php" // Guests can only access this page
];

// Define role-based access for each page
$page_roles = [
    "superadmin_dashboard.php"    => ["SUPERADMIN"],
    "admin_dashboard.php"         => ["ADMIN", "STAFF", "SUPERADMIN"],
    "engineer_dashboard.php"      => ["ENGINEER", "SUPERVISOR", "SUPERADMIN"],
    "supv&mgrDashboard.php"       => ["SUPERVISOR", "MANAGER", "SUPERADMIN"],
    "representative_Dashboard.php"=> ["REPRESENTATIVE", "SUPERADMIN"],
    "guest_ncprfiling.php"        => ["GUEST"] // Only guests can access this
];

// Pages that should not be accessed after login
$entry_pages = ["loginform.php"];

// Redirect logged-in users from login pages to their respective dashboards
if ($is_logged_in && in_array($current_page, $entry_pages)) {
    $target_dashboard = $role_dashboard[$role] ?? "error.php";

    // Prevent unnecessary redirects
    if ($current_page !== basename($target_dashboard)) {
        header("Location: $target_dashboard");
        exit();
    }
}

// Restrict access based on role
if (isset($page_roles[$current_page])) {
    if (!$role || !in_array($role, $page_roles[$current_page])) {
        header("Location: unauthorized.php"); // Redirect unauthorized users
        exit();
    }
}

// Prevent guests and logged-out users from accessing unauthorized pages
if (!$is_logged_in && $current_page !== "loginform.php") {
    header("Location: loginform.php");
    exit();
}
?>
