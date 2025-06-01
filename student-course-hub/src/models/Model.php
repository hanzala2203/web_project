<?php

namespace App\Models;

class Model {    protected $db;    
    
    public function __construct() {
        error_log("Model constructor called for " . get_class($this));
        if (!isset($this->db)) {
            require_once __DIR__ . '/../config/database.php';
            global $conn;
            
            if (!$conn) {
                error_log("Database connection not initialized in Model constructor");
                throw new \Exception("Database connection not initialized");
            }
            
            try {
                $testQuery = $conn->query("SELECT 1");
                if ($testQuery === false) {
                    error_log("Database test query failed in Model constructor");
                    throw new \PDOException("Test query failed");
                }
                error_log("Database connection test successful");
                $this->db = $conn;
            } catch (\PDOException $e) {
                error_log("Database connection error in Model constructor: " . $e->getMessage());
                throw new \Exception("Database connection error: " . $e->getMessage());
            }
        } else {
            error_log("Using existing database connection");
        }
    }
}