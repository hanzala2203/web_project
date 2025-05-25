<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Programme.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../utils/Notification.php';

class StudentController {
    private $db;
    private $student;
    private $programme;
    private $module;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->student = new Student($this->db);
        $this->programme = new Programme($this->db);
        $this->module = new Module($this->db);
    }

    public function registerInterest($courseId, $studentId) {
        try {
            // Validate inputs
            if (!$this->programme->exists($courseId)) {
                throw new Exception("Course not found");
            }

            if (!$this->student->exists($studentId)) {
                throw new Exception("Student not found");
            }

            // Check if already registered
            if ($this->student->hasInterest($studentId, $courseId)) {
                throw new Exception("Already registered interest in this course");
            }

            // Register interest
            return $this->student->addInterest($studentId, $courseId);

        } catch (Exception $e) {
            error_log("Error registering interest: " . $e->getMessage());
            throw new Exception("Failed to register interest: " . $e->getMessage());
        }
    }

    public function withdrawInterest($courseId, $studentId) {
        try {
            if (!$this->student->hasInterest($studentId, $courseId)) {
                throw new Exception("No interest registered for this course");
            }

            return $this->student->removeInterest($studentId, $courseId);

        } catch (Exception $e) {
            error_log("Error withdrawing interest: " . $e->getMessage());
            throw new Exception("Failed to withdraw interest: " . $e->getMessage());
        }
    }

    public function viewCourseDetails($courseId) {
        try {
            $course = $this->programme->findById($courseId);
            
            if (!$course || !$course['is_published']) {
                throw new Exception("Course not found");
            }

            // Enhanced module information
            $course['modules'] = $this->programme->getModules($courseId);
            foreach ($course['modules'] as &$module) {
                $module['shared_programmes'] = $this->module->getSharedProgrammes($module['id']);
                $module['accessibility_features'] = $this->getModuleAccessibilityFeatures($module['id']);
            }
            
            // Get course staff with detailed profiles
            $course['staff'] = $this->programme->getStaffMembers($courseId);
            
            // Add accessibility information
            $course['accessibility'] = [
                'has_transcripts' => true,
                'has_alt_formats' => true,
                'wcag_compliance' => 'AA'
            ];

            return $course;
        } catch (Exception $e) {
            error_log("Error viewing course details: " . $e->getMessage());
            throw new Exception("Failed to retrieve course details");
        }
    }

    public function viewDashboard($studentId) {
        try {
            // Get student details from users table
            $student = $this->student->findById($studentId);
            if (!$student || $student['role'] !== 'student') {
                throw new Exception("Student not found");
            }

            // Get interested courses
            $interestedCourses = $this->student->getInterestedCourses($studentId);

            // Get enrolled courses with progress
            $enrolledCourses = $this->student->getEnrolledCourses($studentId);
            foreach ($enrolledCourses as &$course) {
                $course['progress'] = $this->calculateCourseProgress($studentId, $course['id']);
            }

            // Get upcoming deadlines
            $deadlines = $this->student->getUpcomingDeadlines($studentId);

            // Get recommended courses
            $recommendedCourses = $this->getRecommendedCourses($studentId);

            return [
                'student' => $student,
                'interested_courses' => $interestedCourses,
                'recommended_courses' => $recommendedCourses,
                'enrolled_courses' => $enrolledCourses,
                'deadlines' => $deadlines
            ];

        } catch (Exception $e) {
            error_log("Error viewing dashboard: " . $e->getMessage());
            throw new Exception("Failed to load dashboard");
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
            // Get student's interested courses
            $interests = $this->student->getInterestedCourses($studentId);
            
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
}