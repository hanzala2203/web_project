<?php
// Database configuration
$DB_HOST = 'localhost';
$DB_NAME = 'student_course_hub';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $db = new PDO(
        "mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME,
        $DB_USER,
        $DB_PASS
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__ . '/database/enrollments.sql');
    $db->exec($sql);
    echo "Enrollments table created successfully\n";
} catch (PDOException $e) {
    echo "Error creating enrollments table: " . $e->getMessage() . "\n";
}
