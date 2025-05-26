<?php
// Manually include required files
require_once __DIR__ . '/src/models/Model.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/config/database.php';

use App\Models\User;

$user = new User();

// Create a test user
$userData = [
    'username' => 'mustafa4',
    'email' => 'm4@gmail.com',
    'password' => password_hash('mustafa123', PASSWORD_DEFAULT),
    'role' => 'student'
];

try {
    // Delete existing user if already exists
    $db = new PDO(
        "mysql:host=localhost;dbname=student_course_hub",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $stmt = $db->prepare("DELETE FROM users WHERE email = :email");
    $stmt->execute([':email' => $userData['email']]);
    
    // Create new user
    if ($user->create($userData)) {
        echo "Test user created successfully!\n";
        echo "Email: " . $userData['email'] . "\n";
        echo "Password: mustafa123\n";
    } else {
        echo "Failed to create test user.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
