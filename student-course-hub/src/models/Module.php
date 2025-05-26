<?php

namespace App\Models;

class Module extends Model {
    public function getAllModules() {
        // Join with programme_modules, programmes and users tables
        $query = "SELECT m.*, p.title as programme_name, u.username as staff_name 
                  FROM modules m 
                  LEFT JOIN programme_modules pm ON m.id = pm.module_id
                  LEFT JOIN programmes p ON pm.programme_id = p.id
                  LEFT JOIN users u ON m.staff_id = u.id 
                  ORDER BY m.title ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
    }

    public function getStats() {
        try {
            $total = $this->db->query("SELECT COUNT(*) FROM modules")->fetchColumn();
            $active = $this->db->query("SELECT COUNT(*) FROM modules WHERE staff_id IS NOT NULL")->fetchColumn();
            
            return [
                'total' => $total,
                'active' => $active
            ];
        } catch (\PDOException $e) {
            error_log("Error getting module stats: " . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0
            ];
        }
    }    public function getModulesByStaffId($staffId) {
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
