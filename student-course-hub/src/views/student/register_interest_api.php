<?php
require_once __DIR__ . '/../../controllers/StudentController.php';
use App\Controllers\StudentController;

// Set JSON response header
header('Content-Type: application/json');

// Check for session
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please log in to register interest']);
    exit;
}

// Get the programme ID from POST data
$data = json_decode(file_get_contents('php://input'), true);
$programmeId = $data['programme_id'] ?? null;

if (!$programmeId) {
    http_response_code(400);
    echo json_encode(['error' => 'No programme specified']);
    exit;
}

$studentId = $_SESSION['user_id'];
$studentController = new StudentController();

try {
    // Check if already registered interest
    if ($studentController->student->hasInterest($studentId, $programmeId)) {
        http_response_code(400);
        echo json_encode(['error' => 'You have already registered interest in this programme']);
        exit;
    }

    // Register interest
    $result = $studentController->registerInterest($programmeId, $studentId);
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Interest successfully registered'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to register interest']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
