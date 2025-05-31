<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // First try to add created_at column
    $sql = "ALTER TABLE student_interests ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;";
    $conn->exec($sql);
    echo "Added created_at column successfully\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") === false) {
        echo "Error: " . $e->getMessage() . "\n";
    } else {
        echo "created_at column already exists\n";
    }
}

try {
    // Make sure registered_at exists too
    $sql = "ALTER TABLE student_interests ADD COLUMN registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;";
    $conn->exec($sql);
    echo "Added registered_at column successfully\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") === false) {
        echo "Error: " . $e->getMessage() . "\n";
    } else {
        echo "registered_at column already exists\n";
    }
}

// Verify the table structure
$sql = "DESCRIBE student_interests;";
$stmt = $conn->query($sql);
echo "\nCurrent table structure:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
