<?php

namespace App\Models;

class Model {
    protected $db;    public function __construct() {
        if (!isset($this->db)) {
            require_once __DIR__ . '/../config/database.php';
            global $conn;
            
            if (!$conn) {
                throw new \Exception("Database connection not initialized");
            }
            
            try {
                $conn->query("SELECT 1");
                $this->db = $conn;
            } catch (\PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                throw new \Exception("Database connection error");
            }
        }
    }
}