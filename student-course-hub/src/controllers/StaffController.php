<?php

namespace App\Controllers;

use App\Models\Module;
use App\Models\Programme;
use App\Models\Staff;
use App\Models\Student;
use Exception;

class StaffController {
    private $module;
    private $programme;
    private $staff;
    private $student;    public function __construct() {
        $this->module = new Module();
        $this->programme = new Programme();
        $this->staff = new Staff();
        $this->student = new Student();
    }

    public function dashboard() {
        // Get staff ID from session
        $staffId = $_SESSION['user_id'] ?? null;
        
        if (!$staffId) {
            $_SESSION['error_message'] = "Staff ID not found. Please log in again.";
            header('Location: /student-course-hub/auth/login');
            exit;
        }

        try {            // Get modules led by this staff member with student counts
            $modules = $this->module->getModulesByStaffId($staffId);
            
            // Get programmes that include these modules
            $programmes = [];
            if (!empty($modules)) {
                $moduleIds = array_column($modules, 'id');
                $programmes = $this->programme->getProgrammesByModuleIds($moduleIds);
            }
              // Include the dashboard view with modules data
            require_once __DIR__ . '/../views/staff/dashboard.php';

        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error loading dashboard: " . $e->getMessage();
            // Log the error
            error_log("Staff dashboard error: " . $e->getMessage());
            // Redirect to a error page or show error in dashboard
            header('Location: /student-course-hub/staff/dashboard');
        }
    }    public function viewModule($id) {
        $staffId = $_SESSION['user_id'] ?? null;
        
        try {
            $module = $this->module->getModuleById($id);
            
            // Verify that this staff member leads this module
            if ($module['staff_id'] !== $staffId) {
                throw new Exception("You do not have permission to view this module.");
            }

            include BASE_PATH . '/src/views/staff/modules/view.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /student-course-hub/staff/dashboard');
            exit;
        }
    }

    public function viewModuleStudents($id) {
        $staffId = $_SESSION['user_id'] ?? null;
        
        try {
            $module = $this->module->getModuleById($id);
            
            // Verify that this staff member leads this module
            if ($module['staff_id'] !== $staffId) {
                throw new Exception("You do not have permission to view this module's students.");
            }

            // Get students enrolled in this module
            $students = $this->module->getEnrolledStudents($id);
            
            // Add module data for the view
            $data = [
                'module' => $module,
                'students' => $students
            ];

            include BASE_PATH . '/src/views/staff/modules/students.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /student-course-hub/staff/modules/' . $id);
            exit;
        }
    }

    public function viewProgramme($id) {
        $staffId = $_SESSION['user_id'] ?? null;
        
        try {
            $programme = $this->programme->findById($id);
            
            // Get all modules in this programme that are led by this staff member
            $staffModules = $this->module->getModulesByStaffIdAndProgrammeId($staffId, $id);
            
            if (empty($staffModules)) {
                throw new Exception("You do not have any modules in this programme.");
            }

            $programme['staff_modules'] = $staffModules;
            include BASE_PATH . '/src/views/staff/programmes/view.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /student-course-hub/staff/dashboard');
            exit;
        }
    }

    public function viewStudentDetails($moduleId, $studentId) {
        $staffId = $_SESSION['user_id'] ?? null;
        
        try {
            $module = $this->module->getModuleById($moduleId);
            
            // Verify that this staff member leads this module
            if ($module['staff_id'] !== $staffId) {
                throw new Exception("You do not have permission to view this module's students.");
            }

            // Get student details and enrollment info
            $student = $this->student->getStudentDetails($studentId, $moduleId);
            if (!$student) {
                throw new Exception("Student not found or not enrolled in this module.");
            }

            $data = [
                'module' => $module,
                'student' => $student
            ];

            include BASE_PATH . '/src/views/staff/modules/student_details.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /student-course-hub/staff/modules/' . $moduleId . '/students');
            exit;
        }
    }
    public function listModules() {
        $staffId = $_SESSION['user_id'] ?? null;
        
        if (!$staffId) {
            $_SESSION['error_message'] = "Staff ID not found. Please log in again.";
            header('Location: /student-course-hub/auth/login');
            exit;
        }

        try {
            $modules = $this->module->getModulesByStaffId($staffId);
            require_once __DIR__ . '/../views/staff/modules/index.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error loading modules: " . $e->getMessage();
            error_log("Staff modules error: " . $e->getMessage());
            require_once __DIR__ . '/../views/staff/modules/index.php';
        }
    }
}
