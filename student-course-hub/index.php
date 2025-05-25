<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('BASE_PATH', __DIR__);
define('BASE_URL', '/student-course-hub');

// Load configurations
require_once BASE_PATH . '/src/config/config.php';
require_once BASE_PATH . '/src/config/database.php';

// Load controllers
require_once BASE_PATH . '/src/controllers/AuthController.php';
require_once BASE_PATH . '/src/controllers/CourseController.php';
require_once BASE_PATH . '/src/controllers/StudentController.php';

// Start session
session_start();

// Load controllers
$authController = new AuthController();
$courseController = new CourseController();
$studentController = new StudentController();

// Get the request URI
$request = $_SERVER['REQUEST_URI'];
$base = '/student-course-hub';
$path = str_replace($base, '', $request);
$path = rtrim($path, '/');

// Parse query string if exists
$queryPos = strpos($path, '?');
if ($queryPos !== false) {
    $path = substr($path, 0, $queryPos);
}

// Basic routing
switch ($path) {
    case '':
    case '/':
        require_once BASE_PATH . '/src/views/home.php';
        break;
    
    case '/auth/login':
    case '/auth/login.php':
        require_once BASE_PATH . '/src/views/auth/login.php';
        break;
        
    case '/auth/register':
    case '/auth/register.php':
        require_once BASE_PATH . '/src/views/auth/register.php';
        break;
        
    case '/auth/logout':
    case '/auth/logout.php':
        $authController->logout();
        header('Location: ' . BASE_URL . '/');
        exit();
        break;
        
    case '/student/dashboard':
    case '/student/dashboard.php':
        require_once BASE_PATH . '/src/views/student/dashboard.php';
        break;
        
    case '/admin/dashboard':
    case '/admin/dashboard.php':
        require_once BASE_PATH . '/src/views/admin/dashboard.php';
        break;
        
    case '/admin/database':
        require_once BASE_PATH . '/db_viewer.php';
        break;
        
    case '/admin/database':
        // Check if user is admin
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            require_once BASE_PATH . '/db_viewer.php';
        } else {
            header('Location: /auth/login');
            exit();
        }
        break;
        
    case '/courses':
        require_once BASE_PATH . '/src/views/student/courses.php';
        break;
        
    case '/admin/courses':
        require_once BASE_PATH . '/src/views/admin/courses.php';
        break;
        
    case '/admin/students':
        require_once BASE_PATH . '/src/views/admin/students.php';
        break;
        break;
        
    case '/login':
        require_once BASE_PATH . '/src/views/auth/login.php';
        break;
        
    case '/register':
        require_once BASE_PATH . '/src/views/auth/register.php';
        break;
        
    default:
        require_once BASE_PATH . '/views/404.php';
        break;
}