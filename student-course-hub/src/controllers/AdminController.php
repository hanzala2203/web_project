<?php

namespace App\Controllers;

use App\Models\Programme;
use App\Models\Module;
use App\Models\Staff;
use App\Models\Student;
use App\Utils\Security;
use App\Utils\ImageValidator;
use Exception;

class AdminController
{
    private $programme;
    private $module;
    private $staff;
    private $student;
    private $user; // Add User model instance

    public function __construct() {
        $this->programme = new Programme();
        $this->module = new Module();
        $this->staff = new Staff();
        $this->student = new Student();
        $this->user = new \App\Models\User(); // Instantiate User model
    }

    // Dashboard
    public function dashboard()
    {
        $stats = $this->getDashboardStats();
        include BASE_PATH . '/src/views/admin/dashboard.php';
    }

    // Programme Management
    public function listProgrammes()
    {
        $programmes = $this->programme->getAllProgrammes();
        include BASE_PATH . '/src/views/admin/programmes.php';
    }

    public function showCreateProgrammeForm()
    {
        // Clear any previous success/error messages that aren't related to form submission
        if (!isset($_SESSION['form_errors'])) {
            unset($_SESSION['success_message'], $_SESSION['error_message']);
        }

        // Load the create form view
        include BASE_PATH . '/src/views/admin/programmes/create.php';
    }

    private function validateProgrammeData($data, $isUpdate = false) 
    {
        $errors = [];
        
        // Required fields
        if (empty($data['title'])) $errors['title'] = "Title is required.";
        if (empty($data['level'])) $errors['level'] = "Level is required.";
        if (empty($data['duration'])) $errors['duration'] = "Duration is required.";
        
        // Validate level values
        $allowedLevels = ['undergraduate', 'postgraduate', 'doctorate'];
        if (!empty($data['level']) && !in_array(strtolower($data['level']), $allowedLevels)) {
            $errors['level'] = "Invalid level. Must be one of: " . implode(', ', $allowedLevels);
        }
        
        // Validate duration format (e.g., "3 years", "2 semesters", etc.)
        if (!empty($data['duration']) && !preg_match('/^\d+\s*(year|years|semester|semesters)$/i', $data['duration'])) {
            $errors['duration'] = "Invalid duration format. Example: '3 years' or '2 semesters'";
        }
        
        // Title uniqueness check (only for new programmes or if title changed)
        if (!$isUpdate || ($isUpdate && isset($data['original_title']) && $data['title'] !== $data['original_title'])) {
            if ($this->programme->titleExists($data['title'], $isUpdate ? $data['id'] : null)) {
                $errors['title'] = "A programme with this title already exists.";
            }
        }
        
        return $errors;
    }

