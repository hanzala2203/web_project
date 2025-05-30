<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id'])) {
    die('No programme ID provided');
}

try {
    $stmt = $conn->prepare('SELECT * FROM programmes WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $programme = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$programme) {
        die('Programme not found');
    }

    echo '<pre>';
    print_r($programme);
    echo '</pre>';

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
