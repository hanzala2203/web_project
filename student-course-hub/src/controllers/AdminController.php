<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Programme.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../models/Staff.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../utils/ImageValidator.php';

class AdminController {
    private $db;
    private $programme;
    private $module;
    private $staff;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->programme = new Programme($this->db);
        $this->module = new Module($this->db);
        $this->staff = new Staff($this->db);
    }

    public function createProgram($data) {
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

    // Add new module management methods
    public function createModule($data) {
        try {
            $this->validateModuleData($data);
            $sanitizedData = Security::sanitizeInput($data);
            return $this->module->create($sanitizedData);
        } catch (Exception $e) {
            error_log("Error creating module: " . $e->getMessage());
            throw new Exception("Failed to create module: " . $e->getMessage());
        }
    }

    public function assignModuleLeader($moduleId, $staffId) {
        try {
            if (!$this->module->exists($moduleId)) {
                throw new Exception("Module not found");
            }
            if (!$this->staff->exists($staffId)) {
                throw new Exception("Staff member not found");
            }
            return $this->module->assignLeader($moduleId, $staffId);
        } catch (Exception $e) {
            error_log("Error assigning module leader: " . $e->getMessage());
            throw new Exception("Failed to assign module leader: " . $e->getMessage());
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

    private function validateModuleData($data) {
        $required = ['title', 'description', 'credits', 'year_of_study'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("$field is required for module");
            }
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

    public function getDashboardStats() {
        // TODO: Replace with real queries
        return [
            'total_programmes' => 5,
            'active_programmes' => 4,
            'total_students' => 120,
            'new_students' => 8,
            'pending_interests' => 3,
            'total_modules' => 20,
            'active_modules' => 18,
            'recent_activities' => [
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
            ]
        ];
    }
}