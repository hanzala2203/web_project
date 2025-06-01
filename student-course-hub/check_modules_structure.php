<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_course_hub');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "Starting table check...\n";

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, 
        DB_USER, 
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n\n";

    // Get table structure
    $stmt = $pdo->query('DESCRIBE modules');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Modules table structure:\n";
    foreach ($columns as $col) {
        echo "{$col['Field']}: {$col['Type']}\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
