<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Course Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <?php
    // Determine page type for CSS loading
    // Ensure BASE_URL is defined (usually in your main index.php or bootstrap file)
    $raw_base_url = defined('BASE_URL') ? BASE_URL : '';

    // Normalize BASE_URL:
    // If it's a path segment (e.g., "student-course-hub"), ensure it starts with "/"
    // If it's a full URL or already starts with "/", or is empty, leave as is (for now)
    if (!empty($raw_base_url) && strpos($raw_base_url, 'http') !== 0 && $raw_base_url[0] !== '/') {
        $base_url_for_css = '/' . ltrim($raw_base_url, '/');
    } else {
        $base_url_for_css = $raw_base_url;
    }
    // Ensure no trailing slash for consistent concatenation
    $base_url_for_css = rtrim($base_url_for_css, '/');

    $currentRequestPathForCss = $_SERVER['REQUEST_URI'] ?? '';

    // Check if the current path starts with BASE_URL followed by /admin/ or /auth/
    $isCurrentPageAdmin = !empty($base_url_for_css) ? 
                          (strpos($currentRequestPathForCss, $base_url_for_css . '/admin/') === 0) :
                          (strpos($currentRequestPathForCss, '/admin/') === 0);
    $isCurrentPageAuth = !empty($base_url_for_css) ?
                         (strpos($currentRequestPathForCss, $base_url_for_css . '/auth/') === 0) :
                         (strpos($currentRequestPathForCss, '/auth/') === 0);
    
    // If BASE_URL is empty (site at root), the strpos checks above might need adjustment
    // For instance, if BASE_URL is empty, '/admin/' should be checked against '/admin/'.
    // The logic above handles this: if $base_url_for_css is empty, it checks for '/admin/' directly.
    
    $cacheBuster = '?v=' . time(); // Cache buster

    // Conditional CSS linking based on page type
    if ($isCurrentPageAdmin) {
        echo '<link rel="stylesheet" href="' . $base_url_for_css . '/public/assets/css/admin-dashboard.css' . $cacheBuster . '">';
        // You might also want a general admin stylesheet if admin-dashboard.css is very specific
        // echo '<link rel="stylesheet" href="' . $base_url_for_css . '/public/assets/css/admin.css' . $cacheBuster . '">';
    } else { // Not an admin page
        echo '<link rel="stylesheet" href="' . $base_url_for_css . '/public/assets/css/style.css' . $cacheBuster . '">';
        if ($isCurrentPageAuth) { // Specifically an auth page (which is also not admin)
            echo '<link rel="stylesheet" href="' . $base_url_for_css . '/public/assets/css/auth.css' . $cacheBuster . '">';
        }
    }
    ?>
</head>
<body>
    <?php
    if ($isCurrentPageAdmin) {
        echo '<div class="admin-container">'; // Open container for admin layout
        // Automatically include sidebar for admin pages
        // header.php is in src/views/layouts/
        // sidebar.php is in src/views/layouts/
        require_once __DIR__ . '/sidebar.php';
        // Admin views will then just need to open <div class="admin-main">
    }

    // This logic is for controlling the display of the main non-admin header
    // It uses $isCurrentPageAdmin which was defined above for CSS loading.
    if (!$isCurrentPageAdmin):
    ?>
    <header>
        <h1>Welcome to the Student Course Hub</h1>
        <nav>
            <ul>
                <li><a href="<?php echo $base_url_for_css; ?>/">Home</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo $base_url_for_css; ?>/auth/login">Login</a></li>
                    <li><a href="<?php echo $base_url_for_css; ?>/auth/register">Register</a></li>
                <?php else: ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="<?php echo $base_url_for_css; ?>/admin/dashboard">Admin Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base_url_for_css; ?>/student/dashboard">Student Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base_url_for_css; ?>/auth/logout">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <?php endif; // End of !$isCurrentPageAdmin check ?>