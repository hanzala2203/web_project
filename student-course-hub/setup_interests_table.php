<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/database/create_new_interests.sql');
    $conn->exec($sql);
    echo "Successfully created new interests table!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
