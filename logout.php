<?php
session_start();

// Capture gateway token from session (if it exists)
//$gateway_token = $_SESSION['GATEWAY_TOKEN'] ?? null;

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

if (isset($_COOKIE[session_name()])) {
    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => '/',
            'secure' => true,       // Only send over HTTPS
            'httponly' => true,     // Not accessible to JavaScript
            'samesite' => 'Lax'     // Prevent CSRF; use 'Strict' or 'None' as needed
        ]
    );
}

// Preserve gateway token in a new cookie for re-entry
/*
if ($gateway_token) {
    setcookie(
        'GATEWAY_TOKEN',
        $gateway_token,
        [
            'expires' => time() + 300, // Valid for 5 minutes
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}
*/

// Prevent browser from showing cached pages after logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: index.php");
exit();
