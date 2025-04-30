<?php
//$gtway = "NCPR_ACCESS_GRANTED_QAE_ONLY_BY_OJT_2025"; $salt = "*june_2_2025*@NTPhil.inc";
/*
session_start();
$gtway = "NCPR_ACCESS_GRANTED_QAE_ONLY_BY_OJT_2025";
$gtway_hashed = hash('sha256', $gtway);;
$expected_base = '32f943e3985f5caa0319576695d7b59c402eab61b07f860cff34f18066a0c16e';
$salt = '*@February_27_2025*&@NTPI_inc*'; // SAME salt used to generate the token
$hardcoded_salt = hash('sha256', $salt);
$expected_token = hash('sha256', $hardcoded_salt . $gtway);

// Get submitted token
$received_token = $_POST['gateway_token'] ?? $_COOKIE['GATEWAY_TOKEN'] ?? '';
if ($received_token !== $gtway_hashed) {
    http_response_code(403); // Optional: returns 403 Forbidden
    exit();
} else {
    if ($expected_base !== $expected_token) {
        http_response_code(403); // Optional: returns 403 Forbidden
        exit();
    }
}
$_SESSION['GATEWAY_VERIFIED'] = true;
$_SESSION['GATEWAY_TOKEN'] = $received_token; // Store for logout reuse
*/
header('Location: loginform.php');
exit();
