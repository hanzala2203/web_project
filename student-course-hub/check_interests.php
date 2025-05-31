<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Define database credentials if not already defined
    $host = 'localhost';
    $database = 'student_course_hub';
    $username = 'root';
    $password = '';
    
    // Connect to database
    echo "Attempting to connect to database...\n";
    $db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check table structure
    $stmt = $db->query("DESCRIBE student_interests");
    echo "Table Structure:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }
    
    // Count total records
    $stmt = $db->query("SELECT COUNT(*) as total FROM student_interests");
    $count = $stmt->fetchColumn();
    echo "\nTotal records: $count\n";
    
    // Show some records if they exist
    if($count > 0) {
        $stmt = $db->query("SELECT si.*, p.title as programme_title, u.email as student_email 
                           FROM student_interests si 
                           JOIN users u ON si.student_id = u.id
                           JOIN programmes p ON si.programme_id = p.id 
                           LIMIT 5");
        echo "\nSample Records:\n";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Student: {$row['student_email']} - Programme: {$row['programme_title']} - Created: {$row['created_at']}\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
