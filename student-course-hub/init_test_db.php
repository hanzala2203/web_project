<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/config/database.php';

try {
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Execute the SQL
    $conn->exec($sql);
    
    echo "Database initialized successfully\n";
    
    // Insert test data
    $conn->exec("
        INSERT INTO programmes (title, description, level, duration, is_published) VALUES
        ('Computer Science', 'Bachelor degree in Computer Science', 'Undergraduate', '3 years', 1),
        ('Data Science', 'Master degree in Data Science', 'Postgraduate', '2 years', 1),
        ('Artificial Intelligence', 'Master degree in AI', 'Postgraduate', '2 years', 1)
    ");
    
    echo "Test data inserted successfully\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
