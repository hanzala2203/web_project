<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

file_put_contents(__DIR__ . '/admin_debug.log', "=== CHECKING ADMIN ACCESS ===\n" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/admin_debug.log', "Session data: " . print_r($_SESSION ?? [], true) . "\n", FILE_APPEND);

// Get database configuration
require_once __DIR__ . '/src/config/database.php';

try {
    // Check modules table directly
    $sql = "SELECT COUNT(*) FROM modules";
    $result = $conn->query($sql)->fetchColumn();
    file_put_contents(__DIR__ . '/admin_debug.log', "Modules count: $result\n", FILE_APPEND);

    // Get the first few modules
    $sql = "SELECT * FROM modules LIMIT 5";
    $stmt = $conn->query($sql);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    file_put_contents(__DIR__ . '/admin_debug.log', "Sample modules:\n" . print_r($modules, true) . "\n", FILE_APPEND);

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/admin_debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
}
