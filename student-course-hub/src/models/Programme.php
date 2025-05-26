<?php

namespace App\Models;

class Programme extends Model {
    public function getAllProgrammes() {
        $stmt = $this->db->query("
            SELECT p.*, 
                   GROUP_CONCAT(DISTINCT m.id) as module_ids,
                   COUNT(DISTINCT m.id) as module_count
            FROM programmes p
            LEFT JOIN programme_modules pm ON p.id = pm.programme_id
            LEFT JOIN modules m ON pm.module_id = m.id
            GROUP BY p.id
        ");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO programmes (title, description, level, duration) 
                VALUES (:title, :description, :level, :duration)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':level' => $data['level'],
            ':duration' => $data['duration'] ?? '3 years'
        ]);
    }

    public function exists($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM programmes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getProgrammeStats() {
        $total = $this->db->query("SELECT COUNT(*) FROM programmes")->fetchColumn();
        $active = $this->db->query("SELECT COUNT(*) FROM programmes WHERE is_published = 1")->fetchColumn();
        
        return [
            'total' => $total,
            'active' => $active
        ];
    }

    public function findById($id) {
        // Get programme details with module count
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   COUNT(DISTINCT m.id) as module_count
            FROM programmes p
            LEFT JOIN programme_modules pm ON p.id = pm.programme_id
            LEFT JOIN modules m ON pm.module_id = m.id
            WHERE p.id = :id
            GROUP BY p.id
        ");
        $stmt->execute([':id' => $id]);
        $programme = $stmt->fetch();

        if ($programme) {
            // Get modules organized by year
            $stmt = $this->db->prepare("
                SELECT m.*, 
                       u.username as staff_name,
                       u.email as staff_email,
                       u.id as staff_id
                FROM modules m
                INNER JOIN programme_modules pm ON m.id = pm.module_id
                LEFT JOIN users u ON m.staff_id = u.id
                WHERE pm.programme_id = :id
                ORDER BY m.year_of_study, m.title
            ");
            $stmt->execute([':id' => $id]);
            $modules = [];
            
            while ($module = $stmt->fetch()) {
                $year = $module['year_of_study'] ?? 1;
                if (!isset($modules[$year])) {
                    $modules[$year] = [];
                }
                $module['shared_programmes'] = $this->getSharedProgrammes($module['id']);
                $modules[$year][] = $module;
            }

            $programme['modules'] = $modules;
            $programme['staff'] = $this->getStaffMembers($id);
        }

        return $programme;
    }

    public function update($data) {
        $is_published = isset($data['is_published']) ? (int)$data['is_published'] : 0;

        $sql = "UPDATE programmes 
                SET title = :title, 
                    description = :description, 
                    level = :level, 
                    duration = :duration,
                    is_published = :is_published 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':level' => $data['level'],
            ':duration' => $data['duration'] ?? '3 years',
            ':is_published' => $is_published
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM programmes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function hasStudentInterest($studentId, $programmeId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM student_interests 
            WHERE student_id = ? AND programme_id = ?
        ");
        $stmt->execute([$studentId, $programmeId]);
        return $stmt->fetchColumn() > 0;
    }

    public function addStudentInterest($studentId, $programmeId) {
        if ($this->hasStudentInterest($studentId, $programmeId)) {
            return false;
        }

        $stmt = $this->db->prepare("
            INSERT INTO student_interests (student_id, programme_id, registered_at)
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$studentId, $programmeId]);
    }

    public function removeStudentInterest($studentId, $programmeId) {
        $stmt = $this->db->prepare("
            DELETE FROM student_interests 
            WHERE student_id = ? AND programme_id = ?
        ");
        return $stmt->execute([$studentId, $programmeId]);
    }

    public function getInterestedStudents($programmeId) {
        $stmt = $this->db->prepare("
            SELECT u.*, si.registered_at
            FROM users u
            INNER JOIN student_interests si ON u.id = si.student_id
            WHERE si.programme_id = ?
            ORDER BY si.registered_at DESC
        ");
        $stmt->execute([$programmeId]);
        return $stmt->fetchAll();
    }

    public function getProgrammesByModuleIds($moduleIds) {
        if (empty($moduleIds)) {
            return [];
        }

        try {
            $placeholders = str_repeat('?,', count($moduleIds) - 1) . '?';
            $sql = "SELECT DISTINCT p.*, 
                           GROUP_CONCAT(pm.module_id) as module_ids 
                   FROM programmes p
                   INNER JOIN programme_modules pm ON p.id = pm.programme_id
                   WHERE pm.module_id IN ($placeholders)
                   GROUP BY p.id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($moduleIds);
            $programmes = $stmt->fetchAll();
            
            foreach ($programmes as &$programme) {
                $programme['module_ids'] = explode(',', $programme['module_ids']);
            }
            
            return $programmes;
        } catch (\PDOException $e) {
            error_log('Error getting programmes by module IDs: ' . $e->getMessage());
            throw new \Exception('Error retrieving programmes.');
        }
    }

    public function getStaffMembers($programmeId) {
        $stmt = $this->db->prepare("
            SELECT DISTINCT 
                u.id,
                u.username as name,
                u.email,
                'Module Leader' as role,
                u.avatar_url
            FROM users u
            INNER JOIN modules m ON u.id = m.staff_id
            INNER JOIN programme_modules pm ON m.id = pm.module_id
            WHERE pm.programme_id = ?
            AND u.role = 'staff'
            ORDER BY u.username
        ");
        $stmt->execute([$programmeId]);
        return $stmt->fetchAll();
    }

    public function getSharedProgrammes($moduleId) {
        $stmt = $this->db->prepare("
            SELECT p.id, p.title
            FROM programmes p
            INNER JOIN programme_modules pm ON p.id = pm.programme_id
            WHERE pm.module_id = ?
        ");
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }

    public function search($keyword) {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   COUNT(DISTINCT m.id) as module_count
            FROM programmes p
            LEFT JOIN programme_modules pm ON p.id = pm.programme_id
            LEFT JOIN modules m ON pm.module_id = m.id
            WHERE (p.title LIKE :keyword 
                  OR p.description LIKE :keyword)
                  AND p.is_published = 1
            GROUP BY p.id
            ORDER BY p.title
        ");
        $keyword = "%{$keyword}%";
        $stmt->execute([':keyword' => $keyword]);
        return $stmt->fetchAll();
    }

    public function removeAllModules($programmeId) {
        $stmt = $this->db->prepare("
            DELETE FROM programme_modules 
            WHERE programme_id = ?
        ");
        return $stmt->execute([$programmeId]);
    }

    public function addModule($programmeId, $moduleId) {
        $stmt = $this->db->prepare("
            INSERT INTO programme_modules (programme_id, module_id)
            VALUES (?, ?)
        ");
        return $stmt->execute([$programmeId, $moduleId]);
    }

    public function getDepartments() {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT department 
                FROM programmes 
                WHERE department IS NOT NULL 
                ORDER BY department
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            error_log('Error getting departments: ' . $e->getMessage());
            return [];
        }
    }

    public function getInterestCount($programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM student_interests 
                WHERE programme_id = ?
            ");
            $stmt->execute([$programmeId]);
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log('Error getting interest count: ' . $e->getMessage());
            return 0;
        }
    }

    public function getKeyFeatures($programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT feature 
                FROM programme_features 
                WHERE programme_id = ?
            ");
            $stmt->execute([$programmeId]);
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            error_log('Error getting key features: ' . $e->getMessage());
            return [];
        }
    }

    public function findAll($conditions = [], $params = [], $orderBy = 'title ASC') {
        try {
            $where = empty($conditions) ? "WHERE is_published = 1" : "WHERE is_published = 1 AND " . implode(" AND ", $conditions);
            $sql = "
                SELECT p.*,
                       (SELECT COUNT(*) FROM student_interests si WHERE si.programme_id = p.id) as interest_count,
                       (SELECT COUNT(*) FROM programme_modules pm WHERE pm.programme_id = p.id) as module_count
                FROM programmes p
                {$where}
                ORDER BY {$orderBy}
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $programmes = $stmt->fetchAll();

            // Enhance programmes with additional data
            foreach ($programmes as &$programme) {
                $programme['key_features'] = $this->getKeyFeatures($programme['id']);
                $programme['modules'] = $this->getModules($programme['id']);
            }

            return $programmes;
        } catch (\PDOException $e) {
            error_log('Error retrieving programmes: ' . $e->getMessage());
            throw new \Exception('Error retrieving programmes.');
        }
    }
}
