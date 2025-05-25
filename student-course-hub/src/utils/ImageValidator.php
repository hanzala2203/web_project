<?php
class ImageValidator {
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxSize = 5242880; // 5MB

    public function validate($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }

        // Check file type
        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type. Only JPG, PNG and GIF are allowed'];
        }

        // Check file size
        if ($file['size'] > $this->maxSize) {
            return ['valid' => false, 'error' => 'File is too large. Maximum size is 5MB'];
        }

        // Verify it's actually an image
        if (!getimagesize($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'Invalid image file'];
        }

        return ['valid' => true];
    }

    public function processUpload($file, $targetDir, $fileName = null) {
        $validation = $this->validate($file);
        if (!$validation['valid']) {
            return $validation;
        }

        // Generate unique filename if none provided
        if (!$fileName) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
        }

        $targetPath = $targetDir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['valid' => true, 'path' => $targetPath];
        }

        return ['valid' => false, 'error' => 'Failed to move uploaded file'];
    }
}
