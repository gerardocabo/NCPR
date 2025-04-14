<?php
// Define the log file or a database connection to store scan results
$logFile = 'C:\xampp\htdocs\NCPR\vulnerability_scan.log';
$currentFile = $_SERVER['SCRIPT_FILENAME'];

// Don’t scan the monitor itself or dashboard
if (basename($currentFile) === 'apatternVS.php' || 
    basename($currentFile) === 'adashboardVS.php' || 
    basename($currentFile) === 'adashboardVSfetch.php') {
    return;
}

function analyze_code($code, $filename = '')
{
    $results = [];

    $sources = ['$_GET', '$_POST', '$_REQUEST', '$_COOKIE', '$_FILES'];

    $sinks = [
        'mysql_query',
        'mysqli_query',
        'pg_query',
        'oci_execute',
        'PDO->query',
        'PDO->exec',
        'echo',
        'print',
        'printf',
        'print_r',
        'include',
        'require',
        'include_once',
        'require_once',
        'exec',
        'shell_exec',
        'system',
        'passthru',
        'popen',
        'proc_open',
        'eval',
        'assert',
        'create_function',
        'preg_replace',
        'unserialize',
        'fopen',
        'file_get_contents',
        'file_put_contents',
        'readfile',
        '$_POST',
        '$_GET',
        'password',
        'token',
        'secret',
        'api_key',
        'private_key',
        'if ($_SESSION["role"] !== "admin")',
    ];

    foreach ($sources as $source) {
        foreach ($sinks as $sink) {
            if (preg_match("/$sink\s*\(.*\s*$source\s*/i", $code)) {
                $results[] = [
                    'vulnerability' => detect_vuln_type($sink),
                    'cause' => "User input ($source) used directly in `$sink`.",
                    'recommendation' => get_recommendation($sink),
                    'severity' => get_severity($sink),
                    'line' => guess_line($code, $sink),
                ];
            }
        }
    }

    // Dangerous file uploads
    if (preg_match('/move_uploaded_file\s*\(.*\$_FILES/i', $code)) {
        $results[] = [
            'vulnerability' => 'Insecure File Upload',
            'cause' => 'User file uploaded without validation.',
            'recommendation' => 'Check MIME type, size, extension. Use random names.',
            'severity' => 'High',
            'line' => guess_line($code, 'move_uploaded_file'),
        ];
    }

    // preg_replace with /e modifier
    if (preg_match('/preg_replace\s*\(.*\/e.*\$_(GET|POST|REQUEST)/i', $code)) {
        $results[] = [
            'vulnerability' => 'Code Injection via preg_replace /e',
            'cause' => 'Using /e modifier with user input.',
            'recommendation' => 'Avoid /e modifier. Use preg_replace_callback instead.',
            'severity' => 'High',
            'line' => guess_line($code, 'preg_replace'),
        ];
    }

    // Deserialization
    if (preg_match('/unserialize\s*\(.*\$_(GET|POST|REQUEST|COOKIE)/i', $code)) {
        $results[] = [
            'vulnerability' => 'Deserialization Vulnerability',
            'cause' => 'Unserialize with user input.',
            'recommendation' => 'Use json_decode() instead, or ensure data is trusted.',
            'severity' => 'High',
            'line' => guess_line($code, 'unserialize'),
        ];
    }

    // File Inclusion
    if (preg_match('/include\s*\(.*\$_(GET|POST|REQUEST|COOKIE)/i', $code)) {
        $results[] = [
            'vulnerability' => 'File Inclusion (RFI/LFI)',
            'cause' => 'User input used in file inclusion.',
            'recommendation' => 'Sanitize and whitelist input. Avoid direct includes.',
            'severity' => 'Critical',
            'line' => guess_line($code, 'include'),
        ];
    }

    // Directory Traversal
    if (preg_match('/(fopen|file_get_contents)\s*\(.*\$_(GET|POST|REQUEST|COOKIE)/i', $code)) {
        $results[] = [
            'vulnerability' => 'Directory Traversal',
            'cause' => 'User input used in file read/write.',
            'recommendation' => 'Sanitize path and use realpath().',
            'severity' => 'High',
            'line' => guess_line($code, 'fopen'),
        ];
    }

    // CSRF detection (forms without CSRF token)
    if (preg_match('/<form.*method="POST"/i', $code) && !preg_match('/<input.*name="csrf_token"/i', $code)) {
        $results[] = [
            'vulnerability' => 'CSRF Vulnerability',
            'cause' => 'Form without CSRF token.',
            'recommendation' => 'Add a hidden CSRF token input.',
            'severity' => 'High',
            'line' => guess_line($code, '<form'),
        ];
    }

    // XSS: flag echo of user input NOT protected by htmlspecialchars()
    if (preg_match('/echo\s+(?!.*htmlspecialchars)\s*\$_(GET|POST|REQUEST)/i', $code)) {
        $results[] = [
            'vulnerability' => 'Cross-Site Scripting (XSS)',
            'cause' => 'Echoing user input directly without encoding.',
            'recommendation' => 'Use htmlspecialchars() when outputting user data.',
            'severity' => 'High',
            'line' => guess_line($code, 'echo'),
        ];
    }

    // Hardcoded secrets
    if (
        preg_match('/(password|token|secret|api_key|private_key)\s*[:=]\s*[\'"].+[\'"]/i', $code) ||
        preg_match('/define\s*\(\s*[\'"](password|secret|token|api_key|private_key)[\'"]\s*,\s*[\'"].+[\'"]\)/i', $code)
    ) {
        $results[] = [
            'vulnerability' => 'Sensitive Data Exposure',
            'cause' => 'Secrets or passwords stored in code.',
            'recommendation' => 'Use environment variables and never store secrets in code.',
            'severity' => 'Critical',
            'line' => guess_line($code, 'password'),
        ];
    }

    // Access control missing from admin pages
    if (stripos($filename, 'admin') !== false && !preg_match('/\$_SESSION\[.*role.*\]/i', $code)) {
        $results[] = [
            'vulnerability' => 'Broken Access Control',
            'cause' => 'No session role check in admin page.',
            'recommendation' => 'Restrict admin pages using session role validation.',
            'severity' => 'Critical',
            'line' => 0,
        ];
    }

    // Insecure role check via GET or POST
    if (preg_match('/if\s*\(.*\$_(GET|POST).*admin.*\)/i', $code)) {
        $results[] = [
            'vulnerability' => 'Broken Access Control',
            'cause' => 'Access level determined from user input.',
            'recommendation' => 'Never trust user input for access control. Use session-based checks.',
            'severity' => 'Critical',
            'line' => guess_line($code, 'if'),
        ];
    }

    // Insecure cookie flags
    if (preg_match('/setcookie\s*\(.*\)/i', $code) && !preg_match('/(secure\s*=>\s*true|httponly\s*=>\s*true)/i', $code)) {
        $results[] = [
            'vulnerability' => 'Insecure Cookie Settings',
            'cause' => 'Cookies set without Secure or HttpOnly flags.',
            'recommendation' => 'Set cookies with secure and httponly flags.',
            'severity' => 'Medium',
            'line' => guess_line($code, 'setcookie'),
        ];
    }

    return $results;
}


