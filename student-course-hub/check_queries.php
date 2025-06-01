<?php
require_once __DIR__ . '/src/config/database.php';

try {
    // Test the exact query used in Module::getAllModules()
    $query = "SELECT m.*, p.title as programme_name, u.username as staff_name 
              FROM modules m 
              LEFT JOIN programme_modules pm ON m.id = pm.module_id
              LEFT JOIN programmes p ON pm.programme_id = p.id
              LEFT JOIN users u ON m.staff_id = u.id 
              ORDER BY m.title ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($modules) . " modules\n\n";
    foreach ($modules as $module) {
        echo "Module: " . $module['title'] . "\n";
        echo "Programme: " . ($module['programme_name'] ?? 'Not assigned') . "\n";
        echo "Staff: " . ($module['staff_name'] ?? 'Not assigned') . "\n";
        echo "-------------------\n";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
