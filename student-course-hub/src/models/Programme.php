<?php

namespace App\Models;

class Programme extends Model {    
    public function getAllProgrammes() {
        error_log("=== getAllProgrammes START ===");
        error_log("Time: " . date('Y-m-d H:i:s'));

        try {
            // Verify database connection
            try {
                $this->db->query("SELECT 1");
                error_log("Database connection verified");
            } catch (\PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed");
            }

            // First check if programmes table exists and has data
            try {
                $countCheck = $this->db->query("SELECT COUNT(*) FROM programmes");
                $totalCount = $countCheck->fetchColumn();
                error_log("Total programmes in database: " . $totalCount);
            } catch (\PDOException $e) {
                error_log("Error accessing programmes table: " . $e->getMessage());
                throw new \Exception("Error accessing programmes table");
            }

            // If no programmes exist, add sample data
            if ($totalCount == 0) {
                error_log("No programmes found, adding sample data");
                $this->addSampleProgrammes();
            }

            $query = "SELECT p.*, 
                       GROUP_CONCAT(DISTINCT m.id) as module_ids,
                       COUNT(DISTINCT m.id) as module_count
                FROM programmes p
                LEFT JOIN programme_modules pm ON p.id = pm.programme_id
                LEFT JOIN modules m ON pm.module_id = m.id
                GROUP BY p.id
                ORDER BY p.title";
            
            error_log("Executing query: " . $query);
            
            $stmt = $this->db->query($query);
            $results = $stmt->fetchAll();
            error_log("Retrieved " . count($results) . " programmes");
            
            if (!empty($results)) {
                error_log("Sample programme data: " . print_r($results[0], true));
            }

            // If still no data after trying to add sample data
            if (empty($results)) {
                error_log("WARNING: No programmes found even after attempting to add sample data");
            }

            return $results;
        }        catch (\PDOException $e) {
            error_log("Error in getAllProgrammes: " . $e->getMessage());
            return [];
        }
    }
    
