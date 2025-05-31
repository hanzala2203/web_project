<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\StaffController;

// Get the request path
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = '/student-course-hub';
$path = str_replace($base, '', $request);
$path = rtrim($path, '/');
if (empty($path)) $path = '/';

// Handle .php extension
if (substr($path, -4) === '.php') {
    $path = substr($path, 0, -4);
}

// Initialize controllers
$admin = new AdminController();
$auth = new AuthController();

// Basic router
switch ($path) {
    case '/admin':    case '/admin/dashboard':
        $admin->dashboard();
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

    case (preg_match('/^\\/staff\\/modules\\/(\\d+)\\/students\\/(\\d+)\\/details$/', $path, $matches) ? $path : ''):
        $staff = new StaffController();
        $staff->viewStudentDetails($matches[1], $matches[2]);
        break;

    case (preg_match('/^\\/staff\\/programmes\\/(\\d+)$/', $path, $matches) ? $path : ''):
        $staff = new StaffController();
        $staff->viewProgramme($matches[1]);
        break;

    case '/admin/programmes':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->listProgrammes();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // This will be used by the form submission from /admin/programmes/create
            $admin->createProgramme($_POST);
        }
        break;

    case '/admin/programmes/create': // New route for displaying the create form
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->showCreateProgrammeForm(); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/programmes/{id}/edit
    case (preg_match('/^\\/admin\\/programmes\\/(\\d+)\\/edit$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->showEditProgrammeForm($matches[1]); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/programmes/{id}/update
    case (preg_match('/^\\/admin\\/programmes\\/(\\d+)\\/update$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->updateProgramme($matches[1], $_POST); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/programmes/{id}/delete
    case (preg_match('/^\\/admin\\/programmes\\/(\\d+)\\/delete$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->deleteProgramme($matches[1]); // Assumes a method in AdminController
        }
        break;

    case '/admin/modules':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->listModules();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // This will be used by the form submission from /admin/modules/create
            $admin->createModule($_POST);
        }
        break;

    case '/admin/modules/create': // New route for displaying the create form
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->showCreateModuleForm(); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/modules/{id}/edit
    case (preg_match('/^\\/admin\\/modules\\/(\\d+)\\/edit$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->showEditModuleForm($matches[1]); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/modules/{id}/update
    case (preg_match('/^\\/admin\\/modules\\/(\\d+)\\/update$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->updateModule($matches[1], $_POST); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/modules/{id}/delete
    case (preg_match('/^\\/admin\\/modules\\/(\\d+)\\/delete$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->deleteModule($matches[1]); // Assumes a method in AdminController
        }
        break;

    case '/admin/students':
        $admin->listStudents();
        break;

    case '/admin/staff':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->adminStaffList();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->adminStoreStaff($_POST);
        }
        break;

    case '/admin/staff/create':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->adminCreateStaffForm();
        }
        break;

    // Regex for matching /admin/staff/{id}/edit
    case (preg_match('/^\\/admin\\/staff\\/(\\d+)\\/edit$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->adminEditStaffForm($matches[1]);
        }
        break;

    // Regex for matching /admin/staff/{id}/update
    case (preg_match('/^\\/admin\\/staff\\/(\\d+)\\/update$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->adminUpdateStaff($matches[1], $_POST);
        }
        break;

    // Regex for matching /admin/staff/{id}/delete
    case (preg_match('/^\\/admin\\/staff\\/(\\d+)\\/delete$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->adminDeleteStaff($matches[1]);
        }
        break;    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login($_POST['email'], $_POST['password']);
        } else {
            include BASE_PATH . '/src/views/auth/login.php';
        }
        break;

    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register($_POST);
        } else {
            include BASE_PATH . '/src/views/auth/register.php';
        }
        break;

    case '/logout':
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $auth->logout();
        header('Location: /student-course-hub/login');
        exit();
        break;

    // API routes
    case '/api/programmes/filter':
        $student = new \App\Controllers\StudentController();
        $student->getFilteredProgrammes();
        break;
        
    // Student routes    case '/student/explore_programmes':
    case '/student/explore_programmes.php':
        $student = new \App\Controllers\StudentController();
        $student->exploreProgrammes();
        require_once BASE_PATH . '/src/views/student/explore_programmes.php';
        break;    case '/student/dashboard':
        $student = new \App\Controllers\StudentController();
        $student->viewDashboard();
        break;

    case '/student/programme_details':
    case '/student/programme_details_new':
        $student = new \App\Controllers\StudentController();
        $student->viewProgrammeDetails($_GET['id'] ?? null);
        break;

    case '/student/programme_details_debug':
        require_once BASE_PATH . '/src/views/student/programme_details_debug.php';
        break;

    case '/student/manage_interests':
        require_once BASE_PATH . '/src/views/student/manage_interests.php';
        break;

    case '/student/register_interest':
        require_once BASE_PATH . '/src/views/student/register_interest.php';
        break;

    // Default route for 404    default:
        // Handle 404
        http_response_code(404);
        include BASE_PATH . '/src/views/404.php';
        break;
}
