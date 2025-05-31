<?php

namespace App\Controllers;

use App\Models\Student;
use App\Models\Programme;
use App\Models\Module;

require_once __DIR__ . '/../models/Model.php';
use App\Models\Model;

class StudentController extends Model {
    private $student;
    private $programme;
    private $module;

    public function __construct() {
        try {
            parent::__construct();
            $this->student = new Student();
            $this->programme = new Programme();
            $this->module = new Module();
        } catch (\Exception $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize StudentController: ' . $e->getMessage());
        }
    }

    public function viewInterests() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }

            $studentId = $_SESSION['user_id'];
            
            // Get student's interests with detailed programme information
            try {
                $interests = $this->student->getStudentInterests($studentId);
            } catch (\Exception $e) {
                error_log("Error getting student interests: " . $e->getMessage());
                $interests = [];
            }
            
            // Show the view with the interests data
            extract(['interests' => $interests]);
            require_once __DIR__ . '/../views/student/manage_interests.php';
            
        } catch (\Exception $e) {
            error_log("Error in viewInterests: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading your interests.";
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    public function handleInterest() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }

            if (!isset($_POST['programme_id'])) {
                $_SESSION['error'] = 'Programme ID is required';
                header('Location: ' . BASE_URL . '/student/explore_programmes');
                exit;
            }

            $studentId = $_SESSION['user_id'];
            $programmeId = $_POST['programme_id'];
            $action = $_POST['action'] ?? 'register';
            $redirect = $_POST['redirect'] ?? BASE_URL . '/student/programme_details?id=' . $programmeId;

            try {
                if ($action === 'withdraw') {
                    if (!$this->student->hasInterest($studentId, $programmeId)) {
                        $_SESSION['error'] = 'You have not registered interest in this programme';
                    } else if ($this->student->removeInterest($studentId, $programmeId)) {
                        $_SESSION['success'] = 'Successfully withdrew interest from the programme';
                    } else {
                        $_SESSION['error'] = 'Failed to withdraw interest';
                    }
                } else {
                    if ($this->student->hasInterest($studentId, $programmeId)) {
                        $_SESSION['error'] = 'You have already registered interest in this programme';
                    } else if ($this->student->addInterest($studentId, $programmeId)) {
                        $_SESSION['success'] = 'Successfully registered interest in the programme';
                    } else {
                        $_SESSION['error'] = 'Failed to register interest';
                    }
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            header('Location: ' . $redirect);
            exit;
        } catch (\Exception $e) {
            error_log('Error in handleInterest: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while processing your request';
            header('Location: ' . BASE_URL . '/student/explore_programmes');
            exit;
        }
    }

    public function registerInterest($studentId, $programmeId) {
        try {
            // Validate inputs
            if (!$this->programme->exists($programmeId)) {
                throw new \Exception("Programme not found");
            }

            if (!$this->student->exists($studentId)) {
                throw new \Exception("Student not found");
            }

            // Check if already registered
            if ($this->student->hasInterest($studentId, $programmeId)) {
                throw new \Exception("Already registered interest in this programme");
            }

            // Log the attempt
            error_log("StudentController: Attempting to add interest - Student ID: $studentId, Programme ID: $programmeId");
            
            // Register interest
            $result = $this->student->addInterest($studentId, $programmeId);
            
            if ($result) {
                error_log("StudentController: Successfully added interest");
                return true;
            } else {
                error_log("StudentController: Failed to add interest");
                return false;
            }

        } catch (Exception $e) {
            error_log("StudentController: Error registering interest: " . $e->getMessage());
            throw new Exception("Failed to register interest: " . $e->getMessage());
        }
    }





    public function withdrawInterest($studentId, $programmeId) {
        try {
            // Validate inputs
            if (!$this->programme->exists($programmeId)) {
                throw new Exception("Programme not found");
            }

            if (!$this->student->exists($studentId)) {
                throw new Exception("Student not found");
            }

            // Check if interest exists
            if (!$this->student->hasInterest($studentId, $programmeId)) {
                throw new Exception("No registered interest found for this programme");
            }

            // Log the attempt
            error_log("StudentController: Attempting to withdraw interest - Student ID: $studentId, Programme ID: $programmeId");
            
            // Withdraw interest
            $result = $this->student->removeInterest($studentId, $programmeId);
            
            if ($result) {
                error_log("StudentController: Successfully withdrew interest - Student ID: $studentId, Programme ID: $programmeId");
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in withdrawInterest: " . $e->getMessage());
            throw $e;
        }
    }

    public function viewProgrammeDetails($programmeId) {
        try {
            if (!$programmeId) {
                $_SESSION['error'] = 'Programme ID is required';
                header('Location: ' . BASE_URL . '/error');
                exit;
            }

            // Get programme details with modules
            $programme = $this->programme->findById($programmeId);
            if (!$programme) {
                throw new \Exception("Programme not found");
            }

            // Set default images
            $programme['image_url'] = $programme['image_url'] ?? '/assets/images/default-programme.jpg';
            
            // Flatten and enhance modules array
            $flatModules = [];
            if (isset($programme['modules']) && is_array($programme['modules'])) {
                foreach ($programme['modules'] as $year => $yearModules) {
                    foreach ($yearModules as $module) {
                        $module['image_url'] = $module['image_url'] ?? '/assets/images/default-module.jpg';
                        $flatModules[] = $module;
                    }
                }
            }
            $programme['modules'] = $flatModules;

            // Get student's interest status
            $studentId = $_SESSION['user_id'] ?? null;
            $hasInterest = false;
            if ($studentId) {
                try {
                    $hasInterest = $this->student->hasInterest($studentId, $programmeId);
                } catch (\Exception $e) {
                    error_log("Error checking interest: " . $e->getMessage());
                }
            }

            // Prepare data for view
            $data = [
                'programme' => $programme,
                'studentId' => $studentId,
                'hasInterest' => $hasInterest,
                'pageTitle' => 'Programme Details: ' . ($programme['title'] ?? 'Unknown Programme')
            ];

            // Extract data to make it available in the view
            extract($data);
            
            // Show the view
            require_once __DIR__ . '/../views/student/programme_details.php';
            
        } catch (\Exception $e) {
            error_log("Error in viewProgrammeDetails: " . $e->getMessage());
            $_SESSION['error'] = "Error loading programme details";
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    public function viewDashboard() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }
            $studentId = $_SESSION['user_id'];
            
            // Get student details from users table
            $student = $this->student->findById($studentId);
            if (!$student || $student['role'] !== 'student') {
                throw new \Exception("Student not found");
            }

            // Get data with fallbacks for missing functionality
            try {
                $interests = $this->student->getInterests($studentId);
            } catch (\Exception $e) {
                error_log("Error getting interests: " . $e->getMessage());
                $interests = [];
            }

            try {
                $enrolledCourses = $this->student->getEnrolledCourses($studentId);
            } catch (\Exception $e) {
                error_log("Error getting enrolled courses: " . $e->getMessage());
                $enrolledCourses = [];
            }

            try {
                $deadlines = $this->student->getUpcomingDeadlines($studentId);
            } catch (\Exception $e) {
                error_log("Error getting deadlines: " . $e->getMessage());
                $deadlines = [];
            }

            try {
                $interestedCourses = $this->student->getInterestedProgrammes($studentId);
            } catch (\Exception $e) {
                error_log("Error getting interested programmes: " . $e->getMessage());
                $interestedCourses = [];
            }

            // Get optional data with null defaults
            $currentProgramme = $this->getCurrentProgramme($studentId) ?? null;
            $modulePrerequisites = $this->getModulePrerequisites($studentId) ?? [];
            $notifications = []; // Removed notification functionality temporarily
            $interestAnalytics = $this->getInterestAnalytics($studentId) ?? [];
            $recommendedProgrammes = method_exists($this->programme, 'getRecommendedProgrammes') ? 
                                   $this->programme->getRecommendedProgrammes($studentId) : [];

            // Prepare data for view
            $data = [
                'student' => $student,
                'interests' => $interests,
                'enrolledCourses' => $enrolledCourses,
                'deadlines' => $deadlines,
                'interestedCourses' => $interestedCourses,
                'currentProgramme' => $currentProgramme,
                'modulePrerequisites' => $modulePrerequisites,
                'notifications' => $notifications,
                'interestAnalytics' => $interestAnalytics,
                'recommendedProgrammes' => $recommendedProgrammes
            ];

            // Extract data to make it available in the view
            extract($data);
            
            require_once BASE_PATH . '/src/views/student/dashboard.php';
        } catch (\Exception $e) {
            error_log("Error in viewDashboard: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading the dashboard: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    public function viewDashboardNew() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }
            $studentId = $_SESSION['user_id'];
            
            // Get student details from users table
            $student = $this->student->findById($studentId);
            if (!$student || $student['role'] !== 'student') {
                throw new \Exception("Student not found");
            }

            // Get data with fallbacks
            try {
                $interests = $this->student->getInterests($studentId);
            } catch (\Exception $e) {
                error_log("Error getting interests: " . $e->getMessage());
                $interests = [];
            }

            try {
                $enrolledCourses = $this->student->getEnrolledCourses($studentId);
                // Calculate progress for each course
                foreach ($enrolledCourses as &$course) {
                    $course['progress'] = $this->calculateCourseProgress($studentId, $course['id']);
                }
            } catch (\Exception $e) {
                error_log("Error getting enrolled courses: " . $e->getMessage());
                $enrolledCourses = [];
            }

            try {
                $deadlines = $this->student->getUpcomingDeadlines($studentId);
            } catch (\Exception $e) {
                error_log("Error getting deadlines: " . $e->getMessage());
                $deadlines = [];
            }

            try {
                $interestedCourses = $this->student->getInterestedProgrammes($studentId);
            } catch (\Exception $e) {
                error_log("Error getting interested programmes: " . $e->getMessage());
                $interestedCourses = [];
            }

            // Get optional data with null defaults
            $currentProgramme = $this->getCurrentProgramme($studentId) ?? null;
            $modulePrerequisites = $this->getModulePrerequisites($studentId) ?? [];
            $notifications = []; // Removed notification functionality temporarily
            $interestAnalytics = $this->getInterestAnalytics($studentId) ?? [];
            $recommendedProgrammes = method_exists($this->programme, 'getRecommendedProgrammes') ? 
                                   $this->programme->getRecommendedProgrammes($studentId) : [];

            // Calculate additional stats for display
            $enrolledCoursesCount = count($enrolledCourses);
            $completedCoursesCount = count(array_filter($enrolledCourses, function($course) {
                return ($course['progress'] ?? 0) === 100;
            }));
            $overallProgress = $enrolledCoursesCount > 0 
                ? array_sum(array_column($enrolledCourses, 'progress')) / $enrolledCoursesCount 
                : 0;

            // Prepare data for view
            $data = [
                'student' => $student,
                'interests' => $interests,
                'enrolledCourses' => $enrolledCourses,
                'deadlines' => $deadlines,
                'interestedCourses' => $interestedCourses,
                'currentProgramme' => $currentProgramme,
                'modulePrerequisites' => $modulePrerequisites,
                'notifications' => $notifications,
                'interestAnalytics' => $interestAnalytics,
                'recommendedProgrammes' => $recommendedProgrammes,
                'enrolledCoursesCount' => $enrolledCoursesCount,
                'completedCoursesCount' => $completedCoursesCount,
                'overallProgress' => $overallProgress
            ];

            // Extract data to make it available in the view
            extract($data);
            
            require_once BASE_PATH . '/src/views/student/dashboard_new.php';
        } catch (\Exception $e) {
            error_log("Error in viewDashboardNew: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading the dashboard: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    public function exploreProgrammes() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }
            $studentId = $_SESSION['user_id'];
            
            // Get query parameters
            $query = $_GET['query'] ?? '';
            $filters = [
                'level' => $_GET['level'] ?? '',
                'duration' => $_GET['duration'] ?? '',
                'department' => $_GET['department'] ?? ''
            ];
            
            // Remove empty filters
            $filters = array_filter($filters);
            
            // Get filtered and searched programmes
            try {
                $programmes = $this->programme->searchProgrammes($query, $filters);
                if (!$programmes) {
                    $programmes = []; // Set empty array if no programmes found
                }
            } catch (\Exception $e) {
                error_log("Error getting filtered programmes: " . $e->getMessage());
                $programmes = [];
            }
            
            // Get student's current interests
            try {
                $interests = $this->student->getInterests($studentId);
            } catch (\Exception $e) {
                error_log("Error getting student interests: " . $e->getMessage());
                $interests = [];
            }

            // Get departments for filtering
            $departments = [];
            foreach ($programmes as $prog) {
                if (!empty($prog['department']) && !in_array($prog['department'], $departments)) {
                    $departments[] = $prog['department'];
                }
            }
            
            // Prepare data for view
            $data = [
                'programmes' => $programmes,
                'interests' => $interests,
                'departments' => $departments
            ];

            // Extract data to make it available in the view
            extract($data);
            
            require_once BASE_PATH . '/src/views/student/explore_programmes.php';
        } catch (\Exception $e) {
            error_log("Error in exploreProgrammes: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading programmes. Please try again later.";
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    private function calculateCourseProgress($studentId, $courseId) {
        // For now, return a random progress between 0 and 100
        // TODO: Implement actual progress calculation based on completed modules/assignments
        return rand(0, 100);
    }

    public function updatePreferences($studentId, $preferences) {
        try {
            $validPreferences = $this->validatePreferences($preferences);
            
            // Enhanced preferences
            if (isset($validPreferences['accessibility_needs'])) {
                $this->updateAccessibilityPreferences($studentId, $validPreferences['accessibility_needs']);
            }

            if (isset($validPreferences['notification_preferences'])) {
                $this->updateNotificationPreferences($studentId, $validPreferences['notification_preferences']);
            }

            return $this->student->updatePreferences($studentId, $validPreferences);
        } catch (Exception $e) {
            error_log("Error updating preferences: " . $e->getMessage());
            throw new Exception("Failed to update preferences");
        }
    }

    private function getRecommendedCourses($studentId) {
        try {
            // Get student's interested programmes
            $interests = $this->student->getInterestedProgrammes($studentId);
            
            // For now, return empty array until recommendation system is implemented
            return [];
            
            // TODO: Implement actual course recommendations based on interests
            // $recommendedCourses = $this->programme->getRecommendations($interests);
            // return array_slice($recommendedCourses, 0, 5);

        } catch (Exception $e) {
            error_log("Error getting recommendations: " . $e->getMessage());
            return [];
        }
    }

    private function validatePreferences($preferences) {
        $validPreferences = [];
        
        // Enhanced preference validation
        foreach ($preferences as $key => $value) {
            switch ($key) {
                case 'email_notifications':
                    $validPreferences[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'study_level':
                    if (in_array($value, ['undergraduate', 'postgraduate'])) {
                        $validPreferences[$key] = $value;
                    }
                    break;
                case 'accessibility_needs':
                    $validPreferences[$key] = $this->validateAccessibilityNeeds($value);
                    break;
                case 'notification_preferences':
                    $validPreferences[$key] = $this->validateNotificationPreferences($value);
                    break;
            }
        }
        
        return $validPreferences;
    }

    private function getModuleAccessibilityFeatures($moduleId) {
        return [
            'screen_reader_compatible' => true,
            'has_captions' => true,
            'has_transcripts' => true,
            'keyboard_navigable' => true
        ];
    }

    private function validateAccessibilityNeeds($needs) {
        $validNeeds = [];
        $allowedNeeds = ['screen_reader', 'high_contrast', 'keyboard_only', 'captions'];
        
        foreach ($needs as $need) {
            if (in_array($need, $allowedNeeds)) {
                $validNeeds[] = $need;
            }
        }
        
        return $validNeeds;
    }

    private function validateNotificationPreferences($prefs) {
        $validPrefs = [];
        $allowedTypes = ['email', 'sms', 'web'];
        $allowedEvents = ['programme_updates', 'open_days', 'application_deadlines'];
        
        foreach ($prefs as $type => $events) {
            if (in_array($type, $allowedTypes)) {
                $validPrefs[$type] = array_intersect($events, $allowedEvents);
            }
        }
        
        return $validPrefs;
    }

    private function getCurrentProgramme($studentId) {
        try {
            // Get the student's current programme
            $stmt = $this->db->prepare("
                SELECT p.*, 
                       m.id as module_id, 
                       m.title as module_title,
                       m.credits,
                       m.year_of_study,
                       m.semester,
                       u.username as staff_name,
                       u.email as staff_email,
                       NULL as staff_avatar
                FROM programmes p
                JOIN student_programmes sp ON p.id = sp.programme_id
                JOIN programme_modules pm ON p.id = pm.programme_id
                JOIN modules m ON pm.module_id = m.id
                LEFT JOIN users u ON m.staff_id = u.id
                WHERE sp.student_id = ?
                ORDER BY m.year_of_study, m.semester, m.title
            ");
            $stmt->execute([$studentId]);
            
            $programme = null;
            $modules = [];
            $staff = [];
            
            while ($row = $stmt->fetch()) {
                if (!$programme) {
                    $programme = [
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'description' => $row['description'],
                        'level' => $row['level'],
                        'years' => [],
                        'staff' => []
                    ];
                }
                
                $year = $row['year_of_study'];
                $semester = $row['semester'] ?? 1;
                
                if (!isset($programme['years'][$year])) {
                    $programme['years'][$year] = [];
                }
                if (!isset($programme['years'][$year][$semester])) {
                    $programme['years'][$year][$semester] = [];
                }
                
                $programme['years'][$year][$semester][] = [
                    'id' => $row['module_id'],
                    'title' => $row['module_title'],
                    'credits' => $row['credits']
                ];
                
                if ($row['staff_name'] && !isset($staff[$row['staff_name']])) {
                    $staff[$row['staff_name']] = [
                        'name' => $row['staff_name'],
                        'email' => $row['staff_email'],
                        'avatar_url' => $row['staff_avatar'],
                        'role' => 'Module Leader'
                    ];
                }
            }
            
            if ($programme) {
                $programme['staff'] = array_values($staff);
            }
            
            return $programme;
        } catch (Exception $e) {
            error_log("Error getting current programme: " . $e->getMessage());
            return null;
        }
    }

    private function getModulePrerequisites($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.id, m.title, 
                       GROUP_CONCAT(pm.prerequisite_module_id) as prerequisites
                FROM modules m
                JOIN programme_modules pgm ON m.id = pgm.module_id
                JOIN student_programmes sp ON pgm.programme_id = sp.programme_id
                LEFT JOIN module_prerequisites pm ON m.id = pm.module_id
                WHERE sp.student_id = ?
                GROUP BY m.id
                ORDER BY m.year_of_study, m.semester
            ");
            $stmt->execute([$studentId]);
            
            $modules = [];
            while ($row = $stmt->fetch()) {
                $prerequisites = [];
                if ($row['prerequisites']) {
                    $prereqIds = explode(',', $row['prerequisites']);
                    $prerequisites = $this->getModulesByIds($prereqIds);
                }
                
                $modules[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'prerequisites' => $prerequisites
                ];
            }
            
            return $modules;
        } catch (Exception $e) {
            error_log("Error getting module prerequisites: " . $e->getMessage());
            return [];
        }
    }

    private function getStudentNotifications($studentId) {
        try {
            return $this->notification->getStudentNotifications($studentId);
        } catch (Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            return [];
        }
    }

    private function getInterestAnalytics($studentId) {
        try {
            // Get total interests registered
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total_interests
                FROM student_interests
                WHERE student_id = ?
            ");
            $stmt->execute([$studentId]);
            $totalInterests = $stmt->fetchColumn();

            // Get interests by programme level
            $stmt = $this->db->prepare("
                SELECT p.level, COUNT(*) as count
                FROM student_interests si
                JOIN programmes p ON si.programme_id = p.id
                WHERE si.student_id = ?
                GROUP BY p.level
            ");
            $stmt->execute([$studentId]);
            $interestsByLevel = $stmt->fetchAll();

            $analytics = [
                [
                    'label' => 'Total Interests',
                    'value' => $totalInterests
                ]
            ];

            foreach ($interestsByLevel as $interest) {
                $analytics[] = [
                    'label' => ucfirst($interest['level']) . ' Programmes',
                    'value' => $interest['count']
                ];
            }

            return $analytics;
        } catch (Exception $e) {
            error_log("Error getting interest analytics: " . $e->getMessage());
            return [];
        }
    }

    private function getModulesByIds($moduleIds) {
        $placeholders = str_repeat('?,', count($moduleIds) - 1) . '?';
        $stmt = $this->db->prepare("
            SELECT id, title 
            FROM modules 
            WHERE id IN ($placeholders)
        ");
        $stmt->execute($moduleIds);
        return $stmt->fetchAll();
    }

    public function browseProgrammes($query = null, $filters = []) {
        try {
            // Get filtered and searched programmes
            $programmes = $this->programme->searchProgrammes($query, $filters);
            
            // Enhance each programme with additional information
            foreach ($programmes as &$prog) {
                // Get total number of staff
                $prog['staff_count'] = count($this->programme->getStaffMembers($prog['id']));
                
                // Get level-specific information
                if ($prog['level'] === 'undergraduate') {
                    $prog['duration'] = '3-4 years';
                    $prog['qualification'] = 'Bachelor\'s Degree';
                } else {
                    $prog['duration'] = '1-2 years';
                    $prog['qualification'] = 'Master\'s Degree';
                }
            }
            
            return [
                'programmes' => $programmes,
                'total' => count($programmes),
                'filters' => $filters,
                'query' => $query
            ];
            
        } catch (\Exception $e) {
            error_log("Error browsing programmes: " . $e->getMessage());
            throw new \Exception("Failed to retrieve programmes");
        }
    }

    public function getFilteredProgrammes() {
        try {
            // Get query parameters
            $query = $_GET['query'] ?? '';
            $filters = [
                'level' => $_GET['level'] ?? '',
                'department' => $_GET['department'] ?? '',
                'duration' => $_GET['duration'] ?? ''
            ];

            // Remove empty filters
            $filters = array_filter($filters);

            // Get filtered and searched programmes
            $programmes = $this->programme->searchProgrammes($query, $filters);
            
            // Enhance each programme with additional information
            foreach ($programmes as &$programme) {
                // Get level-specific information
                if ($programme['level'] === 'undergraduate') {
                    $programme['qualification'] = 'Bachelor\'s Degree';
                } else {
                    $programme['qualification'] = 'Master\'s Degree';
                }

                // Add key features if they exist
                $programme['key_features'] = $this->programme->getKeyFeatures($programme['id']);
                
                // Add interest status if user is logged in
                if (isset($_SESSION['user_id'])) {
                    $programme['interest_registered'] = $this->student->hasInterest($_SESSION['user_id'], $programme['id']);
                }
            }

            // Set appropriate headers
            header('Content-Type: application/json');
            http_response_code(200);
            
            // Return JSON response
            echo json_encode([
                'success' => true,
                'programmes' => $programmes,
                'total' => count($programmes),
                'filters' => $filters,
                'query' => $query
            ]);
            exit;
            
        } catch (\Exception $e) {
            // Log error
            error_log("Error in getFilteredProgrammes: " . $e->getMessage());
            
            // Return error response
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to retrieve programmes'
            ]);
            exit;
        }
    }

    public function getProgrammeStructure($programmeId) {
        try {
            // Get programme details first
            $details = $this->programme->findById($programmeId);
            if (!$details) {
                throw new \Exception("Programme not found");
            }
            
            // Get detailed programme structure
            try {
                $structure = $this->programme->getProgrammeStructure($programmeId);
            } catch (\Exception $e) {
                error_log("Error getting programme structure: " . $e->getMessage());
                $structure = []; // Default to empty structure if not found
            }
            
            // Calculate total credits
            $totalCredits = $this->calculateTotalCredits($structure);
            
            return [
                'programme' => $details,
                'structure' => $structure,
                'total_credits' => $totalCredits
            ];
            
        } catch (\Exception $e) {
            error_log("Error getting programme structure: " . $e->getMessage());
            throw new \Exception("Failed to retrieve programme details");
        }
    }

    private function calculateTotalCredits($structure) {
        $total = 0;
        foreach ($structure as $year) {
            foreach ($year as $semester) {
                foreach ($semester as $module) {
                    $total += $module['credits'];
                }
            }
        }
        return $total;
    }

    public function handleRegisterInterest() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }

            if (!isset($_POST['programme_id'])) {
                $_SESSION['error'] = 'Programme ID is required';
                header('Location: ' . BASE_URL . '/student/explore_programmes');
                exit;
            }

            $studentId = $_SESSION['user_id'];
            $programmeId = $_POST['programme_id'];

            if ($this->student->hasInterest($studentId, $programmeId)) {
                $_SESSION['error'] = 'You have already registered interest in this programme';
            } else if ($this->student->addInterest($studentId, $programmeId)) {
                $_SESSION['success'] = 'Successfully registered interest in the programme';
            } else {
                $_SESSION['error'] = 'Failed to register interest';
            }

            header('Location: ' . BASE_URL . '/student/programme_details?id=' . $programmeId);
            exit;
        } catch (\Exception $e) {
            error_log('Error in handleRegisterInterest: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while processing your request';
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    public function handleWithdrawInterest() {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            }

            if (!isset($_POST['programme_id'])) {
                $_SESSION['error'] = 'Programme ID is required';
                header('Location: ' . BASE_URL . '/student/my_interests');
                exit;
            }

            $studentId = $_SESSION['user_id'];
            $programmeId = $_POST['programme_id'];

            if (!$this->student->hasInterest($studentId, $programmeId)) {
                $_SESSION['error'] = 'You have not registered interest in this programme';
            } else if ($this->student->removeInterest($studentId, $programmeId)) {
                $_SESSION['success'] = 'Successfully withdrew interest from the programme';
            } else {
                $_SESSION['error'] = 'Failed to withdraw interest';
            }

            // Redirect back to referring page
            $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/student/my_interests';
            header('Location: ' . $referer);
            exit;
        } catch (\Exception $e) {
            error_log('Error in handleWithdrawInterest: ' . $e->getMessage());
            $_SESSION['error'] = 'An error occurred while processing your request';
            header('Location: ' . BASE_URL . '/error');
            exit;
        }
    }

    public function getInterests($studentId) {
        try {
            if (!isset($studentId)) {
                throw new \Exception("Student ID is required");
            }

            $query = "SELECT p.*, si.created_at as interest_date 
                     FROM programmes p 
                     JOIN student_interests si ON p.id = si.programme_id 
                     WHERE si.student_id = :student_id 
                     ORDER BY si.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            $interests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Add default image if not set
            foreach ($interests as &$programme) {
                $programme['image_url'] = $programme['image_url'] ?? '/assets/images/default-programme.jpg';
            }
            
            return $interests;
        } catch (\Exception $e) {
            error_log("Error getting interests: " . $e->getMessage());
            throw new \Exception("Failed to retrieve interests");
        }
    }

    public function getStudentDetails($studentId) {
        try {
            if (!isset($studentId)) {
                throw new \Exception("Student ID is required");
            }

            $query = "SELECT * FROM users WHERE id = :student_id AND role = 'student'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            $student = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$student) {
                throw new \Exception("Student not found");
            }
            
            return $student;
        } catch (\Exception $e) {
            error_log("Error getting student details: " . $e->getMessage());
            throw new \Exception("Failed to retrieve student details");
        }
    }
}
