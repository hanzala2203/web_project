<?php
// Debug echo statement
error_log("handle_interest.php accessed - POST data: " . print_r($_POST, true));
require_once __DIR__ . '/../../controllers/StudentController.php';
require_once __DIR__ . '/../../config/config.php';

use App\Controllers\StudentController;

// Check for session or redirect to login (no need to start session as it's already started in index.php)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to register interest in programmes.';
    header('Location: ' . BASE_URL . '/auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$studentId = $_SESSION['user_id'];
$programmeId = $_POST['programme_id'] ?? null;
$action = $_POST['action'] ?? 'register';
$redirect = $_POST['redirect'] ?? BASE_URL . '/student/programme_details?id=' . $programmeId;

if (!$programmeId) {
    $_SESSION['error'] = 'Programme ID is required';
    header('Location: ' . BASE_URL . '/student/explore_programmes');
    exit;
}

$studentController = new StudentController();

try {
    if ($action === 'withdraw') {
        // Instead of accessing $student directly, use controller methods        $result = $studentController->withdrawInterest($studentId, $programmeId);
        if ($result) {
            $_SESSION['success'] = 'Successfully withdrew interest from the programme.';
        } else {
            $_SESSION['error'] = 'Failed to withdraw interest.';
        }
    } else {
        // Instead of accessing $student directly, use controller methods
        $result = $studentController->registerInterest($studentId, $programmeId);
        if ($result === true) {
            $_SESSION['success'] = 'Successfully registered interest in the programme.';
        } else {
            $_SESSION['error'] = 'Failed to register interest.';
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'An error occurred: ' . $e->getMessage();
}

// Redirect back to the referring page
header('Location: ' . $redirect);
exit;
