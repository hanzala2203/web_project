<?php
function isActive($currentUri, $path) {
    return strpos($currentUri, $path) !== false ? 'active' : '';
}
$currentUri = $_SERVER['REQUEST_URI'];
?>

<style>
    /* Staff Sidebar */
    .staff-sidebar {
        width: 260px;
        background: #1e293b;
        color: #e2e8f0;
        position: fixed;
        height: 100vh;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 40;
    }

    .sidebar-header {
        padding: 1.5rem;
        text-align: center;
        border-bottom: 1px solid #334155;
    }

    .sidebar-header .logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff;
        text-decoration: none;
        display: block;
    }

    .staff-sidebar nav {
        flex: 1;
        padding: 1rem 0;
    }

    .staff-sidebar nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .staff-sidebar nav ul li {
        margin-bottom: 0.25rem;
    }

    .staff-sidebar nav ul li a {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        color: #cbd5e1;
        text-decoration: none;
        transition: all 0.2s;
    }

    .staff-sidebar nav ul li.active a {
        background-color: #334155;
        color: #fff;
        border-left: 3px solid #3b82f6;
    }

    .staff-sidebar nav ul li a:hover {
        background-color: #334155;
        color: #fff;
    }

    .staff-sidebar nav ul li a i {
        width: 1.25rem;
        margin-right: 0.75rem;
        font-size: 1.1rem;
    }

    .sidebar-footer {
        padding: 1rem;
        border-top: 1px solid #334155;
    }

    .staff-btn-danger {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        background-color: #ef4444;
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .staff-btn-danger:hover {
        background-color: #dc2626;
    }

    .staff-btn-danger i {
        margin-right: 0.5rem;
    }
</style>

<aside class="staff-sidebar">
    <div class="sidebar-header">
        <a href="/student-course-hub/staff/dashboard" class="logo">Staff Portal</a>
    </div>
    <nav>
        <ul>            <li class="<?php echo isActive($currentUri, '/staff/dashboard'); ?>">
                <a href="/student-course-hub/staff/dashboard"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/staff/modules'); ?>">
                <a href="/student-course-hub/staff/modules"><i class="fas fa-book"></i> My Modules</a>
            </li>
            <li class="<?php echo isActive($currentUri, '/staff/programmes'); ?>">
                <a href="/student-course-hub/staff/programmes"><i class="fas fa-graduation-cap"></i> Programmes</a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="/student-course-hub/auth/logout" class="staff-btn staff-btn-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>