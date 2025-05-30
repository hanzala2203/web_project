<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('BASE_PATH', __DIR__);
define('BASE_URL', '/student-course-hub');

// Start session
session_start();

// Load configurations
require_once BASE_PATH . '/src/config/config.php';
require_once BASE_PATH . '/src/config/database.php';

// Load controllers
require_once BASE_PATH . '/src/controllers/AuthController.php';
require_once BASE_PATH . '/src/controllers/AdminController.php';
require_once BASE_PATH . '/src/controllers/StaffController.php';
require_once BASE_PATH . '/src/controllers/StudentController.php';

// Include base model and models
require_once BASE_PATH . '/src/models/Model.php';
require_once BASE_PATH . '/src/models/User.php';
require_once BASE_PATH . '/src/models/Programme.php';
require_once BASE_PATH . '/src/models/Module.php';
require_once BASE_PATH . '/src/models/Staff.php';
require_once BASE_PATH . '/src/models/Student.php';

// Parse request path
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace(BASE_URL, '', $request);

// Remove .php extension if present
$path = preg_replace('/\.php$/', '', $path);

// Route the request
switch ($path) {
    case '':
    case '/':
        require_once BASE_PATH . '/src/views/home.php';
        break;
    
    // Auth routes
    case '/auth/login':
    case '/auth/login.php':
        require_once BASE_PATH . '/src/views/auth/login.php';
        break;
        
    case '/auth/register':
    case '/auth/register.php':
        require_once BASE_PATH . '/src/views/auth/register.php';
        break;
        
    // Student routes
    case '/student/dashboard':
    case '/student/dashboard.php':
        $student = new \App\Controllers\StudentController();
        $student->viewDashboardNew(); // Using new modern dashboard
        break;
        
    case '/student/explore_programmes':
    case '/student/explore_programmes.php':
        $student = new \App\Controllers\StudentController();
        $student->exploreProgrammes();
        require_once BASE_PATH . '/src/views/student/programmes_new.php';
        break;
        
    case '/student/programme_details':
    case '/student/programme_details_new':
        if (!isset($_GET['id'])) {
            require BASE_PATH . '/src/views/404.php';
            break;
        }
        require BASE_PATH . '/src/views/student/programme_details_new.php';
        break;
        
    case '/student/register_interest':
    case '/student/register_interest.php':
        if (!isset($_POST['programme_id'])) {
            header('Location: ' . BASE_URL . '/student/explore_programmes');
            exit;
        }
        $student = new \App\Controllers\StudentController();
        $student->registerInterest($_POST['programme_id'], $_SESSION['user_id']);
        break;
        
    case '/student/manage_interests':
    case '/student/manage_interests.php':
        $student = new \App\Controllers\StudentController();
        $student->viewInterests();
        break;
        
    // Admin routes
    case '/admin/dashboard':
    case '/admin/dashboard.php':
        require_once BASE_PATH . '/src/views/admin/dashboard.php';
        break;
        
    case '/admin/programmes':
        require_once BASE_PATH . '/src/views/admin/programmes.php';
        break;
        
    case '/admin/modules':
        require_once BASE_PATH . '/src/views/admin/modules.php';
        break;
        
    case '/admin/students':
        require_once BASE_PATH . '/src/views/admin/students.php';
        break;
        
    case '/admin/staff':
        require_once BASE_PATH . '/src/views/admin/staff.php';
        break;
        
    case '/auth/logout':
    case '/auth/logout.php':
        $auth = new \App\Controllers\AuthController();
        $auth->logout();
        header('Location: ' . BASE_URL . '/');
        exit();
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
        
    case '/error':
        require_once BASE_PATH . '/src/views/error.php';
        break;

    default:
        header('HTTP/1.0 404 Not Found');
        require_once BASE_PATH . '/src/views/404.php';
        break;
}