<?php
require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/models/User.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $password = 'Admin123!';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $userData = [
        'username' => 'admin_test',
        'email' => 'admin_test@example.com',
        'password' => $hashedPassword,
        'role' => 'admin'
    ];

    if ($user->create($userData)) {
        echo "Admin user created successfully\n";
        
        // Verify the user was created
        $createdUser = $user->findByEmail($userData['email']);
        var_dump($createdUser);
    } else {
        echo "Failed to create admin user\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
