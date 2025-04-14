<?php
$logFile = 'C:\xampp\htdocs\NCPR\vulnerability_scan.log'; // path to your log file
$logLines = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

$vulnLogs = [];
$cleanLogs = [];

foreach ($logLines as $line) {
    if (strpos($line, 'No vulnerabilities found') !== false) {
        $cleanLogs[] = $line;
    } elseif (strpos($line, '{') === 0) {
        // This is a JSON vulnerability detail
        $vulnLogs[] = json_decode($line, true);
    } else {
        // Title line: "Vulnerabilities found in ..."
        $vulnLogs[] = ['header' => $line];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Vulnerability Dashboard</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">

</head>

<body class="bg-light">
    <div id="log-section">
        
    </div>
    </div>
    <script>
        function loadLogs() {
            fetch('adashboardVSfetch.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('log-section').innerHTML = html;
                });
        }

        // Initial load
        loadLogs();

        // Refresh every 5 seconds
        setInterval(loadLogs, 5000);
    </script>

</body>

</html>