function guess_line($code, $needle)
{
    $lines = explode("\n", $code);
    foreach ($lines as $i => $line) {
        if (stripos($line, $needle) !== false) {
            return $i + 1;
        }
    }
    return 0;
}

function detect_vuln_type($sink)
{
    $mapping = [
        // SQL Injection
        'mysql_query' => 'SQL Injection',
        'mysqli_query' => 'SQL Injection',
        'pg_query' => 'SQL Injection',
        'oci_execute' => 'SQL Injection',
        'PDO->query' => 'SQL Injection',
        'PDO->exec' => 'SQL Injection',

        // Cross-Site Scripting
        'echo' => 'Cross-Site Scripting (XSS)',
        'print' => 'Cross-Site Scripting (XSS)',
        'printf' => 'Cross-Site Scripting (XSS)',
        'print_r' => 'Cross-Site Scripting (XSS)',

        // File Inclusion
        'include' => 'File Inclusion',
        'require' => 'File Inclusion',
        'include_once' => 'File Inclusion',
        'require_once' => 'File Inclusion',

        // Command Execution
        'exec' => 'Command Execution',
        'shell_exec' => 'Command Execution',
        'system' => 'Command Execution',
        'passthru' => 'Command Execution',
        'popen' => 'Command Execution',
        'proc_open' => 'Command Execution',

        // Code Execution
        'eval' => 'Code Injection',
        'assert' => 'Code Injection',
        'create_function' => 'Code Injection',
        'preg_replace' => 'Code Injection',

        // Deserialization
        'unserialize' => 'Deserialization Vulnerability',

        // Directory Traversal
        'fopen' => 'Directory Traversal',
        'file_get_contents' => 'Directory Traversal',
        'file_put_contents' => 'Directory Traversal',
        'readfile' => 'Directory Traversal',

        // CSRF
        '$_POST' => 'CSRF Vulnerability',
        '$_GET' => 'CSRF Vulnerability',

        // Sensitive Data
        'password' => 'Sensitive Data Exposure',
        'token' => 'Sensitive Data Exposure',
        'secret' => 'Sensitive Data Exposure',
        'api_key' => 'Sensitive Data Exposure',
        'private_key' => 'Sensitive Data Exposure',

        // Access Control
        'if ($_SESSION["role"] !== "admin")' => 'Broken Access Control',
        'setcookie' => 'Insecure Cookie Settings',
    ];

    return $mapping[$sink] ?? 'Unknown Vulnerability';
}


