<?php

namespace App\Models;

use PDO;

class Student extends Model {
    private $table = 'users';

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

    public function getInterestedCourses($studentId) {
        $query = "SELECT p.* FROM programmes p 
                 INNER JOIN student_interests si ON p.id = si.programme_id 
                 WHERE si.student_id = :student_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addInterest($studentId, $courseId) {
        $query = "INSERT INTO student_interests (student_id, programme_id) 
                 VALUES (:student_id, :programme_id)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':programme_id', $courseId);
        
        return $stmt->execute();
    }

    public function removeInterest($studentId, $courseId) {
        $query = "DELETE FROM student_interests 
                 WHERE student_id = :student_id AND programme_id = :programme_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':programme_id', $courseId);
        
        return $stmt->execute();
    }

    public function hasInterest($studentId, $courseId) {
        $query = "SELECT COUNT(*) FROM student_interests 
                 WHERE student_id = :student_id AND programme_id = :programme_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->bindParam(':programme_id', $courseId);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
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
            // Get total students count
            $total = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE role = 'student'")->fetchColumn();
            
            // Get new students this month
            $newThisMonth = $this->db->query(
                "SELECT COUNT(*) FROM {$this->table} 
                WHERE role = 'student' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"
            )->fetchColumn();
            
            // Get student interests count for the past week
            $pendingInterests = $this->db->query(
                "SELECT COUNT(*) FROM student_interests 
                WHERE registered_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)"
            )->fetchColumn();
            
            return [
                'total' => $total,
                'new_this_month' => $newThisMonth,
                'pending_interests' => $pendingInterests
            ];
        } catch (PDOException $e) {
            error_log("Error getting student stats: " . $e->getMessage());
            return [
                'total' => 0,
                'new_this_month' => 0,
                'pending_interests' => 0
            ];
        }
    }

    public function getAllStudents($filters = []) {
        try {
            $conditions = [];
            $params = [];
            
            // Only show students, not admins
            $conditions[] = "u.role = :role";
            $params[':role'] = 'student';
            
            if (!empty($filters['search'])) {
                $conditions[] = "(u.username LIKE :search OR u.email LIKE :search)";
                $params[':search'] = "%" . $filters['search'] . "%";
            }
            
            if (!empty($filters['programme'])) {
                $conditions[] = "p.id = :programme_id";
                $params[':programme_id'] = $filters['programme'];
            }

            $query = "SELECT u.id, u.username, u.email, u.created_at, u.role,
                            GROUP_CONCAT(DISTINCT p.title SEPARATOR ', ') as interests
                    FROM {$this->table} u
                    LEFT JOIN student_interests si ON u.id = si.student_id
                    LEFT JOIN programmes p ON si.programme_id = p.id
                    WHERE " . implode(" AND ", $conditions) . "
                    GROUP BY u.id, u.username, u.email, u.created_at, u.role
                    ORDER BY u.username";

            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getAllStudents: " . $e->getMessage());
            throw $e;
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Corrected: removed extra backslash

        // Convert comma-separated interests string to an array of associative arrays
        // to match the view's expectation (array of interests, each with a 'title')
        foreach ($students as &$student) {
            if (!empty($student['interests'])) {
                $interestTitles = explode(', ', $student['interests']);
                $student['interests'] = [];
                foreach ($interestTitles as $title) {
                    $student['interests'][] = ['title' => $title];
                }
            } else {
                $student['interests'] = []; // Ensure it's an empty array if no interests
            }
        }
        unset($student); // Unset reference to last element

        return $students;
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
        $query = "SELECT DISTINCT p.*, e.enrollment_date, e.status 
                 FROM programmes p 
                 INNER JOIN programme_modules pm ON pm.programme_id = p.id
                 INNER JOIN modules m ON m.id = pm.module_id
                 INNER JOIN enrollments e ON m.id = e.module_id 
                 WHERE e.student_id = :student_id 
                 ORDER BY e.enrollment_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUpcomingDeadlines($studentId) {
        $query = "SELECT 
                    m.id,
                    m.title as name,
                    p.title as programme,
                    e.enrollment_date as due_date,
                    CASE 
                        WHEN e.status = 'completed' THEN 100
                        ELSE 0
                    END as progress,
                    e.status
                 FROM modules m
                 INNER JOIN enrollments e ON m.id = e.module_id
                 INNER JOIN programme_modules pm ON m.id = pm.module_id
                 INNER JOIN programmes p ON pm.programme_id = p.id
                 WHERE e.student_id = :student_id 
                 AND e.status = 'enrolled'
                 ORDER BY e.enrollment_date DESC
                 LIMIT 5";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId);
        $stmt->execute();
        
        $deadlines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dates and add status messages
        foreach ($deadlines as &$deadline) {
            $deadline['due_date_formatted'] = date('M j, Y', strtotime($deadline['due_date']));
            $deadline['status_message'] = $deadline['status'] === 'completed' ? 'Completed' : 'In Progress';
        }
        
        return $deadlines;
    }
}