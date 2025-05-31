<?php
require_once __DIR__ . '/src/config/database.php';

try {
    $sqlContent = file_get_contents(__DIR__ . '/database/fix_tables.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sqlContent)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
                echo "Successfully executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "Error executing statement: " . $statement . "\n";
                echo "Error message: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nDatabase updates completed.\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
