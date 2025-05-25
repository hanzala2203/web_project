<?php
class Module {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllModules() {
        $query = "SELECT * FROM modules";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModuleById($id) {
        $query = "SELECT * FROM modules WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createModule($name, $code, $description, $credits) {
        $query = "INSERT INTO modules (name, code, description, credits) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$name, $code, $description, $credits]);
    }

    public function updateModule($id, $name, $code, $description, $credits) {
        $query = "UPDATE modules SET name = ?, code = ?, description = ?, credits = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$name, $code, $description, $credits, $id]);
    }

    public function deleteModule($id) {
        $query = "DELETE FROM modules WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
}
