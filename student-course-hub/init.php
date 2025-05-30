<?php
require_once 'src/config/database.php';

try {
    $db = new Database();
    
    // Create database first
    echo "Creating database...\n";
    $db->createDatabase();
    
    // Now connect to the database and initialize tables
    echo "Initializing tables...\n";
    $conn = $db->getConnection();
    $db->initializeTables();
    
    echo "Database initialized successfully!\n";
} catch(Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
