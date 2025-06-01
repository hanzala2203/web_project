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
            
            // Direct count check
            try {
                $count = $this->db->query("SELECT COUNT(*) FROM modules")->fetchColumn();
                error_log("Direct module count: " . $count);
            } catch (\PDOException $e) {
                error_log("Error counting modules: " . $e->getMessage());
            }

            // Check if modules table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'modules'")->rowCount();
            if ($tableCheck === 0) {
                error_log("Modules table does not exist!");
                return [];
            }
            error_log("Modules table exists");// Join with programme_modules, programmes and users tables
            $query = "SELECT 
                    m.id, 
                    m.title, 
                    m.credits,
                    m.year_of_study, 
                    m.semester,
                    m.staff_id,
                    p.title as programme_name, 
                    u.username as staff_name 
                    FROM modules m 
                    LEFT JOIN programme_modules pm ON m.id = pm.module_id
                    LEFT JOIN programmes p ON pm.programme_id = p.id
                    LEFT JOIN users u ON m.staff_id = u.id 
                    ORDER BY m.title ASC";

            error_log("getAllModules query: " . $query);
            error_log("Checking modules table directly...");
            $checkQuery = "SELECT COUNT(*) FROM modules";
            $count = $this->db->query($checkQuery)->fetchColumn();
            error_log("Raw modules table count: " . $count);
              $stmt = $this->db->prepare($query);
            error_log("Prepared statement created, executing query...");
            
            try {
                $executed = $stmt->execute();
                if (!$executed) {
                    error_log("Failed to execute getAllModules query");
                    error_log("PDO error info: " . print_r($stmt->errorInfo(), true));
                    return [];
                }
            } catch (\PDOException $e) {
                error_log("PDO Exception in getAllModules: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                return [];
            }
            
            error_log("Query executed successfully, fetching results...");
            $modules = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            error_log("Fetched " . count($modules) . " modules");
            error_log("First module data: " . print_r($modules[0] ?? "No modules found", true));
            error_log("getAllModules found " . count($modules) . " modules");
            error_log("Module data: " . print_r($modules, true));
            
            return $modules;
        } catch (\PDOException $e) {
            error_log("Error in getAllModules: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [];
        }
    }

    public function createModule($title, $description, $credits, $year_of_study = null, $programme_id = null, $staff_id = null) {
        $query = "INSERT INTO modules (title, description, credits, year_of_study, staff_id) 
                 VALUES (:title, :description, :credits, :year_of_study, :staff_id)";
        $stmt = $this->db->prepare($query);
        $result = $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':credits' => $credits,
            ':year_of_study' => $year_of_study,
            ':staff_id' => $staff_id
        ]);

        if ($result && $programme_id) {
            $moduleId = $this->db->lastInsertId();
            $query = "INSERT INTO programme_modules (programme_id, module_id) VALUES (:programme_id, :module_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':programme_id' => $programme_id,
                ':module_id' => $moduleId
            ]);
        }

        return $result;
    }

    public function updateModule($id, $title, $description, $credits, $yearOfStudy, $programmeId, $staffId = null) {
        try {
            $sql = "UPDATE modules 
                   SET title = ?, description = ?, credits = ?, 
                       year_of_study = ?, staff_id = ?
                   WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $title,
                $description,
                $credits,
                $yearOfStudy,
                $staffId,
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
    }

    public function deleteModule($id) {
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
    }

    public function getEnrolledStudents($moduleId) {
        $query = "SELECT 
            u.id,
            u.username,
            u.email,
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
}
