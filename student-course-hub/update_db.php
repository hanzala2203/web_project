<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // Add created_at column to student_interests
    $sql = file_get_contents(__DIR__ . '/database/add_created_at_to_interests.sql');
    $conn->exec($sql);
    echo "Added created_at column to student_interests table\n";

    // Verify programmes table has data
    $stmt = $conn->query("SELECT COUNT(*) FROM programmes");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $conn->exec("
            INSERT INTO programmes (title, description, level, duration, is_published) VALUES
            ('Computer Science', 'Bachelor degree in Computer Science', 'Undergraduate', '3 years', 1),
            ('Data Science', 'Master degree in Data Science', 'Postgraduate', '2 years', 1),
            ('Artificial Intelligence', 'Master degree in AI', 'Postgraduate', '2 years', 1)
        ");
        echo "Added sample programmes\n";
    } else {
        echo "Programmes table already has $count records\n";
    }

} catch (PDOException $e) {
    if ($e->getCode() == '42S21') { // Duplicate column
        echo "created_at column already exists\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
