<?php
date_default_timezone_set('Asia/Karachi');

// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Possible log file locations
$logFiles = [
    __DIR__ . '/php_errors.log',
    __DIR__ . '/debug.log',
    __DIR__ . '/admin_debug.log',
    'C:/xampp/php/logs/php_error.log',
    'C:/xampp/logs/php_error.log'
];

echo "<h1>PHP Error Logs (Karachi Time)</h1>";
echo "<p>Current time (Karachi): " . date('Y-m-d H:i:s T') . "</p>";

foreach ($logFiles as $logFile) {
    if (file_exists($logFile)) {
        echo "<h2>Contents of " . basename($logFile) . ":</h2>";
        echo "<pre>";
        $contents = file_get_contents($logFile);
        if ($contents) {
            echo htmlspecialchars($contents);
        } else {
            echo "Log file is empty";
        }
        echo "</pre><hr>";
    }
}

if (!isset($foundLogs)) {
    echo "<p>No log files found. Creating a test log entry...</p>";
    error_log("Test log entry created at " . date('Y-m-d H:i:s T'));
}
?>
