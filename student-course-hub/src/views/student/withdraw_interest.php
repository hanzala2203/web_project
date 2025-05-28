<?php
require_once __DIR__ . '/../../controllers/StudentController.php';
use App\Controllers\StudentController;

// Check for session or redirect to login
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$studentId = $_SESSION['user_id'];
$programmeId = $_POST['programme_id'] ?? $_GET['id'] ?? null;
$redirectUrl = isset($_POST['redirect']) ? $_POST['redirect'] : 'programme_details.php?id=' . $programmeId;

if (!$programmeId) {
    $_SESSION['error'] = 'No programme specified.';
    header('Location: manage_interests.php');
    exit;
}

$studentController = new StudentController();

try {
    // Check if has registered interest
    if (!$studentController->student->hasInterest($studentId, $programmeId)) {
        $_SESSION['info'] = 'You have not registered interest in this programme.';
    } else {
        // Withdraw interest
        $result = $studentController->withdrawInterest($programmeId, $studentId);
        if ($result) {
            $_SESSION['success'] = 'Interest successfully withdrawn.';
        } else {
            $_SESSION['error'] = 'Failed to withdraw interest. Please try again.';
        }
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the programme page or specified redirect URL
header('Location: ' . $redirectUrl);
exit;
