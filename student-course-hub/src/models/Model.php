<?php

namespace App\Models;

class Model {
    protected $db;    public function __construct() {
        if (!isset($this->db)) {
            require_once __DIR__ . '/../config/database.php';
            global $conn;
            $this->db = $conn;
            
            if (!$this->db) {
                throw new \Exception("Database connection failed");
            }
        }
    }
}