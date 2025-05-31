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

// Get the data from POST
$data = json_decode(file_get_contents('php://input'), true);
$programmeId = $data['programme_id'] ?? null;
$action = $data['action'] ?? 'register'; // Default to register if not specified

if (!$programmeId) {
    http_response_code(400);
    echo json_encode(['error' => 'No programme specified']);
    exit;
}

$studentId = $_SESSION['user_id'];
$studentController = new StudentController();

try {
    // Log request details
    error_log("Attempting to {$action} interest - Student ID: $studentId, Programme ID: $programmeId");
    
    if ($action === 'withdraw') {
        // Withdraw interest
        $result = $studentController->withdrawInterest($studentId, $programmeId);
        if ($result) {
            error_log("Successfully withdrawn interest for Student ID: $studentId, Programme ID: $programmeId");
            echo json_encode([
                'success' => true,
                'message' => 'Interest successfully withdrawn'
            ]);
        }
    } else {
        // Register interest
        $result = $studentController->registerInterest($studentId, $programmeId);
        if ($result) {
            error_log("Successfully registered interest for Student ID: $studentId, Programme ID: $programmeId");
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
