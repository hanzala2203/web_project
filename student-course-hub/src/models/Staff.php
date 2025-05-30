<?php

namespace App\Models;

use PDO;

class Staff extends User { // Inherit from User model for base user properties
    private $userTable = 'users'; // Explicitly define for clarity, though parent might handle it

    // Override constructor if necessary, or rely on parent Model constructor
    // public function __construct($db) {
    //     parent::__construct($db);
    // }

    /**
     * Get all users with the role 'staff'.
     * @return array List of staff members.
     */
    public function getAllStaff() {
        $query = "SELECT id, username, email, created_at FROM {$this->userTable} WHERE role = :role ORDER BY username ASC";
        $stmt = $this->db->prepare($query);
        $role = 'staff';
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a specific staff member by ID.
     * @param int $id User ID.
     * @return array|false Staff member data or false if not found or not staff.
     */
    public function getStaffById($id) {
        $query = "SELECT id, username, email, role, created_at FROM {$this->userTable} WHERE id = :id AND role = :role";
        $stmt = $this->db->prepare($query);
        $role = 'staff';
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new staff member (a user with role 'staff').
     * @param array $data Contains username, email, password.
     * @return bool True on success, false on failure.
     */
    public function createStaff($data) {
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            // Basic validation, controller should handle more robustly
            return false; 
        }

        $query = "INSERT INTO {$this->userTable} (username, email, password, role) 
                  VALUES (:username, :email, :password, :role)";
        
        $stmt = $this->db->prepare($query);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = 'staff';

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }

    /**
     * Update an existing staff member's details.
     * @param int $id User ID of the staff member.
     * @param array $data Contains username, email. Password update is separate.
     * @return bool True on success, false on failure.
     */
    public function updateStaff($id, $data) {
        // Ensure we are only updating staff
        $staff = $this->getStaffById($id);
        if (!$staff) {
            return false; // Not a staff member or doesn't exist
        }

        $query = "UPDATE {$this->userTable} SET username = :username, email = :email 
                  WHERE id = :id AND role = 'staff'"; // Extra check for role
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Update a staff member's password.
     * @param int $id User ID.
     * @param string $newPassword The new plain text password.
     * @return bool True on success, false on failure.
     */
    public function updateStaffPassword($id, $newPassword) {
        $staff = $this->getStaffById($id);
        if (!$staff) {
            return false; 
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE {$this->userTable} SET password = :password WHERE id = :id AND role = 'staff'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    /**
     * Delete a staff member.
     * Also unassigns them from any modules they are leading.
     * @param int $id User ID of the staff member.
     * @return bool True on success, false on failure.
     */
    public function deleteStaff($id) {
        // Before deleting the staff (user), unassign them from modules
        $this->unassignStaffFromModules($id);

        $query = "DELETE FROM {$this->userTable} WHERE id = :id AND role = :role";
        $stmt = $this->db->prepare($query);
        $role = 'staff';
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }

    /**
     * Unassigns a staff member from all modules they lead.
     * Sets staff_id to NULL for those modules.
     * @param int $staffId The ID of the staff member.
     */
    private function unassignStaffFromModules($staffId) {
        $query = "UPDATE modules SET staff_id = NULL WHERE staff_id = :staff_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    // The exists method from the original Staff model is no longer needed
    // as getStaffById serves a similar purpose for validation.
    // If a generic user existence check is needed, it should be in the User model.
}
