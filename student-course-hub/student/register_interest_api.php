<?php
require_once __DIR__ . '/../src/controllers/InterestController.php';

// Set JSON response header
header('Content-Type: application/json');

// Log the request
error_log("Interest API called - POST data: " . file_get_contents('php://input'));

// Check for session
session_start();
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in");
    http_response_code(401);
    echo json_encode(['error' => 'Please log in to register interest']);
    exit;
}

// Get the data from POST
$data = json_decode(file_get_contents('php://input'), true);
error_log("Decoded POST data: " . print_r($data, true));

$programmeId = $data['programme_id'] ?? null;
$action = $data['action'] ?? 'register'; // Default to register if not specified

if (!$programmeId) {
    error_log("Programme ID missing");
    http_response_code(400);
    echo json_encode(['error' => 'Programme ID is required']);
    exit;
}

$studentId = $_SESSION['user_id'];
$controller = new InterestController();

try {
    error_log("Processing {$action} interest request - Student: $studentId, Programme: $programmeId");

    if ($action === 'withdraw') {
        $result = $controller->withdrawInterest($studentId, $programmeId);
        if ($result) {
            error_log("Successfully withdrawn interest");
            echo json_encode(['success' => true, 'message' => 'Interest withdrawn successfully']);
        } else {
            error_log("Failed to withdraw interest");
            http_response_code(500);
            echo json_encode(['error' => 'Failed to withdraw interest']);
        }
    } else {
        $result = $controller->registerInterest($studentId, $programmeId);
        if ($result === true) {
            error_log("Successfully registered interest");
            echo json_encode(['success' => true, 'message' => 'Interest registered successfully']);
        } else if ($result === "Already registered") {
            error_log("Already registered interest");
            http_response_code(400);
            echo json_encode(['error' => 'Already registered for this programme']);
        } else {
            error_log("Failed to register interest");
            http_response_code(500);
            echo json_encode(['error' => 'Failed to register interest']);
        }
    }
} catch (Exception $e) {
    error_log("Error in interest API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>
