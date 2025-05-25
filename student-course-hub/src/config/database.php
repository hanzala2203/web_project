<?php
class Database {
    private $host = "localhost";
    private $db_name = "student_course_hub";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3, // 3 seconds timeout
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ];

            // Check if MySQL is running first
            $socket = @fsockopen($this->host, 3306, $errno, $errstr, 5);
            if (!$socket) {
                throw new Exception("MySQL server is not running. Please start MySQL server through XAMPP Control Panel.");
            }
            fclose($socket);

            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                $options
            );
            
            return $this->conn;
        } catch(PDOException $e) {
            $message = "Connection Error: ";
            if (strpos($e->getMessage(), "Unknown database") !== false) {
                $message .= "Database '{$this->db_name}' does not exist. Please run init.php to create it.";
            } else {
                $message .= $e->getMessage();
            }
            error_log($message);
            throw new Exception($message);
        }
    }

    public function createDatabase() {
        try {
            // Connect without specifying database
            $conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Create database if it doesn't exist
            $conn->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            return true;
        } catch(PDOException $e) {
            error_log("Database Creation Error: " . $e->getMessage());
            throw new Exception("Failed to create database: " . $e->getMessage());
        }
    }

    // Method to initialize database tables
    public function initializeTables() {
        try {
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
                )"
            ];

            foreach ($queries as $query) {
                $this->conn->exec($query);
            }
            
            return true;
        } catch(PDOException $e) {
            error_log("Table Creation Error: " . $e->getMessage());
            throw new Exception("Failed to initialize database tables: " . $e->getMessage());
        }
    }
}