<?php
// Set the expected token (in production, store this securely or generate via session)
$expected_token = 'ABC123SECRET';

// Get all headers
$headers = getallheaders();

// Check token
if (!isset($headers['X-SECURE-TOKEN']) || $headers['X-SECURE-TOKEN'] !== $expected_token) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>403 Forbidden</title>
        <style>
            body { font-family: sans-serif; background: #f2f2f2; padding: 2em; color: #444; }
            h1 { color: #c00; }
        </style>
    </head>
    <body>
        <h1>403 Forbidden</h1>
        <p>You don't have permission to access this resource on this server.</p>
        <hr>
        <address>PHP Server at <?php echo $_SERVER['HTTP_HOST']; ?> Port 80</address>
    </body>
    </html>
    <?php
    exit;
}
