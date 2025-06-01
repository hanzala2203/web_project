<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

// Get database configuration
require_once __DIR__ . '/src/config/database.php';

try {
    file_put_contents(__DIR__ . '/debug.log', "=== START MODULE CHECK ===\n" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    if (!isset($conn)) {
        throw new Exception("Database connection not established");
    }
    
    file_put_contents(__DIR__ . '/debug.log', "Database connected successfully\n", FILE_APPEND);
      // Check modules table
    $query = "SHOW TABLES LIKE 'modules'";
    $result = $conn->query($query);
    if ($result->rowCount() > 0) {
        file_put_contents(__DIR__ . '/debug.log', "Modules table exists\n", FILE_APPEND);
        
        // Count modules
        $count = $conn->query("SELECT COUNT(*) FROM modules")->fetchColumn();
        file_put_contents(__DIR__ . '/debug.log', "Total modules: $count\n", FILE_APPEND);
        
        // Get sample modules
        $modules = $conn->query("SELECT * FROM modules LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents(__DIR__ . '/debug.log', "Sample modules:\n" . print_r($modules, true) . "\n", FILE_APPEND);
        
        // Check table structure
        $structure = $conn->query("DESCRIBE modules")->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents(__DIR__ . '/debug.log', "Table structure:\n" . print_r($structure, true) . "\n", FILE_APPEND);
    } else {
        file_put_contents(__DIR__ . '/debug.log', "Modules table does not exist!\n", FILE_APPEND);
    }
    
} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/debug.log', "Database error: " . $e->getMessage() . "\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/debug.log', "General error: " . $e->getMessage() . "\n", FILE_APPEND);
}
