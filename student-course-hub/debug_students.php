<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration - hardcode for testing
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_course_hub');
define('DB_USER', 'root');
define('DB_PASS', '');

echo "Starting debug script...\n";

try {
    // Create PDO connection
    echo "Attempting database connection...\n";
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, 
        DB_USER, 
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!\n\n";

    // First check - direct count of students
    echo "Checking student count...\n";
    $directCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();
    echo "Direct count of students in database: " . $directCount . "\n\n";
    
    // Second check - list all users and their roles
    echo "Listing all users:\n";
    $users = $pdo->query("SELECT id, username, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo sprintf("ID: %d, Username: %s, Email: %s, Role: %s\n", 
            $user['id'], 
            $user['username'],
            $user['email'],
            $user['role']
        );
    }

    // Third check - try the actual getAllStudents query
    echo "\nTesting getAllStudents query:\n";
    $query = "SELECT u.id, u.username, u.email, u.created_at, u.role,
             (SELECT GROUP_CONCAT(DISTINCT p2.title ORDER BY p2.title SEPARATOR ', ')
              FROM student_interests si2 
              LEFT JOIN programmes p2 ON si2.programme_id = p2.id AND p2.is_published = 1
              WHERE si2.student_id = u.id) as interests
             FROM users u
             WHERE u.role = :role
             ORDER BY u.username";

    $stmt = $pdo->prepare($query);
    $stmt->execute([':role' => 'student']);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nResults from getAllStudents query:\n";
    echo "Found " . count($students) . " students\n";
    foreach ($students as $student) {
        echo sprintf("ID: %d, Username: %s, Email: %s, Role: %s, Interests: %s\n",
            $student['id'],
            $student['username'],
            $student['email'],
            $student['role'],
            $student['interests'] ?? 'None'
        );
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
    echo "\nFetching all students...\n";
    $students = $student->getAllStudents();
    echo "Number of students found: " . count($students) . "\n\n";
    echo "Student data:\n";
    print_r($students);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
