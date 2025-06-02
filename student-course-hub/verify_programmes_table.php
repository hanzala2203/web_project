<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'student_programmes'");
    $tableExists = $result->rowCount() > 0;
    echo "student_programmes table exists: " . ($tableExists ? "Yes" : "No") . "\n";

    if ($tableExists) {
        // Check table structure
        $result = $conn->query("DESCRIBE student_programmes");
        echo "\nTable structure:\n";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Key'] . "\n";
        }

        // Check if there's any data
        $result = $conn->query("SELECT COUNT(*) FROM student_programmes");
        $count = $result->fetchColumn();
        echo "\nNumber of records: " . $count . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
