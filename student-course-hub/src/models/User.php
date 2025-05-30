<?php

namespace App\Models;

use PDO;

class User extends Model {
    private $table = 'users';

    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (username, email, password, role) 
                 VALUES (:username, :email, :password, :role)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }

    public function emailExists($email) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }

    public function storeResetToken($userId, $token, $expiry) {
        $query = "UPDATE {$this->table} SET reset_token = :token, reset_expiry = :expiry 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->bindParam(':id', $userId);
        
        return $stmt->execute();
    }
}