function get_recommendation($sink)
{
    $rec = [
        // SQL Injection
        'mysql_query' => 'Use PDO with prepared statements.',
        'mysqli_query' => 'Use prepared statements or parameterized queries.',
        'pg_query' => 'Use prepared statements.',
        'oci_execute' => 'Use prepared statements or escape input.',
        'PDO->query' => 'Use bindParam with prepared statements.',
        'PDO->exec' => 'Avoid direct SQL; use parameters.',

        // XSS
        'echo' => 'Escape output using htmlspecialchars().',
        'print' => 'Escape output using htmlspecialchars().',
        'printf' => 'Escape all variables before output.',
        'print_r' => 'Avoid using with raw user input.',

        // File Inclusion
        'include' => 'Avoid including files from user input. Sanitize and whitelist.',
        'require' => 'Avoid including files from user input. Sanitize and whitelist.',

        // Command Execution
        'exec' => 'Never pass user input directly to system commands. Use escapeshellarg().',
        'shell_exec' => 'Avoid or sanitize with escapeshellcmd().',
        'system' => 'Escape and validate command input.',
        'passthru' => 'Validate and escape input.',
        'popen' => 'Avoid use, or sanitize carefully.',
        'proc_open' => 'Sanitize all input before using.',

        // Code Injection
        'eval' => 'Avoid eval(). Refactor code to avoid dynamic execution.',
        'assert' => 'Avoid using assert() with user input.',
        'create_function' => 'Avoid. Use anonymous functions instead.',
        'preg_replace' => 'Avoid /e modifier. Use preg_replace_callback.',

        // Deserialization
        'unserialize' => 'Avoid unserialize() on untrusted data. Use json_decode() instead.',

        // Directory Traversal
        'fopen' => 'Sanitize file paths and validate with realpath().',
        'file_get_contents' => 'Sanitize file paths. Avoid using user input directly.',

        // CSRF
        '$_POST' => 'Use CSRF tokens in forms and validate them.',
        '$_GET' => 'Avoid critical actions via GET. Use POST with CSRF protection.',

        // Sensitive Data
        'password' => 'Store sensitive data in environment variables, not in code.',
        'token' => 'Avoid hardcoding secrets. Use a config/env system.',
        'secret' => 'Never store secrets in source files.',
        'api_key' => 'Use environment variables to store secrets.',
        'private_key' => 'Store securely and outside web root.',

        // Cookies
        'setcookie' => 'Use secure => true and httponly => true flags.',

        // Access Control
        'if ($_SESSION["role"] !== "admin")' => 'Always validate access rights using session data, not user input.',
    ];

    return $rec[$sink] ?? 'Sanitize user input and validate it before using.';
}


