<?php
require_once "config.php";
try {
    $db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check users table
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = \"student\"");
    $studentCount = $stmt->fetchColumn();
    echo "Total students: $studentCount\n";

    if ($studentCount > 0) {
        $stmt = $db->query("SELECT * FROM users WHERE role = \"student\" LIMIT 3");
        echo "First few students:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
