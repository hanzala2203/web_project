<?php

namespace App\Middleware;

class StudentAuthMiddleware {
    public static function authenticate() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in and is a student
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
            $_SESSION['error'] = "Please login as a student to access this page.";
            header('Location: /student-course-hub/auth/login');
            exit();
        }
    }
}
