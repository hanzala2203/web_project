<?php
// Database configuration
$host = "localhost";
$db_name = "student_course_hub";
$username = "root";
$password = "";

// Create connection
global $conn;

// Only create a new connection if one doesn't exist
if (!isset($conn)) {
    try {
        // Check if MySQL is running
        $socket = @fsockopen($host, 3306, $errno, $errstr, 5);
        if (!$socket) {
            throw new Exception("MySQL server is not running. Please start MySQL server through XAMPP Control Panel.");
        }
        fclose($socket);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ];

        // Try to connect with database name first
        try {
            $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password, $options);
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), "Unknown database") !== false) {
                // If database doesn't exist, create it
                try {
                    $temp_conn = new PDO("mysql:host=$host", $username, $password, $options);
                    $temp_conn->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password, $options);
                } catch(PDOException $e2) {
                    die("Failed to create database: " . $e2->getMessage());
                }
            } else {
                die("Connection Error: " . $e->getMessage());
            }
        }

        // Create tables if they don't exist
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('student', 'admin', 'staff') NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS programmes (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                level ENUM('undergraduate', 'postgraduate') NOT NULL,
                duration VARCHAR(50),
                image_url VARCHAR(255),
                is_published BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            "CREATE TABLE IF NOT EXISTS modules (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(200) NOT NULL,
                description TEXT,
                credits INT NOT NULL,
                year_of_study INT NOT NULL,
                semester INT DEFAULT 1,
                staff_id INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (staff_id) REFERENCES users(id)
            )",
            
            "CREATE TABLE IF NOT EXISTS programme_modules (
                programme_id INT,
                module_id INT,
                PRIMARY KEY (programme_id, module_id),
                FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
                FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
            )",
            
            "CREATE TABLE IF NOT EXISTS student_interests (
                student_id INT,
                programme_id INT,
                registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (student_id, programme_id),
                FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE
            )",

            "CREATE TABLE IF NOT EXISTS module_prerequisites (
                id INT AUTO_INCREMENT PRIMARY KEY,
                module_id INT NOT NULL,
                prerequisite_module_id INT NOT NULL,
                FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
                FOREIGN KEY (prerequisite_module_id) REFERENCES modules(id) ON DELETE CASCADE,
                UNIQUE KEY module_prerequisite_pair (module_id, prerequisite_module_id)
            )",

            "CREATE TABLE IF NOT EXISTS student_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                type VARCHAR(50) DEFAULT 'info',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                read_at TIMESTAMP NULL,
                FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
            )"
        ];

        foreach ($queries as $query) {
            try {
                $conn->exec($query);
            } catch(PDOException $e) {
                error_log("Table Creation Error: " . $e->getMessage());
                // Continue with other queries even if one fails
            }
        }

    } catch(Exception $e) {
        die($e->getMessage());
    }
}
?>