    public function createProgramme($data)
    {
        error_log("AdminController: createProgramme START ===");
        error_log("Time: " . date('Y-m-d H:i:s'));
        error_log("Raw data received: " . print_r($data, true));
        
        $errors = $this->validateProgrammeData($data);
        
        if (!empty($errors)) {
            error_log("AdminController: Validation errors found: " . print_r($errors, true));
            $_SESSION['error_message'] = "Please correct the errors below.";
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            header('Location: /student-course-hub/admin/programmes/create');
            exit;
        }
        
        try {
            // Sanitize and prepare data
            $programmeData = [
                'title' => htmlspecialchars($data['title']),
                'description' => htmlspecialchars($data['description'] ?? ''),
                'level' => strtolower(htmlspecialchars($data['level'])),
                'duration' => htmlspecialchars($data['duration']),
                'is_published' => isset($data['is_published']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            error_log("AdminController: Prepared data for creation: " . print_r($programmeData, true));
            
            $result = $this->programme->create($programmeData);
            error_log("AdminController: Programme creation result: " . ($result ? "success with ID: $result" : "failed"));
            
            if ($result) {
                $_SESSION['success_message'] = "Programme created successfully.";
                header('Location: /student-course-hub/admin/programmes');
            } else {
                throw new Exception("Failed to create programme - no ID returned");
            }
            exit;
        }
        catch (Exception $e) {
            error_log("AdminController: Error creating programme: " . $e->getMessage());
            error_log("AdminController: Stack trace: " . $e->getTraceAsString());
            $_SESSION['error_message'] = "Error creating programme: " . $e->getMessage();
            $_SESSION['form_data'] = $data;
            header('Location: /student-course-hub/admin/programmes/create');
            exit;
        }
    }

    public function showEditProgrammeForm($id)
    {
        $programme = $this->programme->findById($id);
        if (!$programme) {
            // Handle not found - perhaps show a 404 page
            echo "Programme not found.";
            return;
        }
        // You'll need to create this view file: /src/views/admin/programmes/edit.php
        // Pass the $programme data to the view
        include BASE_PATH . '/src/views/admin/programmes/edit.php';
    }

    public function updateProgramme($id, $data)
    {
        // First validate if programme exists
        $programme = $this->programme->findById($id);
        if (!$programme) {
            $_SESSION['error_message'] = "Programme not found.";
            header('Location: /student-course-hub/admin/programmes');
            exit;
        }

        $errors = [];
        // Validate required fields
        if (empty($data['title'])) $errors['title'] = "Title is required.";
        if (empty($data['level'])) $errors['level'] = "Level is required.";
        if (empty($data['duration'])) $errors['duration'] = "Duration is required.";

        if (!empty($errors)) {
            $_SESSION['error_message'] = "Please correct the errors below.";
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            header('Location: /student-course-hub/admin/programmes/' . $id . '/edit');
            exit;
        }

        try {
            // Sanitize and prepare data
            $programmeData = [
                'id' => $id,
                'title' => htmlspecialchars($data['title']),
                'description' => htmlspecialchars($data['description'] ?? ''),
                'level' => htmlspecialchars($data['level']),
                'duration' => htmlspecialchars($data['duration']),
                'is_published' => isset($data['is_published']) ? 1 : 0
            ];

            $this->programme->update($programmeData);
            $_SESSION['success_message'] = "Programme updated successfully.";
            header('Location: /student-course-hub/admin/programmes');
            exit;
        } catch (Exception $e) {
            error_log("Error updating programme: " . $e->getMessage());
            $_SESSION['error_message'] = "Error updating programme. Please try again.";
            $_SESSION['form_data'] = $data;
            header('Location: /student-course-hub/admin/programmes/' . $id . '/edit');
            exit;
        }
    }

    // public function viewProgramme($id)
    // {
    //     try {
    //         $programme = $this->programme->findById($id);

    //         if (!$programme) {
    //             $_SESSION['error_message'] = "Programme not found.";
    //             header('Location: /student-course-hub/admin/programmes');
    //             exit;
    //         }

    //         // Get all modules associated with this programme
    //         $modules = $this->module->getByProgramme($id);
    //         $programme['modules'] = $modules;

    //         // Pass the data to the view
    //         include BASE_PATH . '/src/views/admin/programmes/view.php';
    //     } catch (Exception $e) {
    //         error_log("Error viewing programme: " . $e->getMessage());
    //         $_SESSION['error_message'] = "Error viewing programme: " . $e->getMessage();
    //         header('Location: /student-course-hub/admin/programmes');
    //         exit;
    //     }
    // }


    public function viewProgramme($id)
{
    try {
        // Log the programme ID being processed
        echo "<script>console.log('viewProgramme: Starting with programme ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "');</script>";

        $programme = $this->programme->findById($id);
        
        if (!$programme) {
            // Log when programme is not found
            echo "<script>console.log('viewProgramme: Programme not found for ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "');</script>";
            $_SESSION['error_message'] = "Programme not found.";
            header('Location: /student-course-hub/admin/programmes');
            exit;
        }

        // Log the programme details
        echo "<script>console.log('viewProgramme: Programme found - Title: " . htmlspecialchars($programme['title'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') . ", Data: ', " . json_encode($programme) . ");</script>";

        // Get all modules associated with this programme
        $modules = $this->module->getByProgramme($id);

        // Log the number of modules retrieved
        echo "<script>console.log('viewProgramme: Retrieved ' + " . count($modules) . " + ' modules for programme ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "');</script>";
        // Log the modules data
        echo "<script>console.log('viewProgramme: Modules data: ', " . json_encode($modules) . ");</script>";

        $programme['modules'] = $modules;

        // Log before rendering the view
        echo "<script>console.log('viewProgramme: Rendering view for programme ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "');</script>";

        include BASE_PATH . '/src/views/admin/programmes/view.php';
    } catch (Exception $e) {
        // Log the error
        $errorMessage = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "<script>console.log('viewProgramme: Error for programme ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . " - Error: " . $errorMessage . "');</script>";
        
        $_SESSION['error_message'] = "Error viewing programme: " . $e->getMessage();
        header('Location: /student-course-hub/admin/programmes');
        exit;
    }
}
    public function deleteProgramme($id)
    {
        try {
            error_log("Starting programme deletion process for ID: " . $id);
            
            // First check if programme exists
            $programme = $this->programme->findById($id);
            if (!$programme) {
                error_log("Programme not found with ID: " . $id);
                $_SESSION['error_message'] = "Programme not found.";
                header('Location: /student-course-hub/admin/programmes');
                exit;
            }
            error_log("Programme found: " . json_encode($programme));

            // Check for enrolled students
            $hasStudents = $this->programme->hasEnrolledStudents($id);
            error_log("Has enrolled students check result: " . ($hasStudents ? 'true' : 'false'));
            if ($hasStudents) {
                $_SESSION['error_message'] = "Cannot delete programme with enrolled students.";
                header('Location: /student-course-hub/admin/programmes');
                exit;
            }

            // Attempt deletion
            error_log("Attempting to delete programme with ID: " . $id);
            $deleteResult = $this->programme->delete($id);
            if ($deleteResult) {
                error_log("Programme successfully deleted");
                $_SESSION['success_message'] = "Programme deleted successfully.";
            } else {
                error_log("Programme deletion returned false");
                throw new \Exception("Programme deletion failed");
            }
            
            header('Location: /student-course-hub/admin/programmes');
            exit;
        } catch (\Exception $e) {
            error_log("Error in deleteProgramme: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error_message'] = "Error deleting programme. Please try again.";
            header('Location: /student-course-hub/admin/programmes');
            exit;
        }
    }

    // // Student Management
    // public function listStudents()
    // {
    //     try {
    //         // Start with debugging info
    //         error_log("\n\n=== AdminController::listStudents START ===");
    //         error_log("Time: " . date('Y-m-d H:i:s'));
    //         error_log("Session data: " . print_r($_SESSION, true));
            
    //         // Initialize variables
    //         $filters = [];
    //         $search = '';
    //         $selectedProgramme = '';
            
    //         // Get search filters from GET parameters
    //         if (!empty($_GET['search'])) {
    //             $filters['search'] = $_GET['search'];
    //             $search = $_GET['search'];
    //             error_log("Search filter: " . $_GET['search']);
    //         }
            
    //         if (!empty($_GET['programme'])) {
    //             $filters['programme'] = $_GET['programme'];
    //             $selectedProgramme = $_GET['programme'];
    //             error_log("Programme filter: " . $_GET['programme']);
    //         }
            
    //         // Handle data export
    //         if (isset($_GET['action']) && $_GET['action'] === 'export') {
    //             error_log("Handling export request");
    //             return $this->exportStudentData($filters);
    //         }
            
    //         // Fetch students with error handling
    //         error_log("About to call getAllStudents with filters: " . print_r($filters, true));
    //         $students = $this->student->getAllStudents($filters);
            
    //         // Debug the returned data
    //         error_log("Raw students data returned: " . print_r($students, true));
            
    //         if ($students === false || $students === null) {
    //             error_log("getAllStudents() returned false or null");
    //             $students = [];
    //         }
    //         error_log("Found " . count($students) . " students");
            
    //         // Verify student data structure
    //         if (!empty($students)) {
    //             error_log("First student data: " . print_r($students[0], true));
    //         }
            
    //         // Fetch programmes with error handling
    //         try {
    //             $programmes = $this->programme->getAllProgrammes();
    //             if ($programmes === false || $programmes === null) {
    //                 error_log("getAllProgrammes() returned false or null");
    //                 $programmes = [];
    //             }
    //         } catch (\Exception $e) {
    //             error_log("Error fetching programmes: " . $e->getMessage());
    //             $programmes = [];
    //         }
            
    //         error_log("Found " . count($programmes) . " programmes");
            
    //         // Debug what we're passing to the view
    //         error_log("Passing to view - students: " . count($students) . ", programmes: " . count($programmes));
    //         error_log("Search: " . $search . ", Selected Programme: " . $selectedProgramme);
            
    //         error_log("About to include view with data:");
    //         error_log("students count: " . count($students));
    //         error_log("programmes count: " . count($programmes));
    //         error_log("search: " . $search);
    //         error_log("selectedProgramme: " . $selectedProgramme);
            
    //         // Ensure all variables are defined
    //         $pageTitle = "Manage Students";
            
    //         // Extract all variables into local scope
    //         extract([
    //             'students' => $students,
    //             'programmes' => $programmes,
    //             'search' => $search,
    //             'selectedProgramme' => $selectedProgramme,
    //             'pageTitle' => $pageTitle
    //         ]);
            
    //         // Include with explicit scope
    //         include BASE_PATH . '/src/views/admin/students.php';
            
    //     } catch (\Exception $e) {
    //         error_log("Error in AdminController::listStudents: " . $e->getMessage());
    //         $_SESSION['error_message'] = "Error loading students: " . $e->getMessage();
    //         include BASE_PATH . '/src/views/admin/students.php';
    //     }
    // }
    public function listStudents()
    {
        try {
            // Start with debugging info
            echo "<script>console.log('=== AdminController::listStudents START ===');</script>";
            echo "<script>console.log('Time: " . date('Y-m-d H:i:s') . "');</script>";
            echo "<script>console.log('Session data: " . addslashes(json_encode($_SESSION)) . "');</script>";
            error_log("\n\n=== AdminController::listStudents START ===");
            error_log("Time: " . date('Y-m-d H:i:s'));
            error_log("Session data: " . print_r($_SESSION, true));
            
            // Initialize variables
            $filters = [];
            $search = '';
            $selectedProgramme = '';
            
            // Get search filters from GET parameters
            if (!empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
                $search = $_GET['search'];
                echo "<script>console.log('Search filter: " . addslashes($_GET['search']) . "');</script>";
                error_log("Search filter: " . $_GET['search']);
            }
            
            if (!empty($_GET['programme'])) {
                $filters['programme'] = $_GET['programme'];
                $selectedProgramme = $_GET['programme'];
                echo "<script>console.log('Programme filter: " . addslashes($_GET['programme']) . "');</script>";
                error_log("Programme filter: " . $_GET['programme']);
            }
            
            // Handle data export
            if (isset($_GET['action']) && $_GET['action'] === 'export') {
                echo "<script>console.log('Handling export request');</script>";
                error_log("Handling export request");
                return $this->exportStudentData($filters);
            }
            
            // Fetch students with error handling
            echo "<script>console.log('About to call getAllStudents with filters: " . addslashes(json_encode($filters)) . "');</script>";
            error_log("About to call getAllStudents with filters: " . print_r($filters, true));
            $students = $this->student->getAllStudents($filters);
            
            // Debug the returned data
            echo "<script>console.log('Raw students data returned: " . addslashes(json_encode($students)) . "');</script>";
            error_log("Raw students data returned: " . print_r($students, true));
            
            if ($students === false || $students === null) {
                echo "<script>console.log('getAllStudents() returned false or null');</script>";
                error_log("getAllStudents() returned false or null");
                $students = [];
            }
            echo "<script>console.log('Found " . count($students) . " students');</script>";
            error_log("Found " . count($students) . " students");
            
            // Verify student data structure
            if (!empty($students)) {
                echo "<script>console.log('First student data: " . addslashes(json_encode($students[0])) . "');</script>";
                error_log("First student data: " . print_r($students[0], true));
            }
            
            // Fetch programmes with error handling
            try {
                $programmes = $this->programme->getAllProgrammes(true);
                if ($programmes === false || $programmes === null) {
                    echo "<script>console.log('getAllProgrammes() returned false or null');</script>";
                    error_log("getAllProgrammes() returned false or null");
                    $programmes = [];
                }
            } catch (\Exception $e) {
                echo "<script>console.log('Error fetching programmes: " . $e->getMessage() . "');</script>";
                error_log("Error fetching programmes: " . $e->getMessage());
                $programmes = [];
            }
            
            echo "<script>console.log('Found " . count($programmes) . " programmes');</script>";
            error_log("Found " . count($programmes) . " programmes");
            
            // Debug what we’re passing to the view
            echo "<script>console.log('Passing to view - students: " . count($students) . ", programmes: " . count($programmes) . "');</script>";
            echo "<script>console.log('Search: " . addslashes($search) . ", Selected Programme: " . addslashes($selectedProgramme) . "');</script>";
            error_log("Passing to view - students: " . count($students) . ", programmes: " . count($programmes));
            error_log("Search: " . $search . ", Selected Programme: " . $selectedProgramme);
            
            error_log("About to include view with data:");
            echo "<script>console.log('About to include view with data:');</script>";
            echo "<script>console.log('students count: ' + " . count($students) . ");</script>";
            echo "<script>console.log('programmes count: ' + " . count($programmes) . ");</script>";
            echo "<script>console.log('search: " . addslashes($search) . "');</script>";
            echo "<script>console.log('selectedProgramme: " . addslashes($selectedProgramme) . "');</script>";
            error_log("students count: " . count($students));
            error_log("programmes count: " . count($programmes));
            error_log("search: " . $search);
            error_log("selectedProgramme: " . $selectedProgramme);
            
            // Ensure all variables are defined
            $pageTitle = "Manage Students";
            
            // Extract all variables into local scope
            extract([
                'students' => $students,
                'programmes' => $programmes,
                'search' => $search,
                'selectedProgramme' => $selectedProgramme,
                'pageTitle' => $pageTitle
            ]);
            
            // Include with explicit scope
            include BASE_PATH . '/src/views/admin/students.php';
            
        } catch (\Exception $e) {
            echo "<script>console.log('Error in AdminController::listStudents: " . addslashes($e->getMessage()) . "');</script>";
            error_log("Error in AdminController::listStudents: " . $e->getMessage());
            $_SESSION['error_message'] = "Error loading students: " . $e->getMessage();
            include BASE_PATH . '/src/views/admin/students.php';
        }
    }
    private function exportStudentData($filters = [])
    {
        $students = $this->student->getAllStudents($filters);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students_export_' . date('Y-m-d') . '.csv"');
        
        // Create output handle
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['ID', 'Username', 'Email', 'Interested Programmes', 'Registration Date']);
        
        // Add data
        foreach ($students as $student) {
            fputcsv($output, [
                $student['id'],
                $student['username'],
                $student['email'],
                $student['interests'] ?: 'None',
                $student['created_at']
            ]);
        }
        
        fclose($output);
        exit();
    }

    public function getStudentDetails(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $student = $this->student->findById($id);
        
        if (!$student) {
            throw new \Slim\Exception\HttpNotFoundException($request);
        }

        $enrolledCourses = $this->student->getEnrolledCourses($id);
        
        return $this->view->render($response, 'admin/students/details.twig', [
            'student' => $student,
            'courses' => $enrolledCourses
        ]);
    }

    // Module Management
    public function listModules() 
    {
        try {
            error_log("=== ADMIN MODULES LIST START ===");
            error_log("Time: " . date('Y-m-d H:i:s'));
            
            // Check admin authentication
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                error_log("Access denied - not an admin user. Session: " . print_r($_SESSION, true));
                header('Location: /student-course-hub/login');
                exit;
            }
            error_log("Admin authentication passed, user_id: " . $_SESSION['user_id']);
            
            if (!$this->module) {
                error_log("Module model is not initialized in AdminController");
                throw new \Exception("Module model not initialized");
            }
            
            $modules = $this->module->getAllModules();
            if (!$modules) {
                error_log("No modules returned from getAllModules");
                $modules = [];
            } else {
                error_log("Got " . count($modules) . " modules from getAllModules");
                error_log("First module: " . print_r($modules[0] ?? null, true));
            }
            
            // Ensure $modules variable is available to the included view
            include BASE_PATH . '/src/views/admin/modules.php';
            
        } catch (\Exception $e) {
            error_log("Error in AdminController::listModules: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error_message'] = "Error loading modules: " . $e->getMessage();
            include BASE_PATH . '/src/views/admin/modules.php';
        }
    }

    public function showCreateModuleForm()
    {
        $programmes = $this->programme->getAllProgrammes();
        $staffList = $this->staff->getAllStaff();
        // Clear previous form data and errors if any
        $data = $_SESSION['form_data'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_data'], $_SESSION['form_errors']);

        include BASE_PATH . '/src/views/admin/modules/create.php';
    }

    public function createModuleForm()
    {
        try {
            $programmes = $this->programme->getAllProgrammes();
            $staffList = $this->staff->getAllStaff();
            
            // Clear previous form data and errors if any
            $data = $_SESSION['form_data'] ?? [];
            $errors = $_SESSION['form_errors'] ?? [];
            unset($_SESSION['form_data'], $_SESSION['form_errors']);

            include BASE_PATH . '/src/views/admin/modules/create.php';
        } catch (\Exception $e) {
            error_log("Error in createModuleForm: " . $e->getMessage());
            $_SESSION['error_message'] = "Error loading module creation form: " . $e->getMessage();
            header('Location: /student-course-hub/admin/modules');
            exit;
        }
    }

    public function createModule($data)
    {
        try {
            $errors = [];
            if (empty($data['title'])) $errors['title'] = "Title is required.";
            if (empty($data['credits'])) $errors['credits'] = "Credits are required.";
            if (!is_numeric($data['credits']) || $data['credits'] <= 0) $errors['credits'] = "Credits must be a positive number.";
            if (empty($data['programme_id'])) $errors['programme_id'] = "Programme is required.";
            if (empty($data['year_of_study'])) $errors['year_of_study'] = "Year of Study is required.";
            if (!is_numeric($data['year_of_study']) || $data['year_of_study'] <= 0) $errors['year_of_study'] = "Year of Study must be a positive number.";
            if (!empty($data['semester']) && (!is_numeric($data['semester']) || $data['semester'] < 1 || $data['semester'] > 2)) {
                $errors['semester'] = "Semester must be 1 or 2";
            }

            if (!empty($errors)) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $data;
                $_SESSION['error_message'] = "Please correct the errors below.";
                header('Location: /student-course-hub/admin/modules/create');
                exit;
            }
            
            $moduleId = $this->module->createModule(
                $data['title'], 
                $data['description'] ?? '', 
                $data['credits'],
                $data['year_of_study'] ?? null,
                $data['programme_id'],
                $data['staff_id'] ?? null,
                $data['semester'] ?? 1
            );

            if ($moduleId) {
                $_SESSION['success_message'] = "Module created successfully.";
                header('Location: /student-course-hub/admin/modules');
            } else {
                throw new \Exception("Failed to create module");
            }
            exit;
        } catch (\Exception $e) {
            error_log("Error creating module: " . $e->getMessage());
            $_SESSION['error_message'] = "Error creating module: " . $e->getMessage();
            $_SESSION['form_data'] = $data;
            header('Location: /student-course-hub/admin/modules/create');
            exit;
        }
    }

    public function showEditModuleForm($id)
    {
        $module = $this->module->getModuleById($id);
        if (!$module) {
            $_SESSION['error_message'] = "Module not found.";
            header('Location: /student-course-hub/admin/modules');
            exit;
        }
        $programmes = $this->programme->getAllProgrammes();
        $staffList = $this->staff->getAllStaff();
        
        // Merge module data with session form data if validation failed previously
        $formData = $_SESSION['form_data'] ?? $module;
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_data'], $_SESSION['form_errors']);

        include BASE_PATH . '/src/views/admin/modules/edit.php';
    }

    public function updateModule($id, $data)
    {
        $errors = [];
        if (empty($data['title'])) $errors['title'] = "Title is required.";
        if (empty($data['credits'])) $errors['credits'] = "Credits are required.";
        if (empty($data['programme_id'])) $errors['programme_id'] = "Programme is required.";
        // Add more validation as needed

        if (!empty($errors)) {
            $_SESSION['error_message'] = "Please correct the errors below.";
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            header('Location: /student-course-hub/admin/modules/' . $id . '/edit');
            exit;
        }
        
        try {
            $this->module->updateModule(
                $id, 
                $data['title'], 
                $data['description'] ?? '', 
                $data['credits'],
                $data['year_of_study'] ?? null,
                $data['programme_id'],
                $data['staff_id'] ?? null
            );
            $_SESSION['success_message'] = "Module updated successfully.";
            header('Location: /student-course-hub/admin/modules');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Error updating module: " . $e->getMessage();
            $_SESSION['form_data'] = $data;
            header('Location: /student-course-hub/admin/modules/' . $id . '/edit');
            exit;
        }
    }

    // Staff Management Methods (Corrected and Renamed to avoid conflicts)

    public function adminStaffList() // Renamed from listStaff
    {
        $staffList = $this->staff->getAllStaff();
        // Prepare data for the view, including any session messages
        $successMessage = $_SESSION['success_message'] ?? null;
        $errorMessage = $_SESSION['error_message'] ?? null;
        unset($_SESSION['success_message'], $_SESSION['error_message']);

        include BASE_PATH . '/src/views/admin/staff/index.php';
    }

    public function adminCreateStaffForm()
    {
        $data = $_SESSION['form_data'] ?? [];
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_data'], $_SESSION['form_errors']);
        include BASE_PATH . '/src/views/admin/staff/create.php';
    }

    public function adminStoreStaff($data)
    {
        $errors = [];
        if (empty($data['username'])) $errors['username'] = "Username is required.";
        if (empty($data['email'])) $errors['email'] = "Email is required.";
        else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";
        if (empty($data['password'])) $errors['password'] = "Password is required.";
        // Add password confirmation check if desired: if ($data['password'] !== $data['password_confirmation']) $errors['password'] = "Passwords do not match.";

        if ($this->user->emailExists($data['email'])) { // Use User model for email check
            $errors['email'] = "Email already exists.";
        }

        if (!empty($errors)) {
            $_SESSION['error_message'] = "Please correct the errors below.";
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            header('Location: /student-course-hub/admin/staff/create');
            exit;
        }
        
        try {
            $this->staff->createStaff($data); // createStaff in Staff model handles hashing and role
            $_SESSION['success_message'] = "Staff member created successfully.";
            header('Location: /student-course-hub/admin/staff');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Error creating staff member: " . $e->getMessage();
            $_SESSION['form_data'] = $data;
            header('Location: /student-course-hub/admin/staff/create');
            exit;
        }
    }

    public function adminEditStaffForm($id)
    {
        $staffMember = $this->staff->getStaffById($id);
        if (!$staffMember) {
            $_SESSION['error_message'] = "Staff member not found.";
            header('Location: /student-course-hub/admin/staff');
            exit;
        }
        // Merge with session data if validation failed on update attempt
        $data = $_SESSION['form_data'] ?? $staffMember;
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_data'], $_SESSION['form_errors']);

        include BASE_PATH . '/src/views/admin/staff/edit.php';
    }

    public function adminUpdateStaff($id, $data) // Renamed from updateExistingStaff
    {
        $errors = [];
        if (empty($data['username'])) $errors['username'] = "Username is required.";
        if (empty($data['email'])) $errors['email'] = "Email is required.";
        else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";

        $currentStaff = $this->staff->getStaffById($id);
        if (!$currentStaff) {
            $_SESSION['error_message'] = "Staff member not found.";
            header('Location: /student-course-hub/admin/staff');
            exit;
        }

        if ($data['email'] !== $currentStaff['email'] && $this->user->emailExists($data['email'])) { // Use User model
            $errors['email'] = "New email address is already in use.";
        }

        if (!empty($errors)) {
            $_SESSION['error_message'] = "Please correct the errors below.";
            $_SESSION['form_data'] = $data;
            $_SESSION['form_errors'] = $errors;
            header('Location: /student-course-hub/admin/staff/' . $id . '/edit');
            exit;
        }

        try {
            $this->staff->updateStaff($id, ['username' => $data['username'], 'email' => $data['email']]);

            if (!empty($data['password'])) {
                // Add password validation (e.g., length, complexity) before updating
                $this->staff->updateStaffPassword($id, $data['password']);
            }
            
            $_SESSION['success_message'] = "Staff member updated successfully.";
            header('Location: /student-course-hub/admin/staff');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Error updating staff member: " . $e->getMessage();
            $_SESSION['form_data'] = $data;
            header('Location: /student-course-hub/admin/staff/' . $id . '/edit');
            exit;
        }
    }

    public function adminDeleteStaff($id) // Renamed from destroyStaff
    {
        try {
            $staffMember = $this->staff->getStaffById($id);
            if (!$staffMember) {
                $_SESSION['error_message'] = "Staff member not found.";
                header('Location: /student-course-hub/admin/staff');
                exit;
            }
            $this->staff->deleteStaff($id);
            $_SESSION['success_message'] = "Staff member deleted successfully.";
            header('Location: /student-course-hub/admin/staff');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Error deleting staff member: " . $e->getMessage();
            header('Location: /student-course-hub/admin/staff');
            exit;
        }
    }

    private function getDashboardStats(): array
    {
        try {
            $programmeStats = $this->programme->getProgrammeStats();
            error_log("Programme stats: " . print_r($programmeStats, true));
            
            $studentStats = $this->student->getStats();
            error_log("Student stats: " . print_r($studentStats, true));
            
            $moduleStats = $this->module->getStats();
            error_log("Module stats: " . print_r($moduleStats, true));

            $stats = [
                'total_programmes' => $programmeStats['total'],
                'active_programmes' => $programmeStats['active'], 
                'total_students' => $studentStats['total'],
                'new_students' => $studentStats['new_this_month'],
                'total_modules' => $moduleStats['total'],
            'active_modules' => $moduleStats['active'],
            'recent_activities' => $this->getRecentActivities()
            ];

            error_log("Final stats array: " . print_r($stats, true));
            return $stats;
            
        } catch (\Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            return [
                'total_programmes' => 0,
                'active_programmes' => 0,
                'total_students' => 0,
                'new_students' => 0,
                'total_modules' => 0,
                'active_modules' => 0,
                'recent_activities' => []
            ];
        }
    }

    private function getRecentActivities(): array
    {
        // TODO: Implement activity logging and retrieval
        return [
            [
                'icon' => 'user-plus',
                'description' => 'New student registered',
                'time' => '2 hours ago'
            ],
            [
                'icon' => 'graduation-cap',
                'description' => 'New programme added',
                'time' => '1 day ago'
            ],
            [
                'icon' => 'book',
                'description' => 'Module updated',
                'time' => '3 days ago'
            ]
        ];
    }

    private function validateModuleData(array $data): void
    {
        $required = ['title', 'credits', 'level'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("$field is required");
            }
        }

        if (!is_numeric($data['credits']) || $data['credits'] < 0) {
            throw new Exception("Credits must be a positive number");
        }
    }

    // Programme Management
    public function createProgram(Request $request, Response $response): Response {
        try {
            // Validate required fields
            $requiredFields = ['title', 'description', 'level'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("$field is required");
                }
            }

            // Sanitize inputs
            $sanitizedData = array_map('htmlspecialchars', $data);
            
            return $this->programme->create($sanitizedData);
        } catch (Exception $e) {
            error_log("Error creating program: " . $e->getMessage());
            throw new Exception("Failed to create program: " . $e->getMessage());
        }
    }

    public function updateProgram($id, $data) {
        try {
            if (!$this->programme->exists($id)) {
                throw new Exception("Programme not found");
            }

            // Sanitize inputs
            $sanitizedData = array_map('htmlspecialchars', $data);
            $sanitizedData['id'] = $id;

            return $this->programme->update($sanitizedData);
        } catch (Exception $e) {
            error_log("Error updating program: " . $e->getMessage());
            throw new Exception("Failed to update program: " . $e->getMessage());
        }
    }

    public function deleteProgram($id) {
        try {
            if (!$this->programme->exists($id)) {
                throw new Exception("Programme not found");
            }

            // Check if there are any enrolled students
            if ($this->programme->hasEnrolledStudents($id)) {
                throw new Exception("Cannot delete program with enrolled students");
            }

            return $this->programme->delete($id);
        } catch (Exception $e) {
            error_log("Error deleting program: " . $e->getMessage());
            throw new Exception("Failed to delete program: " . $e->getMessage());
        }
    }

    public function publishProgramme($id) {
        try {
            $programme = $this->programme->findById($id);
            if (!$programme) {
                throw new Exception("Programme not found");
            }

            $this->programme->update([
                'id' => $id,
                'is_published' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            error_log("Programme published: " . $id);
            $_SESSION['success_message'] = "Programme published successfully.";
            
        } catch (Exception $e) {
            error_log("Error publishing programme: " . $e->getMessage());
            $_SESSION['error_message'] = "Error publishing programme. Please try again.";
        }
        
        header('Location: /student-course-hub/admin/programmes');
        exit;
    }

    public function unpublishProgramme($id) {
        try {
            $programme = $this->programme->findById($id);
            if (!$programme) {
                throw new Exception("Programme not found");
            }

            // Check if there are enrolled students before unpublishing
            if ($this->programme->hasEnrolledStudents($id)) {
                throw new Exception("Cannot unpublish programme with enrolled students");
            }

            $this->programme->update([
                'id' => $id,
                'is_published' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            error_log("Programme unpublished: " . $id);
            $_SESSION['success_message'] = "Programme unpublished successfully.";
            
        } catch (Exception $e) {
            error_log("Error unpublishing programme: " . $e->getMessage());
            $_SESSION['error_message'] = "Error unpublishing programme: " . $e->getMessage();
        }
        
        header('Location: /student-course-hub/admin/programmes');
        exit;
    }

    public function manageModules($programId, $moduleData) {
        try {
            if (!$this->programme->exists($programId)) {
                throw new Exception("Programme not found");
            }

            $this->db->beginTransaction();

            // Remove existing module associations
            $this->programme->removeAllModules($programId);

            // Add new module associations
            foreach ($moduleData as $moduleId) {
                if (!$this->module->exists($moduleId)) {
                    throw new Exception("Module $moduleId not found");
                }
                $this->programme->addModule($programId, $moduleId);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error managing modules: " . $e->getMessage());
            throw new Exception("Failed to manage modules: " . $e->getMessage());
        }
    }

    public function getStudentsList($programId) {
        try {
            if (!$this->programme->exists($programId)) {
                throw new Exception("Programme not found");
            }

            return $this->programme->getInterestedStudents($programId);
        } catch (Exception $e) {
            error_log("Error getting students list: " . $e->getMessage());
            throw new Exception("Failed to get students list: " . $e->getMessage());
        }
    }

    public function exportMailingList($programId) {
        try {
            if (!$this->programme->exists($programId)) {
                throw new Exception("Programme not found");
            }

            $students = $this->programme->getInterestedStudents($programId);
            
            // Generate CSV
            $filename = "mailing_list_" . $programId . "_" . date('Y-m-d') . ".csv";
            $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
            
            $fp = fopen($filepath, 'w');
            fputcsv($fp, ['Name', 'Email', 'Registration Date']);
            
            foreach ($students as $student) {
                fputcsv($fp, [
                    $student['username'],
                    $student['email'],
                    $student['registered_at']
                ]);
            }
            
            fclose($fp);
            return $filepath;

        } catch (Exception $e) {
            error_log("Error exporting mailing list: " . $e->getMessage());
            throw new Exception("Failed to export mailing list: " . $e->getMessage());
        }
    }

    // Enhanced mailing list features
    public function generateMailingList($programmeId, $filters = []) {
        try {
            $students = $this->programme->getInterestedStudents($programmeId);
            return $this->formatMailingList($students, $filters);
        } catch (Exception $e) {
            error_log("Error generating mailing list: " . $e->getMessage());
            throw new Exception("Failed to generate mailing list: " . $e->getMessage());
        }
    }

    // Image validation for WCAG2 compliance
    private function validateProgrammeImage($imageData) {
        $validator = new ImageValidator();
        if (!$validator->isWCAG2Compliant($imageData)) {
            throw new Exception("Image does not meet accessibility requirements");
        }
    }

    private function formatMailingList($students, $filters) {
        $formattedList = [];
        foreach ($students as $student) {
            if ($this->matchesFilters($student, $filters)) {
                $formattedList[] = [
                    'email' => $student['email'],
                    'name' => $student['username'],
                    'interests' => $student['interests'],
                    'registration_date' => $student['registered_at']
                ];
            }
        }
        return $formattedList;
    }

    private function matchesFilters($student, $filters) {
        if (empty($filters)) return true;
        
        foreach ($filters as $key => $value) {
            if (!isset($student[$key]) || $student[$key] != $value) {
                return false;
            }
        }
        return true;
    }

    public function showProgrammeStudents($programmeId) {
        try {
            $programme = $this->programme->findById($programmeId);
            if (!$programme) {
                throw new Exception("Programme not found");
            }

            $students = $this->programme->getProgrammeStudents($programmeId);
            
            include BASE_PATH . '/src/views/admin/programmes/students.php';
            
        } catch (Exception $e) {
            error_log("Error showing programme students: " . $e->getMessage());
            $_SESSION['error'] = "Failed to retrieve programme students";
            header('Location: /student-course-hub/admin/programmes');
            exit;
        }
    }

    public function deleteModule($id)
    {
        try {
            // Check if module exists
            $module = $this->module->getModuleById($id);
            if (!$module) {
                $_SESSION['error_message'] = "Module not found.";
                header('Location: /student-course-hub/admin/modules');
                exit;
            }

            try {
                // Check for enrolled students
                $hasEnrolledStudents = $this->module->hasEnrolledStudents($id);
                if ($hasEnrolledStudents) {
                    $_SESSION['error_message'] = "Cannot delete module with enrolled students.";
                    header('Location: /student-course-hub/admin/modules');
                    exit;
                }
            } catch (\PDOException $e) {
                // If there's an issue with the enrollments table, log it but allow deletion
                error_log("Error checking enrollments: " . $e->getMessage());
            }
            
            $this->module->deleteModule($id);
            $_SESSION['success_message'] = "Module deleted successfully.";
            header('Location: /student-course-hub/admin/modules');
            exit;
        } catch (\Exception $e) {
            error_log("Error deleting module: " . $e->getMessage());
            $_SESSION['error_message'] = "Error deleting module: " . $e->getMessage();
            header('Location: /student-course-hub/admin/modules');
            exit;
        }
    }

}