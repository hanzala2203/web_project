<?php
// Get the request URI
$request = $_SERVER['REQUEST_URI'];
$base = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$path = str_replace($base, '', $request);

// Basic routing
switch ($path) {
    case '/':
    case '':
        require BASE_PATH . '/views/home.php';
        break;
    default:
        http_response_code(404);
        require BASE_PATH . '/views/404.php';
        break;
}