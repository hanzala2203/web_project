<?php

use App\Controllers\StaffController;
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Define constants
define('BASE_PATH', __DIR__);
define('BASE_URL', '/student-course-hub');

// Start session
session_start();

// Add console log

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

// Debug logging
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Path after processing: " . $path);

// Route the request
switch ($path) {
    case '':
    case '/':
        require_once BASE_PATH . '/src/views/home.php';
        break;
    
    // Handle programme creation - show form
    case '/admin/programmes/create':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = new \App\Controllers\AdminController();
            $admin->showCreateProgrammeForm();
        }
        break;

    // Handle programme creation - process form
    case '/admin/programmes':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = new \App\Controllers\AdminController();
            $admin->listProgrammes();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->createProgramme($_POST);
        }
        break;

    // Handle programme deletion
    case (preg_match('/^\/admin\/programmes\/(\d+)\/delete$/', $path, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->deleteProgramme($matches[1]);
        } else {
            header('Location: /student-course-hub/admin/programmes');
            exit;
        }
        break;

    // Auth routes
    case '/auth/login':
    case '/auth/login.php':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $auth = new \App\Controllers\AuthController();
                $auth->login($_POST['email'], $_POST['password']);
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }
        } else {
            if (isset($_SESSION['user_id'])) {
                // If already logged in, redirect to appropriate dashboard
                if ($_SESSION['role'] === 'admin') {
                    header('Location: ' . BASE_URL . '/admin/dashboard');
                } else if ($_SESSION['role'] === 'staff') {
                    header('Location: ' . BASE_URL . '/staff/dashboard');
                } else {
                    header('Location: ' . BASE_URL . '/student/dashboard');
                }
                exit;
            }
            require_once BASE_PATH . '/src/views/auth/login.php';
        }
        break;
        
    case '/auth/register':
    case '/auth/register.php':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $auth = new \App\Controllers\AuthController();
                $auth->register($_POST);
                $_SESSION['success'] = 'Registration successful! Please login.';
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: ' . BASE_URL . '/auth/register');
                exit;
            }
        } else {
            if (isset($_SESSION['user_id'])) {
                // If already logged in, redirect to appropriate dashboard
                if ($_SESSION['role'] === 'admin') {
                    header('Location: ' . BASE_URL . '/admin/dashboard');
                } else if ($_SESSION['role'] === 'staff') {
                    header('Location: ' . BASE_URL . '/staff/dashboard');
                } else {
                    header('Location: ' . BASE_URL . '/student/dashboard');
                }
                exit;
            }
            require_once BASE_PATH . '/src/views/auth/register.php';
        }
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
        break;
        
    case '/student/programme_details':
    case '/student/programme_details_new':
        try {
            if (!isset($_GET['id'])) {
                throw new \Exception('Programme ID is required');
            }
            $student = new \App\Controllers\StudentController();
            $student->viewProgrammeDetails($_GET['id']);
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
        break;
        
    case '/student/interests/handle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/views/student/handle_interest.php';
        } else {
            header('Location: ' . BASE_URL . '/student/explore_programmes');
        }
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

    case '/student/withdraw_interest':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $student = new \App\Controllers\StudentController();
            $student->handleWithdrawInterest();
        } else {
            header('Location: ' . BASE_URL . '/student/withdraw_interest');
            exit;
        }
        break;
        
    case '/staff/dashboard':
        $staff = new StaffController();
        $staff->dashboard();
        break;

    case '/staff/modules':
        $staff = new StaffController();
        $staff->listModules();
        break;    case (preg_match('/^\\/staff\\/modules\\/(\\d+)$/', $path, $matches) ? $path : ''):
        $staff = new StaffController();
        $staff->viewModule($matches[1]);
        break;    case (preg_match('/^\\/staff\\/modules\\/(\\d+)\\/students$/', $path, $matches) ? $path : ''):
        $staff = new StaffController();
        $staff->viewModuleStudents($matches[1]);
        break;

    // Admin routes
    case '/admin/dashboard':
    case '/admin/dashboard.php':
        require_once BASE_PATH . '/src/views/admin/dashboard.php';
        break;
    case preg_match('/^\/admin\/programmes\/(\d+)\/view$/', $path, $matches) ? true : false:
        echo "<script>console.log('yes');</script>";
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {  // Changed to GET
            $admin = new \App\Controllers\AdminController();
            $admin->viewProgramme($matches[1]);
            
        } else {
            
            echo "<script>console.log('yes');</script>";
            header('Location: ' . BASE_URL . '/admin/programmes/view');  // Added BASE_URL
        }
        break;

    // case preg_match('/^\/admin\/programme\/(\d+)\/view$/', $path, $matches) ? true : false:
    //     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //         $programmeId = (int)$matches[1];
    //         if ($programmeId > 0) {
    //             $admin = new \App\Controllers\AdminController();
    //             $admin->viewProgramme($programmeId);
    //         } else {
    //             http_response_code(400);
    //             $_SESSION['error'] = 'Invalid programme ID';
    //             header('Location: ' . BASE_URL . '/admin/programmes');
    //             exit();
    //         }
    //     } else {
    //         http_response_code(405);
    //         $_SESSION['error'] = 'Method not allowed. Use GET.';
    //         header('Location: ' . BASE_URL . '/admin/programmes');
    //         exit();
    //     }
    //     break;
    //     // break;
    
    // case (preg_match('/^\/admin\/programmes(\/create|\/edit|$)/', $path) ? $path : ''):
    //     // Only basic programme routes are handled in web.php
    //     require_once BASE_PATH . '/routes/web.php';
    //     break;

    case '/admin/programmes':
        
        $admin = new \App\Controllers\AdminController();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->listProgrammes();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // This will be used by the form submission from /admin/programmes/create
            $admin->createProgramme($_POST);
        }
        break;
        
    case '/admin/programmes/create':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = new \App\Controllers\AdminController();
            $admin->showCreateProgrammeForm();
        }
        break;

    case (preg_match('/^\/admin\/programmes\/(\d+)\/edit$/', $path, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin = new \App\Controllers\AdminController();
            $admin->showEditProgrammeForm($matches[1]);
        }
        break;

    // Handle programme update
    case (preg_match('/^\/admin\/programmes\/(\d+)\/update$/', $path, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            
            echo "<script>console.log('yes');</script>";
            $admin->updateProgramme($matches[1], $_POST);
        }
        break;

    case '/admin/modules':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->createModule($_POST);
        } else {
            $admin = new \App\Controllers\AdminController();
            $admin->listModules();
        }
        break;

    case '/admin/modules/create':
        require_once BASE_PATH . '/src/controllers/AdminController.php';
        $admin = new \App\Controllers\AdminController();
        $admin->createModuleForm();
        break;

    // Handle module deletion
    case preg_match('/^\/admin\/modules\/(\d+)\/delete$/', $path, $matches) ? true : false:
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->deleteModule($matches[1]);
        } else {
            header('Location: /student-course-hub/admin/modules');
        }
        break;

    // Handle module editing
    case preg_match('/^\/admin\/modules\/(\d+)\/edit$/', $path, $matches) ? true : false:
        require_once BASE_PATH . '/src/controllers/AdminController.php';
        $admin = new \App\Controllers\AdminController();
        $admin->showEditModuleForm($matches[1]);
        break;

    // Handle module update
    case preg_match('/^\/admin\/modules\/(\d+)\/update$/', $path, $matches) ? true : false:
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->updateModule($matches[1], $_POST);
        } else {
            header('Location: /student-course-hub/admin/modules');
        }
        break;
        
    case '/admin/students':
        require_once BASE_PATH . '/src/controllers/AdminController.php';
        $controller = new \App\Controllers\AdminController();
        $controller->listStudents();
        break;

    case '/admin/staff':
        $admin = new \App\Controllers\AdminController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->adminStoreStaff($_POST);
        } else {
            $admin->adminStaffList();
        }
        break;
        
    case '/admin/staff/create':
        $admin = new \App\Controllers\AdminController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->adminStoreStaff($_POST);
        } else {
            $admin->adminCreateStaffForm();
        }
        break;

    case preg_match('/^\/admin\/staff\/(\d+)\/update$/', $path, $matches) ? true : false:
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->adminUpdateStaff($matches[1], $_POST);
        } else {
            header('Location: /student-course-hub/admin/staff');
        }
        break;

    case preg_match('/^\/admin\/staff\/(\d+)\/delete$/', $path, $matches) ? true : false:
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin = new \App\Controllers\AdminController();
            $admin->adminDeleteStaff($matches[1]);
        } else {
            header('Location: /student-course-hub/admin/staff');
        }
        break;

    case preg_match('/^\/admin\/staff\/(\d+)\/edit$/', $path, $matches) ? true : false:
        $admin = new \App\Controllers\AdminController();
        $admin->adminEditStaffForm($matches[1]);
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
        
    case '/login':
        header('Location: ' . BASE_URL . '/auth/login');
        exit();
        break;
        
    case '/register':
        header('Location: ' . BASE_URL . '/auth/register');
        exit();
        break;
        
    case '/error':
        require_once BASE_PATH . '/src/views/error.php';
        break;

    default:
        header('HTTP/1.0 404 Not Found');
        require_once BASE_PATH . '/src/views/404.php';
        break;
}