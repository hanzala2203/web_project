<?php
// Minimal Staff model for admin dashboard
class Staff {
    private $db;
    private $table = 'staff';

    public function __construct($db) {
        $this->db = $db;
    }

    public function exists($id) {
        // Dummy implementation for now
        return true;
    }
}
