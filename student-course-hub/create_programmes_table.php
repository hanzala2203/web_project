<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // Create student_programmes table
    $sql = "CREATE TABLE IF NOT EXISTS student_programmes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        programme_id INT NOT NULL,
        enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(50) DEFAULT 'active',
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE CASCADE,
        UNIQUE KEY student_programme_pair (student_id, programme_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $conn->exec($sql);
    echo "student_programmes table created successfully\n";

    // Insert sample data
    $stmt = $conn->query("SELECT id FROM users WHERE role = 'student' LIMIT 1");
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT id FROM programmes LIMIT 1");
    $programme = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && $programme) {
        $sql = "INSERT IGNORE INTO student_programmes (student_id, programme_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$student['id'], $programme['id']]);
        echo "Sample enrollment added\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
