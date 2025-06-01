<?php
require_once __DIR__ . '/bootstrap.php';

// Get database configuration
$config = require_once __DIR__ . '/src/config/database.php';

try {
    // Create PDO connection
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    error_log("=== CHECKING MODULES TABLE ===");
    error_log("Time: " . date('Y-m-d H:i:s'));
    
    // Check modules table
    $query = "SHOW TABLES LIKE 'modules'";
    $result = $pdo->query($query);
    if ($result->rowCount() > 0) {
        error_log("Modules table exists");
        
        // Count modules
        $count = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
        error_log("Total modules in database: " . $count);
        
        // Get sample modules
        $modules = $pdo->query("SELECT * FROM modules LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        error_log("Sample modules: " . print_r($modules, true));
    } else {
        error_log("Modules table does not exist!");
    }
    
    // Check table structure
    $structure = $pdo->query("DESCRIBE modules")->fetchAll(PDO::FETCH_ASSOC);
    error_log("Table structure: " . print_r($structure, true));
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
