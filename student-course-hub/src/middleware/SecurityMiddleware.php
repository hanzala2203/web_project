<?php
class SecurityMiddleware {
    public static function validateRequest() {
        // Set security headers
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Content-Security-Policy: default-src 'self'");
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // Validate Content-Type for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : '';
            if (strpos($contentType, 'application/x-www-form-urlencoded') !== 0 
                && strpos($contentType, 'multipart/form-data') !== 0) {
                throw new Exception("Invalid Content-Type");
            }
        }
    }
}
