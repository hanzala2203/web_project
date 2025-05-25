<?php
class Programme {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function findAll($conditions = [], $params = []) {
        $sql = "SELECT * FROM programmes WHERE " . implode(' AND ', $conditions);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Add other necessary methods...
}
