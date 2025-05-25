<?php

class Student {
    private $db;
    private $table = 'users';

    public function __construct($db) {
        $this->db = $db;
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
        $query = "UPDATE {$this->table} 
                 SET preferences = :preferences 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $studentId);
        $stmt->bindParam(':preferences', json_encode($preferences));
        
        return $stmt->execute();
    }

    public function getEnrolledCourses($studentId) {
        try {
            $query = "SELECT p.*, se.enrollment_date, se.last_accessed 
                     FROM programmes p 
                     INNER JOIN student_enrollments se ON p.id = se.programme_id 
                     WHERE se.student_id = :student_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting enrolled courses: " . $e->getMessage());
            return [];
        }
    }

    public function getUpcomingDeadlines($studentId) {
        try {
            $query = "SELECT a.title, a.due_date, p.title as course_name 
                     FROM assignments a 
                     INNER JOIN modules m ON a.module_id = m.id 
                     INNER JOIN programmes p ON m.id = p.id 
                     INNER JOIN student_enrollments se ON p.id = se.programme_id 
                     WHERE se.student_id = :student_id 
                     AND a.due_date >= CURRENT_DATE 
                     ORDER BY a.due_date ASC 
                     LIMIT 5";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting deadlines: " . $e->getMessage());
            return [];
        }
    }

    public function create($data) {
        try {
            // The user is already created in the users table by AuthController
            // We just need to update their role to 'student'
            $query = "UPDATE {$this->table} 
                     SET role = 'student'
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $data['user_id']);
            
            $success = $stmt->execute();
            
            if (!$success) {
                throw new Exception("Failed to update user role");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error creating student record: " . $e->getMessage());
            throw new Exception("Failed to create student record");
        }
    }
}