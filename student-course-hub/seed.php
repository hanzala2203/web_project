<?php
require_once 'src/config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Insert test admin user
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $conn->exec("INSERT INTO users (username, email, password, role) VALUES 
        ('admin', 'admin@university.com', '$adminPass', 'admin')");

    // Insert test programmes
    $conn->exec("INSERT INTO programmes (title, description, level, duration, is_published) VALUES 
        ('BSc Computer Science', 'Bachelor of Science in Computer Science', 'undergraduate', '3 years', true),
        ('MSc Data Science', 'Master of Science in Data Science', 'postgraduate', '1 year', true)");

    echo "Test data inserted successfully!\n";
} catch(Exception $e) {
    die("Error seeding database: " . $e->getMessage() . "\n");
}
