<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/config/database.php';

try {
    // Check if avatar_url column exists
    $stmt = $conn->query("SHOW COLUMNS FROM users LIKE 'avatar_url'");
    $columnExists = $stmt->rowCount() > 0;

    if (!$columnExists) {
        // Add the column if it doesn't exist
        echo "Adding avatar_url column...\n";
        $conn->exec("ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255) DEFAULT NULL");
        echo "Successfully added avatar_url column\n";
    } else {
        echo "avatar_url column already exists\n";
    }

    // Check if any records exist
    $stmt = $conn->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "Number of users: " . $userCount . "\n";

    // Check a sample user record
    $stmt = $conn->query("SELECT id, username, email, avatar_url FROM users LIMIT 1");
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Sample user: " . print_r($user, true) . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