function get_severity($sink)
{
    $critical = [
        'eval',
        'exec',
        'include',
        'require',
        'shell_exec',
        'system',
        'unserialize',
        'file_get_contents',
        'fopen',
        'PDO->query',
        'mysql_query',
        'preg_replace',
        'token',
        'secret',
        'api_key',
        'private_key',
        'password',
        'if ($_SESSION["role"] !== "admin")',
        'setcookie'
    ];
    $high = [
        'echo',
        'print',
        'print_r',
        'printf',
        'create_function',
        'popen',
        'proc_open',
        '$_POST',
        '$_GET',
        'file_put_contents'
    ];
    $medium = ['require_once', 'include_once'];

    if (in_array($sink, $critical)) return 'Critical';
    if (in_array($sink, $high)) return 'High';
    if (in_array($sink, $medium)) return 'Medium';
    return 'Low';
}


function analyze_best_practices($code)
{
    $results = [];

    // 1. Prepared Statements
    if (preg_match('/->prepare\s*\(/i', $code)) {
        $results[] = [
            'practice' => 'Prepared Statements',
            'description' => 'Uses PDO prepared statements to prevent SQL injection.',
            'line' => guess_line($code, '->prepare'),
            'status' => 'Good',
        ];
    }

    // 2. Output Escaping
    if (preg_match('/htmlspecialchars\s*\(/i', $code)) {
        $results[] = [
            'practice' => 'Output Escaping',
            'description' => 'Escapes HTML output to prevent XSS.',
            'line' => guess_line($code, 'htmlspecialchars'),
            'status' => 'Good',
        ];
    }

    // 3. CSRF Token
    if (preg_match('/<input.*name=["\']csrf_token["\']/i', $code) || preg_match('/\$_SESSION\s*\[\s*[\'"]csrf_token[\'"]\s*\]/i', $code)) {
        $results[] = [
            'practice' => 'CSRF Token',
            'description' => 'Implements a CSRF token to secure form submissions.',
            'line' => guess_line($code, 'csrf_token'),
            'status' => 'Good',
        ];
    }

    // 4. Input Validation
    if (preg_match('/filter_input\s*\(/i', $code) || preg_match('/filter_var\s*\(/i', $code)) {
        $results[] = [
            'practice' => 'Input Validation',
            'description' => 'Validates or sanitizes user input properly.',
            'line' => guess_line($code, 'filter_'),
            'status' => 'Good',
        ];
    }

    // 5. Password Hashing
    if (preg_match('/password_hash\s*\(/i', $code) && preg_match('/password_verify\s*\(/i', $code)) {
        $results[] = [
            'practice' => 'Secure Password Handling',
            'description' => 'Uses password_hash() and password_verify() for password security.',
            'line' => guess_line($code, 'password_hash'),
            'status' => 'Good',
        ];
    }

    // 6. Secure Session Handling
    if (preg_match('/session_start\s*\(\)/i', $code) && preg_match('/session_regenerate_id\s*\(/i', $code)) {
        $results[] = [
            'practice' => 'Secure Session Handling',
            'description' => 'Uses session_regenerate_id() to prevent session fixation.',
            'line' => guess_line($code, 'session_regenerate_id'),
            'status' => 'Good',
        ];
    }

    // 7. HTTPS Check or Redirect
    if (preg_match('/if\s*\(\s*empty\(\s*\$_SERVER\[["\']HTTPS["\']\]/i', $code)) {
        $results[] = [
            'practice' => 'HTTPS Enforcement',
            'description' => 'Redirects users to HTTPS if not already secure.',
            'line' => guess_line($code, '$_SERVER["HTTPS"]'),
            'status' => 'Good',
        ];
    }

    // 8. Secure Cookie Flags
    if (preg_match('/setcookie\s*\(.*true\s*,.*true\s*\)/i', $code)) {
        $results[] = [
            'practice' => 'Secure Cookies',
            'description' => 'Sets cookies with secure and HTTPOnly flags.',
            'line' => guess_line($code, 'setcookie'),
            'status' => 'Good',
        ];
    }

    // 9. Content Security Policy Header
    if (preg_match('/header\s*\(\s*["\']Content-Security-Policy/i', $code)) {
        $results[] = [
            'practice' => 'Content Security Policy (CSP)',
            'description' => 'Implements CSP to mitigate XSS and data injection.',
            'line' => guess_line($code, 'Content-Security-Policy'),
            'status' => 'Good',
        ];
    }

    // 10. Avoiding Deprecated Features
    if (!preg_match('/create_function\s*\(/i', $code) && !preg_match('/preg_replace\s*\(.*\/e/', $code)) {
        $results[] = [
            'practice' => 'Avoid Deprecated Functions',
            'description' => 'Does not use dangerous legacy features like create_function or /e in preg_replace.',
            'line' => 0,
            'status' => 'Good',
        ];
    }

    return $results;
}


