<?php
require_once __DIR__ . '/src/config/database.php';

// Add some basic styling
echo '<!DOCTYPE html>
<html>
<head>
    <title>Database Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; margin: 15px 0; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        h3 { color: #888; }
        .error { color: red; }
        .back-link { margin-bottom: 20px; }
        .back-link a { color: #0066cc; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="back-link">
        <a href="/student-course-hub/admin/dashboard">&larr; Back to Dashboard</a>
    </div>';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get all tables
    $tables_query = "SHOW TABLES";
    $tables_stmt = $db->query($tables_query);
    $tables = $tables_stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h1>Database Tables</h1>";
    
    foreach ($tables as $table) {
        echo "<h2>Table: {$table}</h2>";
        
        // Get table structure
        $structure_query = "DESCRIBE {$table}";
        $structure_stmt = $db->query($structure_query);
        $structure = $structure_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($structure as $column) {
            echo "<tr>";
            foreach ($column as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        // Get table data
        $data_query = "SELECT * FROM {$table} LIMIT 10";
        $data_stmt = $db->query($data_query);
        $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($data) > 0) {
            echo "<h3>Data (First 10 rows):</h3>";
            echo "<table border='1'>";
            
            // Headers
            echo "<tr>";
            foreach (array_keys($data[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            // Data
            foreach ($data as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in this table</p>";
        }
        echo "<hr>";
    }

} catch (PDOException $e) {
    echo '<div class="error">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
echo '</body></html>';
?>
