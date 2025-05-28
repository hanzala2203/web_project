<?php
try {
    // First connect without database
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read and execute SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');
    $pdo->exec($sql);
    
    echo "Database initialized successfully!\n";
} catch(PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
