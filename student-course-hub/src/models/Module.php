<?php

namespace App\Models;

class Module extends Model {
    public function __construct() {
        parent::__construct();
        error_log("Module model constructor called");
    }    public function getAllModules() {
        try {
            error_log("=== getAllModules START ===");
            error_log("Time: " . date('Y-m-d H:i:s'));
            
            // Debug database connection
            if (!$this->db) {
                error_log("Database connection is null in getAllModules");
                return [];
            }
            error_log("Database connection verified in getAllModules");

            // Query to get modules with programme and staff information
            $query = "SELECT 
                m.id, 
                m.title, 
                m.description,
                m.credits,
                m.year_of_study, 
                m.semester,
                m.staff_id,
                GROUP_CONCAT(p.title) as programme_name, 
                u.username as staff_name 
                FROM modules m 
                LEFT JOIN programme_modules pm ON m.id = pm.module_id
                LEFT JOIN programmes p ON pm.programme_id = p.id
                LEFT JOIN users u ON m.staff_id = u.id 
                GROUP BY m.id
                ORDER BY m.title ASC";

            error_log("getAllModules query: " . $query);
            
            $stmt = $this->db->prepare($query);
            $executed = $stmt->execute();
            
            if (!$executed) {
                error_log("Execute failed: " . print_r($stmt->errorInfo(), true));
                return [];
            }
            
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Number of modules found: " . count($results));
            
            if (!empty($results)) {
                error_log("Sample module data: " . print_r($results[0], true));
            }
            
            return $results;
            
        } catch (\PDOException $e) {
            error_log("Error in getAllModules: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }    public function createModule($title, $description, $credits, $year_of_study = null, $programme_id = null, $staff_id = null, $semester = 1) {
        try {
            $this->db->beginTransaction();

            error_log("createModule: Starting module creation");
            error_log("Data received - Title: $title, Programme ID: " . ($programme_id ?? 'null') . ", Staff ID: " . ($staff_id ?? 'null'));

            // Validate inputs
            if (empty($title)) {
                throw new \Exception("Title is required");
            }
            if (empty($credits) || !is_numeric($credits) || $credits <= 0) {
                throw new \Exception("Credits must be a positive number");
            }
            if (empty($year_of_study) || !is_numeric($year_of_study) || $year_of_study <= 0) {
                throw new \Exception("Year of Study must be a positive number");
            }
            if (!is_numeric($semester) || $semester < 1 || $semester > 2) {
                throw new \Exception("Semester must be 1 or 2");
            }

            // Check if programme exists if ID is provided
            if ($programme_id) {
                $programmeCheck = $this->db->prepare("SELECT COUNT(*) FROM programmes WHERE id = ?");
                $programmeCheck->execute([$programme_id]);
                if ($programmeCheck->fetchColumn() == 0) {
                    throw new \Exception("Invalid programme ID provided");
                }
                error_log("Programme exists check passed");
            }

            // Insert into modules table - removed programme_id from INSERT
            $query = "INSERT INTO modules (title, description, credits, year_of_study, staff_id, semester) 
                     VALUES (:title, :description, :credits, :year_of_study, :staff_id, :semester)";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':credits' => $credits,
                ':year_of_study' => $year_of_study,
                ':staff_id' => $staff_id,
                ':semester' => $semester
            ]);

            if (!$result) {
                $error = $stmt->errorInfo();
                throw new \Exception("Failed to create module: " . ($error[2] ?? 'Unknown error'));
            }
            
            $moduleId = $this->db->lastInsertId();
            error_log("Module created with ID: " . $moduleId);
            
            // Create programme association
            if ($programme_id) {
                error_log("Attempting to associate module with programme ID: " . $programme_id);
                
                $query = "INSERT INTO programme_modules (programme_id, module_id) VALUES (:programme_id, :module_id)";
                $stmt = $this->db->prepare($query);
                $result = $stmt->execute([
                    ':programme_id' => $programme_id,
                    ':module_id' => $moduleId
                ]);

                if (!$result) {
                    error_log("Failed to create programme association: " . print_r($stmt->errorInfo(), true));
                    throw new \Exception("Failed to associate module with programme");
                }
                error_log("Successfully associated module with programme");
            }

            $this->db->commit();
            return $moduleId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateModule($id, $title, $description, $credits, $yearOfStudy, $programmeId, $staffId = null, $semester = 1) {
        try {
            $sql = "UPDATE modules 
                   SET title = ?, description = ?, credits = ?, 
                       year_of_study = ?, staff_id = ?, semester = ?
                   WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $title,
                $description,
                $credits,
                $yearOfStudy,
                $staffId,
                $semester,
                $id
            ]);

            if ($result) {
                // Update programme_modules table
                $sql = "DELETE FROM programme_modules WHERE module_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id]);

                if ($programmeId) {
                    $sql = "INSERT INTO programme_modules (programme_id, module_id) VALUES (?, ?)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$programmeId, $id]);
                }
            }

            return true;
        } catch (\PDOException $e) {
            error_log('Error updating module: ' . $e->getMessage());
            throw new \Exception('Failed to update module: ' . $e->getMessage());
        }
    }    public function hasEnrolledStudents($id) {
        try {
            // First check if enrollments table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'enrollments'");
            if ($tableCheck->rowCount() === 0) {
                // Table doesn't exist, create it
                $sql = file_get_contents(__DIR__ . '/../../database/enrollments.sql');
                $this->db->exec($sql);
                return false; // No students can be enrolled if table was just created
            }

            $query = "SELECT COUNT(*) FROM enrollments WHERE module_id = ? AND status = 'enrolled'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("Error checking enrolled students: " . $e->getMessage());
            return false; // Assume no enrolled students if there's an error
        }
    }

