<?php
error_log("=== Module Page Access Check ===");
error_log(date('Y-m-d H:i:s'));

require_once __DIR__ . '/src/config/database.php';

try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM modules
    ");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    error_log("Module count from direct DB check: " . $count);
    
    $stmt = $conn->prepare("
        SELECT * FROM modules LIMIT 1
    ");
    $stmt->execute();
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    error_log("Sample module: " . print_r($sample, true));
    
} catch (Exception $e) {
    error_log("Error checking modules: " . $e->getMessage());
}
