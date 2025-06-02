<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/config/database.php';

try {
    $sql = file_get_contents(__DIR__ . '/database/add_avatar_url_to_users.sql');
    $conn->exec($sql);
    echo "Avatar URL column added successfully\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
