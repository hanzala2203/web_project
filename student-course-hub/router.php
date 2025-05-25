<?php
// Static file handler for PHP's built-in server
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Check if the file exists in the document root
    if (is_file(__DIR__ . $url)) {
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        
        // Set content type based on file extension
        $content_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif'
        ];
        
        if (isset($content_types[$extension])) {
            header('Content-Type: ' . $content_types[$extension]);
            readfile(__DIR__ . $url);
            return true;
        }
    }
    
    // Let PHP handle the rest
    return false;
}

// Include the main application
require_once __DIR__ . '/index.php';
