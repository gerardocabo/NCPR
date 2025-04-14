<?php
//$gtway = "NCPR_ACCESS_GRANTED_QAE_ONLY_BY_OJT_2025"; $salt = "*june_2_2025*@NTPhil.inc";
/*
session_start();
$expected_base = "NCPR_ACCESS_GRANTED_QAE_ONLY_BY_OJT_2025";
$hardcoded_salt = "b10dcad552013b16738bf5f9216b35080e3ffee3cc353f2b4b49d98dbf216768"; // SAME salt used to generate the token
$expected_token = hash('sha256', $hardcoded_salt . $expected_base);

// Get submitted token
$received_token = $_POST['gateway_token'] ?? $_COOKIE['GATEWAY_TOKEN'] ??'';
if ($received_token !== $expected_token) {
    http_response_code(403); // Optional: returns 403 Forbidden
    exit();
}
$_SESSION['GATEWAY_VERIFIED'] = true;
$_SESSION['GATEWAY_TOKEN'] = $received_token; // Store for logout reuse
*/
header('Location: loginform.php');
exit(); 
?>