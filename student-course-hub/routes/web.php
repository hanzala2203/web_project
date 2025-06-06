<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\StaffController;
use App\Controllers\StudentController;

// Parse request path to match index.php behavior
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace(BASE_URL, '', $request);

// Remove .php extension if present
$path = preg_replace('/\.php$/', '', $path);

// Handle .php extension
if (substr($path, -4) === '.php') {
    $path = substr($path, 0, -4);
}

// Initialize controllers
$admin = new AdminController();
$auth = new AuthController();
$student = new StudentController();

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
        break;    // Regex for matching /admin/programmes/{id}/view - MUST COME BEFORE THE GENERIC /admin/programmes route
     // Regex for matching /admin/programmes/{id}/view
    // case (preg_match('/^\/admin\/programmes\/(\d+)\/view$/', $path, $matches) ? $path : ''):
    //     if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            
    //         echo "<script>console.log('yes');</script>";
    //         $admin->viewProgramme($matches[1]); 
    //     }
        
    //         echo "<script>console.log('yes');</script>";
    //     break;

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
    case (preg_match('/^\/admin\/programmes\/(\d+)\/edit$/', $path, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $admin->showEditProgrammeForm($matches[1]); // Assumes a method in AdminController
        }
        break;

    // Regex for matching /admin/programmes/{id}/update
    case (preg_match('/^\/admin\/programmes\/(\d+)\/update$/', $path, $matches) ? true : false):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            echo "<script>console.log('yes2');</script>";
            $admin->updateProgramme($matches[1], $_POST);
        }
        break;
    case (preg_match('/^\\/admin\\/programmes\\/(\\d+)\\/delete$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->deleteProgramme($matches[1]); // Assumes a method in AdminController
        }       
        break;
    
    // Regex for matching /admin/programmes/{id}/publish
    case (preg_match('/^\\/admin\\/programmes\\/(\\d+)\\/publish$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->publishProgramme($matches[1]);
        }
        break;

    // Regex for matching /admin/programmes/{id}/unpublish
    case (preg_match('/^\\/admin\\/programmes\\/(\\d+)\\/unpublish$/', $path, $matches) ? $path : ''):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $admin->unpublishProgramme($matches[1]);
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

    case '/student/register_interest_api':
    case '/student/register_interest_api.php':
        require_once BASE_PATH . '/student/register_interest_api.php';
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
    case '/student/interests/handle':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once BASE_PATH . '/src/views/student/handle_interest.php';
        }
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

    // Default route for 404
    default:
        // Handle 404
        http_response_code(404);
        include BASE_PATH . '/src/views/404.php';
        break;
}