    public function deleteModule($id) {
        // Check for enrolled students first
        if ($this->hasEnrolledStudents($id)) {
            throw new \Exception("Cannot delete module with enrolled students");
        }

        $query = "DELETE FROM modules WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }    public function getStats() {
        try {
            // Get total modules count
            $total = $this->db->query("
                SELECT COUNT(*) FROM modules"
            )->fetchColumn();
            
            // Get active modules count - modules that have staff assigned and are part of active programmes
            $active = $this->db->query("
                SELECT COUNT(DISTINCT m.id) 
                FROM modules m
                INNER JOIN programme_modules pm ON m.id = pm.module_id
                INNER JOIN programmes p ON pm.programme_id = p.id
                WHERE m.staff_id IS NOT NULL 
                AND p.is_published = 1"
            )->fetchColumn();
            
            error_log("Module stats - Total: $total, Active: $active");
            
            return [
                'total' => (int)$total,
                'active' => (int)$active
            ];
        } catch (\PDOException $e) {
            error_log("Error in getStats: " . $e->getMessage() . "\n");
            error_log("Error getting module stats: " . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0
            ];
        }
    }public function getModulesByStaffId($staffId) {
        $query = "SELECT 
            m.id,
            m.title as name,
            m.title as code,
            (SELECT COUNT(*) FROM enrollments e WHERE e.module_id = m.id) as student_count
        FROM modules m
        WHERE m.staff_id = :staff_id
        GROUP BY m.id, m.title";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':staff_id' => $staffId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getModulesByStaffIdAndProgrammeId($staffId, $programmeId) {
        try {
            $sql = "SELECT m.* FROM modules m 
                   INNER JOIN programme_modules pm ON m.id = pm.module_id 
                   WHERE m.staff_id = ? AND pm.programme_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$staffId, $programmeId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log('Error getting modules by staff and programme ID: ' . $e->getMessage());
            throw new \Exception('Error retrieving modules.');
        }
    }    public function getEnrolledStudents($moduleId) {
        $query = "SELECT 
            u.id,
            u.username,
            u.email,
            u.avatar_url,
            e.enrollment_date,
            e.grade
        FROM users u
        JOIN enrollments e ON u.id = e.student_id
        WHERE e.module_id = :module_id
        AND u.role = 'student'
        ORDER BY u.username ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':module_id' => $moduleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getModuleById($id) {
        $query = "SELECT 
            m.id,
            m.title,
            m.description,
            m.credits,
            m.year_of_study,
            m.staff_id,
            p.title as programme_name,
            p.id as programme_id,
            u.username as staff_name,
            (SELECT COUNT(*) FROM enrollments e WHERE e.module_id = m.id) as student_count
        FROM modules m 
        LEFT JOIN programme_modules pm ON m.id = pm.module_id
        LEFT JOIN programmes p ON pm.programme_id = p.id
        LEFT JOIN users u ON m.staff_id = u.id
        WHERE m.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getByProgramme($programmeId) {
        try {
            $query = "SELECT m.*, 
                    u.username as staff_name,
                    u.email as staff_email,
                    u.id as staff_id
                    FROM modules m
                    INNER JOIN programme_modules pm ON m.id = pm.module_id
                    LEFT JOIN users u ON m.staff_id = u.id
                    WHERE pm.programme_id = :programme_id
                    ORDER BY m.year_of_study, m.title";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':programme_id' => $programmeId]);
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting modules for programme: " . $e->getMessage());
            throw new \Exception("Failed to retrieve programme modules");
        }
    }
}
