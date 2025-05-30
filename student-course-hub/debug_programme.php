<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/models/Programme.php';
require_once __DIR__ . '/src/controllers/StudentController.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    $stmt = $conn->query("SELECT 1");
    echo "Database connection successful\n\n";

    // Try to get programme with ID 1
    echo "Looking up programme with ID 1...\n";
    $programme = new App\Models\Programme();
    $result = $programme->findById(1);
    echo "Programme lookup result:\n";
    print_r($result);

    // Try to instantiate StudentController
    echo "\nTesting StudentController...\n";
    $controller = new App\Controllers\StudentController();
    $controller->viewProgrammeDetails(1);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
