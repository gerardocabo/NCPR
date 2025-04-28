<?php
session_start();

// Prevent browser caching to avoid back button issues after logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Securely get the current page name
$current_page = basename(filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_UNSAFE_RAW) ?? '');

// Define Role Constants
define('ROLE_SUPERADMIN', 'SUPERADMIN');
define('ROLE_ADMIN', 'ADMIN');
define('ROLE_QEMS', 'QEMS OFFICER');
define('ROLE_PCO', 'PCO');
define('ROLE_QA_STAFF', 'QA STAFF');
define('ROLE_QA_ENGINEER', 'QA ENGINEER');
define('ROLE_QA_SUPERVISOR', 'QA SUPERVISOR');
define('ROLE_QA_MANAGER', 'QA MANAGER');
define('ROLE_REPRESENTATIVE', 'SHELDAHL REPRESENTATIVE');
define('ROLE_GUEST', 'GUEST');

// Check if user is logged in
$is_logged_in = !empty($_SESSION["user"]);
$role = $_SESSION["role"] ?? null;

// Define role-based dashboards
$role_dashboard = [
    ROLE_SUPERADMIN     => "SuperAdmin_dashboard.php",
    ROLE_ADMIN          => "admin_dashboard.php",
    ROLE_QA_STAFF       => "admin_dashboard.php",
    ROLE_PCO            => "admin_dashboard.php",
    ROLE_QEMS           => "admin_dashboard.php",
    ROLE_QA_ENGINEER    => "engineer_dashboard.php",
    ROLE_QA_SUPERVISOR  => "supv_mgr_dashboard.php",
    ROLE_QA_MANAGER     => "supv_mgr_dashboard.php",
    ROLE_REPRESENTATIVE => "representative_dashboard.php",
    ROLE_GUEST          => "guest_ncprfiling.php",
];

// Define role-based access for each page
$page_roles = [
    "SuperAdmin_dashboard.php"     => [ROLE_SUPERADMIN],
    "admin_dashboard.php"          => [ROLE_ADMIN, ROLE_QA_STAFF, ROLE_QEMS, ROLE_PCO, ROLE_SUPERADMIN],
    "engineer_dashboard.php"       => [ROLE_QA_ENGINEER, /*ROLE_QA_SUPERVISOR,*/ ROLE_SUPERADMIN],
    "supv_mgr_dashboard.php"       => [ROLE_QA_SUPERVISOR, ROLE_QA_MANAGER, ROLE_SUPERADMIN],
    "representative_dashboard.php" => [ROLE_REPRESENTATIVE, ROLE_SUPERADMIN],
    "guest_ncprfiling.php"         => [ROLE_GUEST],
];

// Redirect users if they attempt to access login pages while logged in
if ($is_logged_in && $current_page === "loginform.php") {
    $target_dashboard = $role_dashboard[$role] ?? "error.php";
    if ($current_page !== basename($target_dashboard)) {
        header("Location: $target_dashboard");
        exit();
    }
}

// Restrict unauthorized users
if (!$is_logged_in) {
    if ($current_page !== "loginform.php") {
        header("Location: loginform.php");
        exit();
    }
} elseif (isset($page_roles[$current_page]) && !in_array($role, $page_roles[$current_page], true)) {
    header("Location: unauthorized.php");
    exit();
}
?>
