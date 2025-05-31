<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../src/controllers/InterestController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please log in first']);
    exit;
}

$controller = new InterestController();
$studentId = $_SESSION['user_id'];

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$programmeId = $data['programme_id'] ?? null;
$action = $data['action'] ?? 'register';

if (!$programmeId) {
    http_response_code(400);
    echo json_encode(['error' => 'Programme ID is required']);
    exit;
}

if ($action === 'withdraw') {
    $result = $controller->withdrawInterest($studentId, $programmeId);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Interest withdrawn']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to withdraw interest']);
    }
} else {
    $result = $controller->registerInterest($studentId, $programmeId);
    if ($result === true) {
        echo json_encode(['success' => true, 'message' => 'Interest registered']);
    } else if ($result === "Already registered") {
        http_response_code(400);
        echo json_encode(['error' => 'Already registered for this programme']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to register interest']);
    }
}
?>
