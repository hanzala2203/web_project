<?php
require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/models/Model.php';
require_once __DIR__ . '/src/models/Student.php';

try {
    echo "Checking database connection...\n";
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful\n\n";

    // Check users table structure
    echo "Table structure for 'users':\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($columns);
    echo "\n";

    // Count total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $totalUsers = $stmt->fetchColumn();
    echo "Total users in database: " . $totalUsers . "\n";

    // Count users by role
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $roleCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nUsers by role:\n";
    print_r($roleCounts);
    
    // Show sample student data
    echo "\nSample student data:\n";
    $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users WHERE role = 'student' LIMIT 5");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($students);

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