function get_file_hash_from_log($logFile, $filePath)
{
    if (!file_exists($logFile)) return null;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($lines) as $line) {
        if (strpos($line, $filePath) !== false && preg_match('/Hash:\s([a-f0-9]{32})/', $line, $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function update_log_timestamp($logFile, $filePath, $hash, $newTime)
{
    if (!file_exists($logFile)) return;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updatedLines = [];
    $found = false;

    foreach ($lines as $line) {
        if (strpos($line, $filePath) !== false && strpos($line, $hash) !== false) {
            // Update timestamp only for matching file and hash
            $line = preg_replace('/Time: .*$/', "Time: {$newTime}", $line);
            $found = true;
        }
        $updatedLines[] = $line;
    }

    if ($found) {
        file_put_contents($logFile, implode("\n", $updatedLines) . "\n");
    }
}

function limit_log_size($logFile, $maxVuln = 100, $maxClean = 100)
{
    if (!file_exists($logFile)) return;

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $vulnLogs = [];
    $cleanLogs = [];

    foreach ($lines as $line) {
        if (strpos($line, 'No vulnerabilities found') !== false) {
            $cleanLogs[] = $line;
        } else {
            $vulnLogs[] = $line;
        }
    }

    $vulnLogs = array_slice($vulnLogs, -$maxVuln);
    $cleanLogs = array_slice($cleanLogs, -$maxClean);

    $finalLogs = array_merge($vulnLogs, $cleanLogs);
    file_put_contents($logFile, implode("\n", $finalLogs) . "\n");
}

function scan_file($filePath)
{
    global $logFile;
    $code = file_get_contents($filePath);
    $results = analyze_code($code); // Detect vulnerabilities
    $bestPractices = analyze_best_practices($code); // Detect good security practices
    $hash = md5($code);
    $timestamp = date('Y-m-d H:i:s');

    // Get the hash associated with the file path from the log
    $existingHash = get_file_hash_from_log($logFile, $filePath);
    if ($existingHash !== null) {
        // Update the existing log's timestamp based on the stored hash
        update_log_timestamp($logFile, $filePath, $existingHash, $timestamp);
        return;
    }

    // First time this file is logged
    $logEntryHeader = "File: {$filePath} | Hash: {$hash} | Time: {$timestamp}";

    // Log vulnerabilities
    if (!empty($results)) {
        file_put_contents($logFile, $logEntryHeader . "\n", FILE_APPEND);
        foreach ($results as $v) {
            file_put_contents($logFile, "[VULN] " . json_encode($v) . "\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "No vulnerabilities found in {$filePath} | Hash: {$hash} | Time: {$timestamp}\n", FILE_APPEND);
    }

    // Log best practices
    if (!empty($bestPractices)) {
        file_put_contents($logFile, "Security Best Practices found in {$filePath}:\n", FILE_APPEND);
        foreach ($bestPractices as $b) {
            file_put_contents($logFile, "[BEST] " . json_encode($b) . "\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "No security best practices found in {$filePath}.\n", FILE_APPEND);
    }

    limit_log_size($logFile, 100, 100);
}


scan_file($currentFile);
