<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/config/database.php';

try {
    // Read the SQL file
    $sql = "ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar_url VARCHAR(255) DEFAULT NULL;";
    
    // Execute the SQL
    global $conn;
    $result = $conn->exec($sql);
    
    echo "Successfully added avatar_url column to users table\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
