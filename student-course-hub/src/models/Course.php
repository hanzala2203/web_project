<?php

class Course {
    private $id;
    private $title;
    private $description;
    private $credits;

    public function __construct($id, $title, $description, $credits) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->credits = $credits;
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