<?php
// Basic error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Just output some text
echo "Basic test file working!";

// Try database connection
try {
    $host = "localhost";
    $db_name = "student_course_hub";
    $username = "root";
    $password = "";
    
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "\n\nDatabase connection successful!";
    
    // Try a simple query
    $stmt = $conn->query("SELECT * FROM programmes LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\n\nFirst programme in database:\n";
    print_r($result);
    
} catch(PDOException $e) {
    echo "\n\nConnection failed: " . $e->getMessage();
}
?>
