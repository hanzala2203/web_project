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

// Start session
session_start();

// Get the request URI
$request = $_SERVER['REQUEST_URI'];
$base = '/student-course-hub';
$path = str_replace($base, '', $request);
$path = rtrim($path, '/');

// Basic routing
switch ($path) {
    case '':
    case '/':
        require_once BASE_PATH . '/src/views/home.php';
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