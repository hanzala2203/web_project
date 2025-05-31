<?php
require_once __DIR__ . '/src/config/database.php';

try {
    $db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop the table if it exists
    $db->exec("DROP TABLE IF EXISTS student_interests");
    
    // Create a simple interests table
    $sql = "CREATE TABLE student_interests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        programme_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY student_programme (student_id, programme_id)
    )";
    
    $db->exec($sql);
    echo "Student interests table created successfully!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
