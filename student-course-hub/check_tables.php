<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // Check modules table
    $query = "SELECT COUNT(*) as count FROM modules";
    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of modules: " . $result['count'] . "\n";

    // Check structure of modules table
    $query = "DESCRIBE modules";
    $stmt = $conn->query($query);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nModules table structure:\n";
    print_r($columns);

    // Check users table (students and staff)
    $query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
    $stmt = $conn->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nUsers by role:\n";
    print_r($results);

    // Check programmes table
    $query = "SELECT COUNT(*) as count FROM programmes";
    $stmt = $conn->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nNumber of programmes: " . $result['count'] . "\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}