<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/config/database.php';

try {
    // Check tables
    $stmt = $conn->query('SHOW TABLES');
    echo "Tables in database:\n";
    while ($row = $stmt->fetch()) {
        echo $row[0] . "\n";
    }

    // Check programmes table
    echo "\nChecking programmes table:\n";
    $stmt = $conn->query('SELECT * FROM programmes');
    $programmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($programmes) . " programmes:\n";
    print_r($programmes);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
