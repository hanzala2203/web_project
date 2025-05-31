<?php
require_once __DIR__ . '/../../controllers/StudentController.php';
use App\Controllers\StudentController;    // Check for session or redirect to login
session_start();
if (!isset($_SESSION['user_id'])) {    header('Location: /student-course-hub/auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$studentId = $_SESSION['user_id'];
$programmeId = $_POST['programme_id'] ?? $_GET['id'] ?? null;
$redirectUrl = isset($_POST['redirect']) ? $_POST['redirect'] : '/student-course-hub/student/explore_programmes.php';

if (!$programmeId) {
    $_SESSION['error'] = 'No programme specified.';    header('Location: /student-course-hub/student/explore_programmes.php');
    exit;
}

$studentController = new StudentController();

try {
    // Check if already registered interest
    if ($studentController->student->hasInterest($studentId, $programmeId)) {
        $_SESSION['info'] = 'You have already registered interest in this programme.';
    } else {        // Register interest
        $result = $studentController->registerInterest($studentId, $programmeId);
        if ($result) {
            $_SESSION['success'] = 'Interest successfully registered.';
        } else {
            $_SESSION['error'] = 'Failed to register interest. Please try again.';
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the programme page or specified redirect URL
header('Location: ' . $redirectUrl);
exit;
