<?php

namespace App\Models;

use PDO;

class Student extends Model {
    private $table = 'users';

    public function removeInterest($studentId, $programmeId) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM student_interests 
                WHERE student_id = :student_id 
                AND programme_id = :programme_id
            ");
            return $stmt->execute([
                ':student_id' => $studentId,
                ':programme_id' => $programmeId
            ]);
        } catch (\Exception $e) {
            error_log("Error removing interest: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        $query = "SELECT u.* 
                 FROM {$this->table} u
                 WHERE u.id = :id AND u.role = 'student'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            error_log("Student not found with ID: " . $id);
            return null;
        }
        return $result;
    }

    public function exists($id) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getInterestedProgrammes($studentId) {
        $query = "SELECT p.*, si.created_at as interest_date 
                 FROM programmes p 
                 INNER JOIN student_interests si ON p.id = si.programme_id 
                 WHERE si.student_id = :student_id
                 ORDER BY si.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting interested programmes: " . $e->getMessage());
            return [];
        }
    }

    public function hasInterest($studentId, $programmeId) {
        $query = "SELECT COUNT(*) FROM student_interests 
                 WHERE student_id = :student_id 
                 AND programme_id = :programme_id";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':student_id' => $studentId,
                ':programme_id' => $programmeId
            ]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking interest: " . $e->getMessage());
            return false;
        }
    }

    public function addInterest($studentId, $programmeId) {
        if ($this->hasInterest($studentId, $programmeId)) {
            error_log("Student already has interest - Student ID: $studentId, Programme ID: $programmeId");
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO student_interests (student_id, programme_id, created_at, registered_at) 
                VALUES (:student_id, :programme_id, NOW(), NOW())
            ");
            $result = $stmt->execute([
                ':student_id' => $studentId,
                ':programme_id' => $ProgrammeId
            ]);

            if (!$result) {
                error_log("Database error in addInterest - " . json_encode($stmt->errorInfo()));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            error_log("Error adding interest: " . $e->getMessage());
            return false;
        }
    }

    public function registerInterest($studentId, $programmeId) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO student_interests (student_id, programme_id)
                VALUES (:student_id, :programme_id)
            ");
            return $stmt->execute([
                ':student_id' => $studentId,
                ':programme_id' => $programmeId
            ]);
        } catch (\Exception $e) {
            // Check if error is due to duplicate entry
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                error_log("Interest already registered");
                return ['error' => 'Interest already registered'];
            }
            error_log("Error registering interest: " . $e->getMessage());
            return false;
        }
    }

    public function getStudentInterests($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, si.created_at as interest_date
                FROM student_interests si
                JOIN programmes p ON si.programme_id = p.id
                WHERE si.student_id = :student_id
                ORDER BY si.created_at DESC
            ");
            $stmt->execute([':student_id' => $studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting student interests: " . $e->getMessage());
            return [];
        }
    }

    public function hasRegisteredInterest($studentId, $programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM student_interests 
                WHERE student_id = :student_id 
                AND programme_id = :programme_id
            ");
            $stmt->execute([
                ':student_id' => $studentId,
                ':programme_id' => $programmeId
            ]);
            return (int)$stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking interest: " . $e->getMessage());
            return false;
        }
    }

    public function updatePreferences($studentId, $preferences) {
        try { // Added try block
            $query = "UPDATE {$this->table} 
                     SET preferences = :preferences 
                     WHERE id = :id AND role = 'student'"; // Added role check for safety
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $studentId);
            // Encode preferences if it's an array or object, otherwise store as is if it's already a string.
            $encodedPreferences = is_string($preferences) ? $preferences : json_encode($preferences);
            $stmt->bindParam(':preferences', $encodedPreferences);
            
            $success = $stmt->execute();
            
            if (!$success) {
                // It's good to log the actual PDO error if possible
                error_log("Failed to update preferences for student ID: " . $studentId . " - Error: " . implode(" ", $stmt->errorInfo()));
                throw new \Exception("Failed to update preferences");
            }
            
            return true;
        } catch (\PDOException $e) { // Catch PDOException specifically
            error_log("Error updating student preferences: " . $e->getMessage());
            throw new \Exception("Failed to update student preferences due to a database error.");
        }
    }

    public function getStats() {
        try {
            // Get total count of students
            $total = $this->db->query("
                SELECT COUNT(*) 
                FROM users 
                WHERE role = 'student'"
            )->fetchColumn();
            
            // Get new students in the last 30 days
            $newThisMonth = $this->db->query("
                SELECT COUNT(*) 
                FROM users 
                WHERE role = 'student' 
                AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)"
            )->fetchColumn();
            
            error_log("Student stats - Total: $total, New: $newThisMonth");
            
            return [
                'total' => (int)$total,
                'new_this_month' => (int)$newThisMonth
            ];
        } catch (\PDOException $e) {
            error_log("Error in getStats: " . $e->getMessage());
            return ['total' => 0, 'new_this_month' => 0];
        }
    }

    // public function getAllStudents($filters = []) {
    //     try {
    //         date_default_timezone_set('Asia/Karachi');
    //         $logFile = dirname(__DIR__, 2) . '/admin_debug.log';
    //         $logMessage = sprintf(
    //             "=== getAllStudents START ===\nTime (Karachi): %s\nFilters received: %s\nDatabase connection: %s\n",
    //             date('Y-m-d H:i:s T'),
    //             print_r($filters, true),
    //             ($this->db ? "Connected" : "Not connected")
    //         );
    //         file_put_contents($logFile, $logMessage, FILE_APPEND);
            
    //         // Verify database connection
    //         try {
    //             $this->db->query("SELECT 1");
    //             error_log("Database connection verified");
    //         } catch (\PDOException $e) {
    //             error_log("Database connection failed: " . $e->getMessage());
    //             throw new \Exception("Database connection failed");
    //         }
            
    //         // Verify users table exists and has data
    //         try {
    //             $countCheck = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
    //             $totalStudents = $countCheck->fetchColumn();
    //             error_log("Total students in database: " . $totalStudents);
    //         } catch (\PDOException $e) {
    //             error_log("Error accessing users table: " . $e->getMessage());
    //             throw new \Exception("Error accessing users table");
    //         }
            
    //         $conditions = [];
    //         $params = [];
            
    //         // Only show students, not admins
    //         $conditions[] = "u.role = :role";
    //         $params[':role'] = 'student';
            
    //         if (!empty($filters['search'])) {
    //             $conditions[] = "(u.username LIKE :search OR u.email LIKE :search)";
    //             $params[':search'] = "%" . $filters['search'] . "%";
    //         }
            
    //         if (!empty($filters['programme'])) {
    //             $conditions[] = "EXISTS (
    //                 SELECT 1 FROM student_interests si2 
    //                 WHERE si2.student_id = u.id 
    //                 AND si2.programme_id = :programme_id
    //             )";
    //             $params[':programme_id'] = $filters['programme'];
    //         }

    //         // Build the base query using a correlated subquery for interests
    //         // Build base query with better error detection
    //         $query = "SELECT 
    //                  u.id, 
    //                  u.username, 
    //                  u.email, 
    //                  u.created_at, 
    //                  u.role,
    //                  COALESCE(
    //                      (SELECT GROUP_CONCAT(DISTINCT p2.title ORDER BY p2.title SEPARATOR ', ')
    //                       FROM student_interests si2 
    //                       LEFT JOIN programmes p2 ON si2.programme_id = p2.id AND p2.is_published = 1
    //                       WHERE si2.student_id = u.id), 
    //                      ''
    //                  ) as interests
    //                  FROM users u";

    //         // Add WHERE clause if there are conditions
    //         if (!empty($conditions)) {
    //             $query .= " WHERE " . implode(" AND ", $conditions);
    //         }
            
    //         // Add ORDER BY (no need for GROUP BY since we're using a subquery)
    //         $query .= " ORDER BY u.username";

    //         error_log("Executing query: " . $query);
    //         error_log("With params: " . print_r($params, true));

    //         $stmt = $this->db->prepare($query);
    //         foreach ($params as $key => $value) {
    //             $stmt->bindValue($key, $value);
    //         }
            
    //         try {
    //             $stmt->execute();
    //             $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    //             error_log("getAllStudents query executed successfully");
    //             error_log("Raw query results: " . print_r($students, true));
    //             error_log("Number of students found: " . count($students));
                
    //             if (empty($students)) {
    //                 error_log("WARNING: No students found in database. This might indicate a problem.");
    //                 return [];
    //             }

    //             // Process each student's interests
    //             foreach ($students as &$student) {
    //                 // Convert null interests to empty string
    //                 $student['interests'] = $student['interests'] ?? '';
                    
    //                 // Convert comma-separated string to array of titles if not empty
    //                 if (!empty($student['interests'])) {
    //                     $titles = array_map('trim', explode(',', $student['interests']));
    //                     $student['interests'] = array_map(function($title) {
    //                         return ['title' => $title];
    //                     }, $titles);
    //                 } else {
    //                     $student['interests'] = [];
    //                 }
    //             }
    //             unset($student); // Unset reference to last element

    //             return $students;

    //         } catch (\PDOException $e) {
    //             error_log("Error executing getAllStudents query: " . $e->getMessage());
    //             error_log("SQL State: " . $e->errorInfo[0]);
    //             error_log("Error Code: " . $e->errorInfo[1]);
    //             error_log("Error Message: " . $e->errorInfo[2]);
    //             throw new \Exception("Database error while fetching students");
    //         }
            
    //     } catch (\PDOException $e) {
    //         error_log("Database error in getAllStudents: " . $e->getMessage());
    //         return [];
    //     }
    // }

    // public function getAllStudents($filters = []) {
    //     try {

    //         // Verify database connection
    //         $this->db->query("SELECT 1");
    //         // Check total users and roles for debugging
    //         $countCheck = $this->db->query("SELECT COUNT(*) FROM users");
    //         $totalUsers = $countCheck->fetchColumn();
    //         error_log("Total users in database: $totalUsers");
    //         $countCheck = $this->db->query("SELECT DISTINCT role FROM users");
    //         $roles = $countCheck->fetchAll(PDO::FETCH_COLUMN);
    //         error_log("Available roles: " . print_r($roles, true));

    //         $conditions = [];
    //         $params = [];
            
    //         // Always filter for students
    //         $conditions[] = "LOWER(u.role) = :role";
    //         $params[':role'] = 'student';
            
    //         // Apply search filter on username only
    //         if (!empty($filters['search'])) {
    //             $conditions[] = "u.username LIKE :search";
    //             $params[':search'] = "%" . $filters['search'] . "%";
    //         }

    //         // Build the query
    //         $query = "SELECT 
    //                  u.id, 
    //                  u.username, 
    //                  u.email, 
    //                  u.created_at, 
    //                  u.role
    //                  FROM users u";

    //         if (!empty($conditions)) {
    //             $query .= " WHERE " . implode(" AND ", $conditions);
    //         }
            
    //         $query .= " ORDER BY u.username";

    //         error_log("Executing query: $query");
    //         error_log("With params: " . print_r($params, true));

    //         $stmt = $this->db->prepare($query);
    //         foreach ($params as $key => $value) {
    //             $stmt->bindValue($key, $value);
    //         }
            
    //         $stmt->execute();
    //         $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //         error_log("Number of students found: " . count($students));
    //         error_log("Raw query results: " . print_r($students, true));

    //         if (empty($students)) {
    //             error_log("WARNING: No students found in database.");
    //         }

    //         return $students;
    //     } catch (\PDOException $e) {
    //         error_log("Database error in getAllStudents: " . $e->getMessage());
    //         return [];
    //     }
    // }
    // public function getAllStudents($filters = []) {
    //     try {
    //         $query = "SELECT id, username, email, created_at, role FROM users WHERE LOWER(role) = :role";
    //         $params = [':role' => 'student'];

    //         if (!empty($filters['search'])) {
    //             $query .= " AND username LIKE :search";
    //             $params[':search'] = "%{$filters['search']}%";
    //         }

    //         $query .= " ORDER BY username";

    //         $stmt = $this->db->prepare($query);
    //         $stmt->execute($params);
    //         $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         error_log("getAllStudents: Found " . count($students) . " students");
    //         return $students;
    //     } catch (\PDOException $e) {
    //         error_log("getAllStudents error: " . $e->getMessage());
    //         return [];
    //     }
    // }

    // public function getAllStudents($filters = []) {
    //     echo "Entered getAllStudents<br>";
    //     try {
    //         $query = "SELECT id, username, email, created_at, role FROM users WHERE LOWER(role) = :role";
    //         $params = [':role' => 'student'];

    //         if (!empty($filters['search'])) {
    //             $query .= " AND username LIKE :search";
    //             $params[':search'] = "%{$filters['search']}%";
    //         }

    //         $query .= " ORDER BY username";

    //         $stmt = $this->db->prepare($query);
    //         $stmt->execute($params);
    //         $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         echo "Found " . count($students) . " students<br>";
    //         return $students;
    //     } catch (\PDOException $e) {
    //         echo "Error in getAllStudents: " . $e->getMessage() . "<br>";
    //         return [];
    //     }
    // }
    public function getAllStudents($filters = []) {
        echo "<script>console.log('Entered getAllStudents');</script>";
        try {
            $query = "SELECT id, username, email, created_at, role FROM users WHERE LOWER(role) = :role";
            $params = [':role' => 'student'];

            if (!empty($filters['search'])) {
                $query .= " AND username LIKE :search";
                $params[':search'] = "%{$filters['search']}%";
            }

            $query .= " ORDER BY username";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<script>console.log('Found " . count($students) . " students');</script>";
            return $students;
        } catch (\PDOException $e) {
            echo "<script>console.log('Error in getAllStudents: " . addslashes($e->getMessage()) . "');</script>";
            return [];
        }
    }
    public function getStudentDetails($studentId, $moduleId) {
        $query = "SELECT u.*, 
                        me.enrolled_date,
                        me.progress as module_progress,
                        me.last_activity_date
                 FROM users u
                 LEFT JOIN module_enrollments me ON u.id = me.student_id 
                 WHERE u.id = :student_id 
                 AND me.module_id = :module_id 
                 AND u.role = 'student'";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':module_id', $moduleId);
            $stmt->execute();
            
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($student) {
                // Get student's assignments for this module
                $assignmentsQuery = "SELECT 
                    a.title,
                    a.due_date,
                    CASE 
                        WHEN sa.submitted_at IS NOT NULL THEN 'Submitted'
                        ELSE 'Pending'
                    END as status,
                    sa.grade
                    FROM assignments a
                    LEFT JOIN student_assignments sa ON a.id = sa.assignment_id 
                        AND sa.student_id = :student_id
                    WHERE a.module_id = :module_id
                    ORDER BY a.due_date ASC";
                
                $stmt = $this->db->prepare($assignmentsQuery);
                $stmt->bindParam(':student_id', $studentId);
                $stmt->bindParam(':module_id', $moduleId);
                $stmt->execute();
                
                $student['assignments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $student;
        } catch (PDOException $e) {
            error_log("Error getting student details: " . $e->getMessage());
            return null;
        }
    }

    public function getEnrolledCourses($studentId) {
        try {
            $query = "SELECT p.*, e.enrollment_date, e.status
                     FROM programmes p 
                     INNER JOIN enrollments e ON p.id = e.programme_id 
                     WHERE e.student_id = :student_id
                     ORDER BY e.enrollment_date DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting enrolled courses for student ID {$studentId}: " . $e->getMessage());
            return [];
        }
    }

    public function getUpcomingDeadlines($studentId) {
        try {
            // Get deadlines for enrolled courses within next 30 days
            $query = "SELECT a.*, m.title as module_title
                     FROM assignments a
                     JOIN modules m ON a.module_id = m.id
                     JOIN enrollments e ON m.programme_id = e.programme_id
                     WHERE e.student_id = :student_id
                     AND a.deadline >= CURRENT_DATE
                     ORDER BY a.deadline ASC LIMIT 5";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getUpcomingDeadlines: " . $e->getMessage());
            return [];
        }
    }

    public function getInterests($studentId) {
        try {
            $query = "SELECT p.*, 
                     COALESCE(si.created_at, si.registered_at) as interest_date
                     FROM programmes p 
                     INNER JOIN student_interests si ON p.id = si.programme_id 
                     WHERE si.student_id = :student_id AND p.is_published = 1
                     ORDER BY COALESCE(si.created_at, si.registered_at) DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            $interests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Enhance each programme with additional information
            foreach ($interests as &$programme) {
                // Get module count
                $moduleQuery = "SELECT COUNT(*) FROM programme_modules WHERE programme_id = :programme_id";
                $moduleStmt = $this->db->prepare($moduleQuery);
                $moduleStmt->bindParam(':programme_id', $programme['id']);
                $moduleStmt->execute();
                $programme['module_count'] = (int)$moduleStmt->fetchColumn();

                // Format the interest date
                $programme['interest_date_formatted'] = date('F j, Y', strtotime($programme['interest_date']));
            }
            
            return $interests;
        } catch (\Exception $e) {
            error_log("Error getting interests for student ID {$studentId}: " . $e->getMessage());
            throw new \Exception("Failed to retrieve student interests");
        }
    }
}