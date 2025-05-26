<?php

namespace App\Models;

class Course extends Model {
    private $table = 'courses';

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (title, description, credits) VALUES (:title, :description, :credits)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':credits' => $data['credits']
        ]);
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCredits() {
        return $this->credits;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCredits($credits) {
        $this->credits = $credits;
    }

    public function save() {
        // Logic to save course to the database
    }

    public function delete() {
        // Logic to delete course from the database
    }

    public static function find($id) {
        // Logic to find a course by its ID
    }

    public static function all() {
        // Logic to retrieve all courses
    }
}