<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$db_name = "student_course_hub";

try {
    echo "Connecting to MySQL...\n";
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully\n";

    // Create database if it doesn't exist
    echo "Creating database if not exists...\n";
    $conn->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created/confirmed\n";

    // Select the database
    $conn->exec("USE `$db_name`");
    echo "Using database $db_name\n";

    // Create tables
    echo "Creating tables...\n";
    
    // Users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('student', 'admin', 'staff') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Sample student data
    echo "Adding sample users...\n";
    $hash = password_hash('Test123!', PASSWORD_DEFAULT);
    $sample_users = [
        ['john_doe', 'john@example.com', $hash, 'student'],
        ['jane_smith', 'jane@example.com', $hash, 'student'],
        ['admin', 'admin@example.com', $hash, 'admin']
    ];

    $stmt = $conn->prepare("INSERT IGNORE INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    foreach ($sample_users as $user) {
        try {
            $stmt->execute($user);
            echo "Added user {$user[0]}\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                throw $e;
            }
            echo "User {$user[0]} already exists\n";
        }
    }

    // Create programmes table
    $conn->exec("CREATE TABLE IF NOT EXISTS programmes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        level ENUM('undergraduate', 'postgraduate') NOT NULL,
        duration VARCHAR(50),
        image_url VARCHAR(255),
        is_published BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Add sample programmes
    echo "Adding sample programmes...\n";
    $sample_programmes = [
        ['Computer Science', 'Bachelor of Computer Science Program', 'undergraduate', '3 years', true],
        ['Data Science', 'Master of Data Science Program', 'postgraduate', '2 years', true],
        ['Software Engineering', 'Bachelor of Software Engineering', 'undergraduate', '4 years', true]
    ];

    $stmt = $conn->prepare("INSERT INTO programmes (title, description, level, duration, is_published) VALUES (?, ?, ?, ?, ?)");
    foreach ($sample_programmes as $prog) {
        try {
            $stmt->execute($prog);
            echo "Added programme {$prog[0]}\n";
        } catch (PDOException $e) {
            echo "Error adding programme {$prog[0]}: {$e->getMessage()}\n";
        }
    }

    // Create student interests table
    $conn->exec("CREATE TABLE IF NOT EXISTS student_interests (
        student_id INT,
        programme_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (student_id, programme_id),
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE
    )");

    // Add sample interests
    echo "Adding sample student interests...\n";
    $stmt = $conn->prepare("SELECT id FROM users WHERE role = 'student' LIMIT 2");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare("SELECT id FROM programmes LIMIT 3");
    $stmt->execute();
    $programmes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $interestStmt = $conn->prepare("INSERT IGNORE INTO student_interests (student_id, programme_id) VALUES (?, ?)");
    foreach ($students as $student) {
        foreach ($programmes as $programme) {
            try {
                $interestStmt->execute([$student, $programme]);
                echo "Added interest for student $student in programme $programme\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                    throw $e;
                }
                echo "Interest already exists for student $student in programme $programme\n";
            }
        }
    }

    echo "\nDatabase setup complete!\n";
    echo "\nSample credentials:\n";
    echo "Student: john@example.com / Test123!\n";
    echo "Admin: admin@example.com / Test123!\n";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
