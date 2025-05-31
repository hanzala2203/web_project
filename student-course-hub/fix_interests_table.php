<?php
require_once __DIR__ . '/src/config/database.php';

try {
    $conn->beginTransaction();

    // Add created_at column if it doesn't exist
    $conn->exec("ALTER TABLE student_interests ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    
    // Copy registered_at values to created_at if not already done
    $conn->exec("UPDATE student_interests SET created_at = registered_at WHERE created_at IS NULL");
    
    // Add registered_at if it doesn't exist (some installations might be reversed)
    $conn->exec("ALTER TABLE student_interests ADD COLUMN IF NOT EXISTS registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    
    // Sync both columns to have the same value
    $conn->exec("UPDATE student_interests SET registered_at = created_at WHERE registered_at IS NULL");
    $conn->exec("UPDATE student_interests SET created_at = registered_at WHERE created_at IS NULL");

    $conn->commit();
    echo "Student interests table updated successfully.\n";
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error updating table: " . $e->getMessage() . "\n";
}
