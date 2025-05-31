<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/debug.log');

require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/models/Model.php';
require_once __DIR__ . '/src/models/Programme.php';
require_once __DIR__ . '/src/controllers/StudentController.php';

try {
    // Test database connection
    echo "Testing database connection...<br>";
    $stmt = $conn->query("SELECT 1");
    echo "Database connection successful<br><br>";

    // Try to get programme with ID 1
    echo "Looking up programme with ID 1...<br>";
    $programme = new \App\Models\Programme();
    $result = $programme->findById(1);
    echo "Programme lookup result:<br>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

    // Try to instantiate StudentController
    echo "<br>Testing StudentController...<br>";
    $controller = new \App\Controllers\StudentController();
    $controller->viewProgrammeDetails(1);

} catch (Exception $e) {
    $error = "Error: " . $e->getMessage() . "\nStack trace:\n" . $e->getTraceAsString();
    error_log($error);
    echo "<pre>$error</pre>";
}
