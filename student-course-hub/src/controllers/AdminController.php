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
        // This method would typically render a form for creating a new programme.
        // For now, let's assume it includes a view file.
        // You'll need to create this view file: /src/views/admin/programmes/create.php
        include BASE_PATH . '/src/views/admin/programmes/create.php';
    }

    public function createProgramme($data)
    {
        // Basic validation (you should expand this)
        if (empty($data['title']) || empty($data['level'])) {
            // Handle error - perhaps redirect back with an error message
            // For simplicity, we'll just echo an error and exit
            echo "Title and Level are required.";
            // You might want to include the create form again with an error message
            // include BASE_PATH . '/src/views/admin/programmes/create.php';
            return;
        }
        
        try {
            $this->programme->create($data);
            // Redirect to the programmes list after creation
            header('Location: /student-course-hub/admin/programmes');
            exit;
        } catch (Exception $e) {
            // Log error or show a user-friendly message
            echo "Error creating programme: " . $e->getMessage();
            // include BASE_PATH . '/src/views/admin/programmes/create.php';
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
        // Basic validation
        if (empty($data['title']) || empty($data['level'])) {
            echo "Title and Level are required.";
            // You might want to include the edit form again with an error message
            // $programme = $this->programme->findById($id);
            // include BASE_PATH . '/src/views/admin/programmes/edit.php';
            return;
        }

        $data['id'] = $id; // Ensure the ID is part of the data array for the update method
        
        try {
            $this->programme->update($data);
            header('Location: /student-course-hub/admin/programmes');
            exit;
        } catch (Exception $e) {
            echo "Error updating programme: " . $e->getMessage();
            // $programme = $this->programme->findById($id); // Fetch again for the form
            // include BASE_PATH . '/src/views/admin/programmes/edit.php';
        }
    }

    public function deleteProgramme($id)
    {
        try {
            $this->programme->delete($id);
            header('Location: /student-course-hub/admin/programmes');
            exit;
        } catch (Exception $e) {
            echo "Error deleting programme: " . $e->getMessage();
            // It might be good to redirect back with an error message
            // header('Location: /student-course-hub/admin/programmes?error=' . urlencode($e->getMessage()));
            // exit;
        }
    }

    // Student Management
    public function listStudents()
    {
        $filters = [];
        
        // Get search filters from GET parameters
        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (!empty($_GET['programme'])) {
            $filters['programme'] = $_GET['programme'];
        }
        
        // Handle data export
        if (isset($_GET['action']) && $_GET['action'] === 'export') {
            return $this->exportStudentData($filters);
        }
        
        $students = $this->student->getAllStudents($filters);
        $programmes = $this->programme->getAllProgrammes(); // For filter dropdown
        
        // Pass any search params back to the view
        $search = $filters['search'] ?? '';
        $selectedProgramme = $filters['programme'] ?? '';
        
        include BASE_PATH . '/src/views/admin/students.php';
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
    public function listModules() // Removed Request $request, Response $response arguments
    {
        $modules = $this->module->getAllModules();
        // Ensure $modules variable is available to the included view
        include BASE_PATH . '/src/views/admin/modules.php'; // Changed to include PHP view
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

    public function createModule($data)
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
            header('Location: /student-course-hub/admin/modules/create');
            exit;
        }
        
        try {
            $this->module->createModule(
                $data['title'], 
                $data['description'] ?? '', 
                $data['credits'],
                $data['year_of_study'] ?? null,
                $data['programme_id'],
                $data['staff_id'] ?? null
            );
            $_SESSION['success_message'] = "Module created successfully.";
            header('Location: /student-course-hub/admin/modules');
            exit;
        } catch (\Exception $e) {
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
        $programmeStats = $this->programme->getProgrammeStats();
        $studentStats = $this->student->getStats();
        $moduleStats = $this->module->getStats();

        return [
            'total_programmes' => $programmeStats['total'],
            'active_programmes' => $programmeStats['active'],
            'total_students' => $studentStats['total'],
            'new_students' => $studentStats['new_this_month'],
            'pending_interests' => $studentStats['pending_interests'],
            'total_modules' => $moduleStats['total'],
            'active_modules' => $moduleStats['active'],
            'recent_activities' => $this->getRecentActivities()
        ];
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

    private function validateProgrammeData(array $data): void
    {
        $required = ['title', 'description', 'level'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("$field is required");
            }
        }

        if (!empty($data['image'])) {
            $this->validateProgrammeImage($data['image']);
        }
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

    public function publishProgram($id) {
        try {
            if (!$this->programme->exists($id)) {
                throw new Exception("Programme not found");
            }

            return $this->programme->updatePublishStatus($id, true);
        } catch (Exception $e) {
            error_log("Error publishing program: " . $e->getMessage());
            throw new Exception("Failed to publish program: " . $e->getMessage());
        }
    }

    public function unpublishProgram($id) {
        try {
            if (!$this->programme->exists($id)) {
                throw new Exception("Programme not found");
            }

            return $this->programme->updatePublishStatus($id, false);
        } catch (Exception $e) {
            error_log("Error unpublishing program: " . $e->getMessage());
            throw new Exception("Failed to unpublish program: " . $e->getMessage());
        }
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

}