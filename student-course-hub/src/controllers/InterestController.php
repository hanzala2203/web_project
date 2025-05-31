<?php
require_once __DIR__ . '/../../src/config/database.php';

class InterestController {
    private $db;
    
    public function __construct() {
        global $host, $database, $username, $password;
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function registerInterest($studentId, $programmeId) {
        try {
            $stmt = $this->db->prepare("INSERT INTO student_interests (student_id, programme_id) VALUES (?, ?)");
            $stmt->execute([$studentId, $programmeId]);
            return true;
        } catch(PDOException $e) {
            if(strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return "Already registered";
            }
            return false;
        }
    }
    
    public function withdrawInterest($studentId, $programmeId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM student_interests WHERE student_id = ? AND programme_id = ?");
            $stmt->execute([$studentId, $programmeId]);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function getStudentInterests($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, si.created_at as interest_date 
                FROM student_interests si
                JOIN programmes p ON si.programme_id = p.id
                WHERE si.student_id = ?
                ORDER BY si.created_at DESC
            ");
            $stmt->execute([$studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}
?>
