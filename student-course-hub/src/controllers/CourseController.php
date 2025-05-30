<?php

namespace App\Controllers;

use App\Models\Programme;
use App\Models\Module;
use App\Utils\ImageValidator;
use App\Utils\Cache;

class CourseController {
    private $programme;
    private $module;

    public function __construct() {
        $this->programme = new Programme();
        $this->module = new Module();
    }

    public function listProgrammes($filters = []) {
        try {
            $cacheKey = 'programmes_' . md5(serialize($filters));
            $cached = Cache::get($cacheKey);
            
            if ($cached !== false) {
                return $cached;
            }

            $conditions = ['is_published = 1'];
            $params = [];

            // Enhanced filters with better search
            if (!empty($filters['search'])) {
                $searchTerm = "%" . $filters['search'] . "%";
                $conditions[] = "(
                    title LIKE :search 
                    OR description LIKE :search 
                    OR keywords LIKE :search 
                    OR department LIKE :search
                )";
                $params[':search'] = $searchTerm;
            }

            if (!empty($filters['level'])) {
                $conditions[] = "level = :level";
                $params[':level'] = $filters['level'];
            }

            if (!empty($filters['duration'])) {
                $conditions[] = "duration = :duration";
                $params[':duration'] = $filters['duration'];
            }

            if (!empty($filters['department'])) {
                $conditions[] = "department = :department";
                $params[':department'] = $filters['department'];
            }

            // Dynamic sorting
            $validSortFields = ['title', 'interest_count', 'created_at'];
            $sortField = in_array($filters['sort_by'] ?? '', $validSortFields) ? $filters['sort_by'] : 'title';
            $sortOrder = ($filters['sort_order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
            $orderBy = "{$sortField} {$sortOrder}";
            
            $results = $this->programme->findAll($conditions, $params, $orderBy);

            // Add department list for filters
            $departments = $this->programme->getDepartments();
            $results = [
                'programmes' => $results,
                'departments' => $departments
            ];

            Cache::set($cacheKey, $results, 3600); // Cache for 1 hour
            
            return $results;
        } catch (Exception $e) {
            error_log("Error listing programmes: " . $e->getMessage());
            throw new Exception("Failed to retrieve programmes");
        }
    }

    public function getProgrammeDetails($id) {
        try {
            $programme = $this->programme->findById($id);
            if (!$programme || !$programme['is_published']) {
                throw new Exception("Programme not found");
            }

            // Get modules for each year
            $modules = $this->module->getByProgramme($id);
            $programme['modules'] = $this->organizeModulesByYear($modules);

            // Get staff information
            $programme['staff'] = $this->programme->getStaffMembers($id);

            return $programme;
        } catch (Exception $e) {
            error_log("Error getting programme details: " . $e->getMessage());
            throw new Exception("Failed to retrieve programme details");
        }
    }

    public function registerInterest($studentId, $programmeId) {
        try {
            if (!$this->programme->exists($programmeId)) {
                throw new Exception("Programme not found");
            }

            // Check if already registered
            if ($this->programme->hasStudentInterest($studentId, $programmeId)) {
                throw new Exception("Already registered interest in this programme");
            }

            return $this->programme->addStudentInterest($studentId, $programmeId);
        } catch (Exception $e) {
            error_log("Error registering interest: " . $e->getMessage());
            throw new Exception("Failed to register interest");
        }
    }

    public function withdrawInterest($studentId, $programmeId) {
        try {
            if (!$this->programme->hasStudentInterest($studentId, $programmeId)) {
                throw new Exception("No interest registered for this programme");
            }

            return $this->programme->removeStudentInterest($studentId, $programmeId);
        } catch (Exception $e) {
            error_log("Error withdrawing interest: " . $e->getMessage());
            throw new Exception("Failed to withdraw interest");
        }
    }

    public function getStudentInterests($studentId) {
        try {
            return $this->programme->getStudentInterests($studentId);
        } catch (Exception $e) {
            error_log("Error getting student interests: " . $e->getMessage());
            throw new Exception("Failed to retrieve interests");
        }
    }

    private function organizeModulesByYear($modules) {
        $organized = [];
        foreach ($modules as $module) {
            $year = $module['year_of_study'];
            if (!isset($organized[$year])) {
                $organized[$year] = [];
            }
            $organized[$year][] = $module;
        }
        ksort($organized);
        return $organized;
    }

    public function searchProgrammes($keyword) {
        try {
            return $this->programme->search($keyword);
        } catch (Exception $e) {
            error_log("Error searching programmes: " . $e->getMessage());
            throw new Exception("Search failed");
        }
    }

    public function getSharedModules($moduleId) {
        try {
            return $this->module->getSharedProgrammes($moduleId);
        } catch (Exception $e) {
            error_log("Error getting shared modules: " . $e->getMessage());
            throw new Exception("Failed to retrieve shared modules");
        }
    }

    public function uploadProgrammeImage($programmeId, $imageData) {
        try {
            // Validate image accessibility
            $validator = new ImageValidator();
            if (!$validator->isWCAG2Compliant($imageData)) {
                throw new Exception("Image does not meet accessibility requirements");
            }

            // Process and store image
            $imagePath = $this->processAndStoreImage($imageData);
            return $this->programme->updateImage($programmeId, $imagePath);
        } catch (Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            throw new Exception("Failed to upload image");
        }
    }

    public function getModuleUsage($moduleId) {
        try {
            $programmes = $this->module->getSharedProgrammes($moduleId);
            $usage = [
                'total_programmes' => count($programmes),
                'programmes' => $programmes,
                'is_shared' => count($programmes) > 1
            ];
            return $usage;
        } catch (Exception $e) {
            error_log("Error getting module usage: " . $e->getMessage());
            throw new Exception("Failed to get module usage");
        }
    }

    private function processAndStoreImage($imageData) {
        // Add image processing logic here
        // Including resizing, optimization, and alt text storage
        // ...existing code...
    }

    private function validateAccessibility($imagePath) {
        // Add accessibility validation logic
        // Including contrast checking and alt text validation
        // ...existing code...
    }
}