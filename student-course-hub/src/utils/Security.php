<?php
// Minimal Security utility for input sanitization
class Security {
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map('htmlspecialchars', $data);
        }
        return htmlspecialchars($data);
    }
}