    public function create($data) {
        error_log("=== Programme::create START ===");
        error_log("Time: " . date('Y-m-d H:i:s'));
        error_log("Data received: " . print_r($data, true));
        
        try {
            // Verify database connection first
            try {
                $this->db->query("SELECT 1");
                error_log("Database connection verified");
            } catch (\PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new \Exception("Database connection failed");
            }            $sql = "INSERT INTO programmes (title, description, level, duration, is_published) 
                    VALUES (:title, :description, :level, :duration, :is_published)";
            
            error_log("Preparing SQL: " . $sql);
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Failed to prepare statement: " . print_r($this->db->errorInfo(), true));
                throw new \Exception("Failed to prepare statement");
            }
            
            $params = [
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':level' => $data['level'],
                ':duration' => $data['duration'] ?? '3 years',
                ':is_published' => $data['is_published'] ?? 0
            ];
            
            error_log("Executing with params: " . print_r($params, true));
            $result = $stmt->execute($params);
            
            if (!$result) {
                error_log("Execute failed: " . print_r($stmt->errorInfo(), true));
                throw new \Exception("Failed to create programme");
            }
            
            $newId = $this->db->lastInsertId();
            error_log("Successfully created programme with ID: " . $newId);
            
            // Verify the created record
            $verifyStmt = $this->db->prepare("SELECT * FROM programmes WHERE id = ?");
            $verifyStmt->execute([$newId]);
            $createdProgramme = $verifyStmt->fetch();
            error_log("Verification of created programme: " . print_r($createdProgramme, true));
            
            error_log("=== Programme::create END ===");
            return $newId;
        } catch (\PDOException $e) {
            error_log("Database error in create: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new \Exception("Database error: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("Error in create: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
    public function exists($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM programmes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }

    public function titleExists($title, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM programmes WHERE LOWER(title) = LOWER(:title)";
        $params = [':title' => $title];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }    public function hasEnrolledStudents($id) {
        try {
            // First check if the table exists
            $tableCheck = $this->db->query("SHOW TABLES LIKE 'student_enrolments'");
            if ($tableCheck->rowCount() == 0) {
                // Table doesn't exist, so no students can be enrolled
                error_log("student_enrolments table does not exist, assuming no enrolled students");
                return false;
            }

            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM student_enrolments
                WHERE programme_id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("Error checking enrolled students: " . $e->getMessage());
            // If there's any error, assume no students are enrolled to allow deletion
            return false;
        }
    }public function getProgrammeStats() {
        try {
            $total = $this->db->query("SELECT COUNT(*) FROM programmes")->fetchColumn();
            
            // Consider a programme active if it's published and has at least one module
            $active = $this->db->query("
                SELECT COUNT(DISTINCT p.id) 
                FROM programmes p 
                INNER JOIN programme_modules pm ON p.id = pm.programme_id 
                WHERE p.is_published = 1")->fetchColumn();
            
            return [
                'total' => (int)$total ?? 0,
                'active' => (int)$active ?? 0
            ];
        } catch (\PDOException $e) {
            error_log("Error in getProgrammeStats: " . $e->getMessage());
            return ['total' => 0, 'active' => 0];
        }
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
    }    public function delete($id) {
        $sql = "DELETE FROM programmes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function updatePublishStatus($id, $isPublished) {
        $stmt = $this->db->prepare("
            UPDATE programmes 
            SET is_published = :is_published,
                updated_at = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':is_published' => $isPublished ? 1 : 0
        ]);
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
    }    public function getStaffMembers($programmeId) {
        $stmt = $this->db->prepare("            SELECT DISTINCT 
                u.id,
                u.username as name,
                u.email,
                u.avatar_url,
                'Module Leader' as role
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
        $stmt = $this->db->prepare("
            SELECT feature_name 
            FROM programme_features
            WHERE programme_id = ?
            ORDER BY display_order
        ");
        $stmt->execute([$programmeId]);
        
        $features = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        // If no custom features defined, return default features based on level and type
        if (empty($features)) {
            $programme = $this->findById($programmeId);
            $defaultFeatures = ['Industry-Relevant Curriculum'];
            
            if ($programme['level'] === 'undergraduate') {
                $defaultFeatures[] = 'Bachelor\'s Degree';
                $defaultFeatures[] = '3-4 Years Duration';
                $defaultFeatures[] = 'Foundation Year Available';
            } else {
                $defaultFeatures[] = 'Master\'s Degree';
                $defaultFeatures[] = '1-2 Years Duration';
                $defaultFeatures[] = 'Research Opportunities';
            }
            
            return $defaultFeatures;
        }
        
        return $features;
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
    }    public function searchProgrammes($query = null, $filters = []) {
        try {
            error_log("Starting searchProgrammes with query: " . print_r($query, true));
            error_log("Filters: " . print_r($filters, true));

            // First check if we have any programmes
            $check = $this->db->query("SELECT COUNT(*) FROM programmes")->fetchColumn();
            error_log("Total programmes in database: " . $check);
            
            if ($check == 0) {
                error_log("No programmes found, adding sample data");
                $this->addSampleProgrammes();
            }
            
            // Simple base query without joins to avoid table issues
            $sql = "SELECT p.*
                FROM programmes p 
                WHERE p.is_published = 1";
            
            $params = [];
            
            // Add search query if provided
            if (!empty($query)) {
                $sql .= " AND (
                    p.title LIKE :query 
                    OR p.description LIKE :query 
                    OR p.department LIKE :query
                )";
                $params[':query'] = "%{$query}%";
            }
            
            // Add level filter
            if (!empty($filters['level'])) {
                $sql .= " AND p.level = :level";
                $params[':level'] = $filters['level'];
            }
            
            // Add duration filter
            if (!empty($filters['duration'])) {
                $sql .= " AND p.duration LIKE :duration";
                $params[':duration'] = "%{$filters['duration']}%";
            }
            
            // Add department filter
            if (!empty($filters['department'])) {
                $sql .= " AND p.department = :department";
                $params[':department'] = $filters['department'];
            }
            
            // Add ordering
            $sql .= " ORDER BY p.title ASC";
            
            error_log("Final SQL: " . $sql);
            error_log("Parameters: " . print_r($params, true));
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $programmes = $stmt->fetchAll();
            
            error_log("Found " . count($programmes) . " programmes");
            
            // Add default data since we don't have all tables yet
            foreach ($programmes as &$programme) {
                $programme['module_count'] = 0;
                $programme['staff_count'] = 0;
                  // Default key features based on level
                $programme['key_features'] = $programme['level'] === 'undergraduate' 
                    ? ['Industry-Relevant Curriculum', 'Bachelor\'s Degree', '3-4 Years Duration']
                    : ['Advanced Specialization', 'Master\'s Degree', '1-2 Years Duration'];
            }

            return $programmes;
            
        } catch (\PDOException $e) {
            error_log('Error searching programmes: ' . $e->getMessage());
            throw new \Exception('Error searching programmes.');
        }
    }

    private function addSampleProgrammes() {
        try {
            $this->db->beginTransaction();
            
            // Add sample programmes
            $stmt = $this->db->prepare("
                INSERT INTO programmes (title, description, level, duration, is_published) 
                VALUES 
                (:title, :description, :level, :duration, 1)
            ");
            
            $programmes = [
                [
                    'title' => 'BSc Computer Science',
                    'description' => 'A comprehensive degree covering software development, algorithms, and computer systems',
                    'level' => 'undergraduate',
                    'duration' => '3 years'
                ],
                [
                    'title' => 'MSc Data Science',
                    'description' => 'Advanced study of data analytics, machine learning, and statistical methods',
                    'level' => 'postgraduate',
                    'duration' => '1 year'
                ],
                [
                    'title' => 'BSc Software Engineering',
                    'description' => 'Focused on software development practices, project management, and system design',
                    'level' => 'undergraduate',
                    'duration' => '3 years'
                ]
            ];
            
            foreach ($programmes as $prog) {
                $stmt->execute([
                    ':title' => $prog['title'],
                    ':description' => $prog['description'],
                    ':level' => $prog['level'],
                    ':duration' => $prog['duration']
                ]);
            }
            
            $this->db->commit();
            error_log("Added sample programmes successfully");
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error adding sample programmes: " . $e->getMessage());
        }
    }

    public function getProgrammeStructure($programmeId) {
        $sql = "SELECT y.year_number, s.semester_number,
                       m.id as module_id, m.title as module_title,
                       m.description as module_description,
                       m.credits, m.image_url,
                       GROUP_CONCAT(DISTINCT u.id) as staff_ids,
                       GROUP_CONCAT(DISTINCT u.name) as staff_names,
                       GROUP_CONCAT(DISTINCT u.role) as staff_roles
                FROM programme_years y
                JOIN programme_semesters s ON y.id = s.year_id
                JOIN modules m ON s.id = m.semester_id
                LEFT JOIN module_staff ms ON m.id = ms.module_id
                LEFT JOIN users u ON ms.staff_id = u.id
                WHERE y.programme_id = :programme_id
                GROUP BY y.year_number, s.semester_number, m.id
                ORDER BY y.year_number, s.semester_number, m.title";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':programme_id' => $programmeId]);
        
        $structure = [];
        while ($row = $stmt->fetch()) {
            $year = $row['year_number'];
            $semester = $row['semester_number'];
            
            if (!isset($structure[$year])) {
                $structure[$year] = [];
            }
            if (!isset($structure[$year][$semester])) {
                $structure[$year][$semester] = [];
            }
            
            // Process staff information
            $staffIds = explode(',', $row['staff_ids']);
            $staffNames = explode(',', $row['staff_names']);
            $staffRoles = explode(',', $row['staff_roles']);
            $staff = [];
            foreach ($staffIds as $i => $staffId) {
                if ($staffId) {
                    $staff[] = [
                        'id' => $staffId,
                        'name' => $staffNames[$i],
                        'role' => $staffRoles[$i]
                    ];
                }
            }
            
            $structure[$year][$semester][] = [
                'id' => $row['module_id'],
                'title' => $row['module_title'],
                'description' => $row['module_description'],
                'credits' => $row['credits'],
                'image_url' => $row['image_url'],
                'staff' => $staff
            ];
        }
        
        return $structure;
    }

    public function updateProgrammeImage($programmeId, $imagePath) {
        $sql = "UPDATE programmes 
                SET image_url = :image_url 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $programmeId,
            ':image_url' => $imagePath
        ]);
    }

    public function getAllPublished() {
        $stmt = $this->db->query("
            SELECT p.*, 
                   GROUP_CONCAT(DISTINCT m.id) as module_ids,
                   COUNT(DISTINCT m.id) as module_count
            FROM programmes p
            LEFT JOIN programme_modules pm ON p.id = pm.programme_id
            LEFT JOIN modules m ON pm.module_id = m.id
            WHERE p.is_published = 1
            GROUP BY p.id
            ORDER BY p.title
        ");
        return $stmt->fetchAll();
    }

    public function getModules($programmeId) {
        try {
            $sql = "SELECT m.*, u.username as staff_name, u.email as staff_email
                   FROM modules m
                   INNER JOIN programme_modules pm ON m.id = pm.module_id
                   LEFT JOIN users u ON m.staff_id = u.id
                   WHERE pm.programme_id = :programme_id
                   ORDER BY m.year_of_study, m.title";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':programme_id' => $programmeId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Error getting modules for programme: " . $e->getMessage());
            throw new \Exception("Failed to retrieve programme modules");
        }
    }

    public function getProgrammeStudents($programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT u.*, si.created_at as registered_at,
                       CASE 
                           WHEN e.id IS NOT NULL THEN 'active'
                           ELSE 'interested'
                       END as status
                FROM users u
                JOIN student_interests si ON u.id = si.student_id
                LEFT JOIN enrollments e ON u.id = e.student_id AND e.programme_id = si.programme_id
                WHERE si.programme_id = :programme_id
                ORDER BY si.created_at DESC
            ");
            
            $stmt->execute([':programme_id' => $programmeId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log('Error getting programme students: ' . $e->getMessage());
            throw new \Exception('Error retrieving programme students.');
        }
    }
}
