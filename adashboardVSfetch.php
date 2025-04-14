<?php
$logFile = 'C:\xampp\htdocs\NCPR\vulnerability_scan.log';
$logLines = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

$vulnLogs = [];
$cleanLogs = [];
$bestPracticeLogs = [];

foreach ($logLines as $line) {
    // Extract timestamp
    preg_match('/Time:\s*([0-9\-:\s]+)/', $line, $match);
    $timestamp = isset($match[1]) ? strtotime($match[1]) : 0;

    // Clean log: No vulnerabilities
    if (strpos($line, 'No vulnerabilities found') !== false) {
        $cleanLogs[] = ['line' => $line, 'time' => $timestamp];
    }
    // Best practices summary line
    elseif (strpos($line, 'Security Best Practices found') !== false) {
        // You can store summary lines if needed
        continue;
    }
    // File header line
    elseif (strpos($line, 'File:') === 0) {
        $vulnLogs[] = [
            'header' => $line,
            'time' => $timestamp,
            'vulnerabilities' => [],
            'best_practices' => []
        ];
    }
    // Line starts with [VULN] or [BEST]
    elseif (strpos(trim($line), '[VULN]') === 0 || strpos(trim($line), '[BEST]') === 0) {
        $lastIndex = count($vulnLogs) - 1;
        if ($lastIndex >= 0) {
            $jsonPart = trim(substr($line, strpos($line, '{')));
            $data = json_decode($jsonPart, true);

            if (strpos($line, '[VULN]') !== false) {
                $vulnLogs[$lastIndex]['vulnerabilities'][] = $data;
            } elseif (strpos($line, '[BEST]') !== false) {
                $vulnLogs[$lastIndex]['best_practices'][] = $data;
            }
        }
    }
}

// Sort both logs by timestamp descending
usort($vulnLogs, fn($a, $b) => $b['time'] <=> $a['time']);
usort($cleanLogs, fn($a, $b) => $b['time'] <=> $a['time']);

?>

<div class="container my-5">
    <h1 class="mb-4 text-center">📊 Vulnerability Monitoring Dashboard</h1>

    <!-- Vulnerabilities Section -->
    <div class="card mb-5 shadow">
        <div class="card-header bg-danger text-white">
            Detected Vulnerabilities (Prioritized)
        </div>
        <div class="card-body p-0">
            <?php if (!empty($vulnLogs)): ?>
                <table class="table table-bordered table-hover m-0">
                    <thead class="table-dark">
                        <tr>
                            <th>File Info</th>
                            <th>Vulnerability</th>
                            <th>Cause</th>
                            <th>Recommendation</th>
                            <th>Severity</th>
                            <th>Line</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($vulnLogs as $log) {
                            foreach ($log['vulnerabilities'] as $vuln) {
                                $badgeClass = match (strtolower($vuln['severity'])) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    'low' => 'info',
                                    default => 'secondary',
                                };
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($log['header']) . "</td>";
                                echo "<td><strong>" . htmlspecialchars($vuln['vulnerability']) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($vuln['cause']) . "</td>";
                                echo "<td>" . htmlspecialchars($vuln['recommendation']) . "</td>";
                                echo "<td><span class='badge bg-{$badgeClass}'>" . htmlspecialchars($vuln['severity']) . "</span></td>";
                                echo "<td>" . htmlspecialchars($vuln['line']) . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-success p-3">✅ No vulnerabilities have been detected.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- No Vulnerability Section -->
    <div class="card shadow mb-5">
        <div class="card-header bg-secondary text-white">
            Scanned Files with No Vulnerabilities
        </div>
        <div class="card-body">
            <?php if (!empty($cleanLogs)): ?>
                <ul class="list-group">
                    <?php foreach ($cleanLogs as $entry): ?>
                        <li class="list-group-item text-muted d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($entry['line']) ?></span>
                            <span class="badge bg-light text-dark small">
                                <?= isset($entry['time']) ? date('Y-m-d H:i:s', $entry['time']) : '' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No "clean" scans logged yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Best Practices Section -->
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            Detected Security Best Practices
        </div>
        <div class="card-body p-0">
            <?php
            $hasBestPractices = false;
            foreach ($vulnLogs as $log) {
                if (!empty($log['best_practices'])) {
                    $hasBestPractices = true;
                    break;
                }
            }
            ?>
            <?php if ($hasBestPractices): ?>
                <table class="table table-bordered table-hover m-0">
                    <thead class="table-success">
                        <tr>
                            <th>File Info</th>
                            <th>Practice</th>
                            <th>Line</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($vulnLogs as $log) {
                            foreach ($log['best_practices'] as $bp) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($log['header']) . "</td>";
                                echo "<td><strong>" . htmlspecialchars($bp['practice']) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($bp['line']) . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted p-3">No security best practices were detected in the scanned files.</p>
            <?php endif; ?>
        </div>
    </div>


</div>