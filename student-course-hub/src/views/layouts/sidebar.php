\
<?php
// src/views/layouts/sidebar.php
require_once __DIR__ . '/../../utils/sidebar_helpers.php';

// Determine active page for highlighting
$currentUri = $_SERVER['REQUEST_URI'];
?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="/student-course-hub/admin/dashboard" class="logo">AdminHub</a>
    </div>
    <nav>
        <ul>
            <li class="<?php echo isActive($currentUri, '/admin/dashboard'); ?>">
                <a href="/student-course-hub/admin/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/admin/programmes'); ?>">
                <a href="/student-course-hub/admin/programmes"><i class="fas fa-graduation-cap"></i> Programmes</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/admin/modules'); ?>">
                <a href="/student-course-hub/admin/modules"><i class="fas fa-book"></i> Modules</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/admin/students'); ?>">
                <a href="/student-course-hub/admin/students"><i class="fas fa-users"></i> Students</a>
            </li>            <li class="<?php echo isActive($currentUri, '/admin/staff'); ?>">
                <a href="/student-course-hub/admin/staff"><i class="fas fa-user-tie"></i> Staff</a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="/student-course-hub/auth/logout" class="btn btn-sm btn-logout-sidebar"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>
