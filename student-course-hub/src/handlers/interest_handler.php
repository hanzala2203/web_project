<?php
require_once __DIR__ . '/../controllers/StudentController.php';

// Check for session or redirect to login
session_start();
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
        // Check if has registered interest
        if (!$studentController->student->hasInterest($studentId, $programmeId)) {
            $_SESSION['error'] = 'You have not registered interest in this programme.';
        } else if ($studentController->student->removeInterest($studentId, $programmeId)) {
            $_SESSION['success'] = 'Successfully withdrew interest from the programme.';
        } else {
            $_SESSION['error'] = 'Failed to withdraw interest.';
        }
    } else {
        // Check if already registered
        if ($studentController->student->hasInterest($studentId, $programmeId)) {
            $_SESSION['error'] = 'You have already registered interest in this programme.';
        } else if ($studentController->student->addInterest($studentId, $programmeId)) {
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
