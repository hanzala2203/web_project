<?php
// src/views/layouts/student_sidebar.php
require_once __DIR__ . '/../../utils/sidebar_helpers.php';

// Determine active page for highlighting
$currentUri = $_SERVER['REQUEST_URI'];
?>
<aside class="student-sidebar">
    <div class="sidebar-header">
        <a href="/student-course-hub/student/dashboard" class="logo">Student Hub</a>
    </div>
    <nav>
        <ul>
            <li class="<?php echo isActive($currentUri, '/student/dashboard'); ?>">
                <a href="/student-course-hub/student/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/student/explore_programmes'); ?>">
                <a href="/student-course-hub/student/explore_programmes"><i class="fas fa-search"></i> Explore Programmes</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/student/my_courses'); ?>">
                <a href="/student-course-hub/student/my_courses"><i class="fas fa-book"></i> My Courses</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/student/interests'); ?>">
                <a href="/student-course-hub/student/manage_interests"><i class="fas fa-star"></i> My Interests</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/student/profile'); ?>">
                <a href="/student-course-hub/student/profile"><i class="fas fa-user"></i> Profile</a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="/student-course-hub/auth/logout" class="btn btn-sm btn-logout-sidebar"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>
