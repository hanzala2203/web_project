<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/models/Model.php';
require_once __DIR__ . '/src/models/Programme.php';
require_once __DIR__ . '/src/models/Module.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    echo "Current working directory: " . getcwd() . "\n";
    
    // Test database connection directly
    echo "Testing PDO connection directly...\n";
    global $conn;
    if ($conn) {
        echo "Direct database connection exists\n";
        try {
            $test = $conn->query("SELECT 1");
            echo "Direct query test successful\n";
        } catch (\PDOException $e) {
            echo "Direct query test failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "No direct database connection\n";
    }
    
    // Create Programme instance
    echo "\nCreating Programme instance...\n";
    $programme = new App\Models\Programme();
    echo "Programme instance created successfully\n";
    
    // Try to find programme with ID 1 (or the ID you're trying to view)
    $id = 1; // replace with your programme ID
    echo "\nLooking for programme ID: $id\n";
    
    // Test database connection in Programme model
    try {
        $result = $programme->findById($id);
        echo "\nProgramme data:\n";
        var_dump($result);
        
        if ($result) {
            echo "\nTrying to get modules...\n";
            $module = new App\Models\Module();
            echo "Module instance created\n";
            
            try {
                $modules = $module->getByProgramme($id);
                echo "\nModules data:\n";
                var_dump($modules);
            } catch (\Exception $e) {
                echo "Error getting modules: " . $e->getMessage() . "\n";
            }
        } else {
            echo "No programme found with ID: $id\n";
        }
    } catch (\PDOException $e) {
        echo "Database error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
