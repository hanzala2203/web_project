<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/models/Model.php';
require_once __DIR__ . '/src/models/Programme.php';

try {
    $programme = new App\Models\Programme();
    $result = $programme->findById(1);
    echo "<pre>";
    var_dump($result);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
