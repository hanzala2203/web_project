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

    // Delete and recreate database
    echo "Recreating database...\n";
    $conn->exec("DROP DATABASE IF EXISTS `$db_name`");
    $conn->exec("CREATE DATABASE `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database recreated\n";

    // Select the database
    $conn->exec("USE `$db_name`");
    echo "Using database $db_name\n";

    // Create tables
    echo "Creating tables...\n";
    
    // Users table
    $conn->exec("CREATE TABLE users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('student', 'admin', 'staff') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Programmes table
    $conn->exec("CREATE TABLE programmes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        level ENUM('undergraduate', 'postgraduate') NOT NULL,
        duration VARCHAR(50),
        image_url VARCHAR(255),
        is_published BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Modules table
    $conn->exec("CREATE TABLE modules (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        credits INT NOT NULL,
        year_of_study INT NOT NULL,
        semester INT DEFAULT 1,
        programme_id INT,
        staff_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE SET NULL,
        FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    // Programme modules table
    $conn->exec("CREATE TABLE programme_modules (
        programme_id INT,
        module_id INT,
        PRIMARY KEY (programme_id, module_id),
        FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
    )");

    // Student interests table
    $conn->exec("CREATE TABLE student_interests (
        student_id INT,
        programme_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (student_id, programme_id),
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE
    )");

    // Insert sample data
    echo "Adding sample data...\n";

    // Create admin user
    $adminHash = password_hash('Admin123!', PASSWORD_DEFAULT);
    $conn->exec("INSERT INTO users (username, email, password, role) 
                VALUES ('admin', 'admin@example.com', '$adminHash', 'admin')");
    echo "Added admin user\n";

    // Create sample students
    $studentHash = password_hash('Student123!', PASSWORD_DEFAULT);
    $students = [
        ['john_doe', 'john@example.com'],
        ['jane_smith', 'jane@example.com'],
        ['bob_wilson', 'bob@example.com']
    ];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'student')");
    foreach ($students as $student) {
        $stmt->execute([$student[0], $student[1], $studentHash]);
        echo "Added student {$student[0]}\n";
    }

    // Create sample programmes
    $programmes = [
        [
            'Computer Science',
            'Comprehensive degree in computer science covering programming, algorithms, and software engineering',
            'undergraduate',
            '3 years'
        ],
        [
            'Data Science',
            'Advanced degree focusing on data analysis, machine learning, and statistics',
            'postgraduate',
            '2 years'
        ],
        [
            'Software Engineering',
            'Professional degree in software development and system design',
            'undergraduate',
            '4 years'
        ]
    ];

    $stmt = $conn->prepare("INSERT INTO programmes (title, description, level, duration, is_published) 
                           VALUES (?, ?, ?, ?, true)");
    foreach ($programmes as $prog) {
        $stmt->execute($prog);
        echo "Added programme {$prog[0]}\n";
    }

    // Add some student interests
    echo "Adding student interests...\n";
    $students = $conn->query("SELECT id FROM users WHERE role = 'student'")->fetchAll(PDO::FETCH_COLUMN);
    $programmes = $conn->query("SELECT id FROM programmes")->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare("INSERT INTO student_interests (student_id, programme_id) VALUES (?, ?)");
    foreach ($students as $studentId) {
        foreach ($programmes as $programmeId) {
            try {
                $stmt->execute([$studentId, $programmeId]);
                echo "Added interest for student $studentId in programme $programmeId\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                    throw $e;
                }
            }
        }
    }

    echo "\nDatabase setup complete!\n";
    echo "\nSample credentials:\n";
    echo "Admin: admin@example.com / Admin123!\n";
    echo "Student: john@example.com / Student123!\n";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
