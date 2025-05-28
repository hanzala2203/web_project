<?php
require_once __DIR__ . '/src/config/database.php';

// Read and execute database.sql
$sql = file_get_contents(__DIR__ . '/database.sql');
try {
    $conn->exec($sql);
    echo "Database initialized successfully\n";

    // Create a test student account
    $hash = password_hash('test123', PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['teststudent', 'student@test.com', $hash, 'student']);
    $studentId = $conn->lastInsertId();
    echo "Test student created with ID: $studentId\n";

    // Add some test data
    $stmt = $conn->prepare("INSERT INTO programmes (title, description, duration_years) VALUES (?, ?, ?)");
    $stmt->execute(['Computer Science', 'Bachelor of Computer Science', 3]);
    $programmeId = $conn->lastInsertId();
    echo "Test programme created with ID: $programmeId\n";

    // Create some test modules
    $stmt = $conn->prepare("INSERT INTO modules (title, credits, year_of_study, semester) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Introduction to Programming', 15, 1, 1]);
    $stmt->execute(['Web Development', 15, 1, 2]);
    $stmt->execute(['Database Systems', 15, 2, 1]);
    echo "Test modules created\n";

    // Enroll test student in the programme
    $stmt = $conn->prepare("INSERT INTO student_programmes (student_id, programme_id) VALUES (?, ?)");
    $stmt->execute([$studentId, $programmeId]);
    echo "Student enrolled in programme\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
