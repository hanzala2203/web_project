<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="student-sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <i class="fas fa-graduation-cap"></i>
            <h1>Student Course Hub</h1>
        </div>
    </div>
    <div class="sidebar-nav">
        <ul>
            <li <?php echo ($current_page == 'dashboard.php') ? 'class="active"' : ''; ?>>
                <a href="<?php echo BASE_URL; ?>/student/dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li <?php echo ($current_page == 'programmes_new.php') ? 'class="active"' : ''; ?>>
                <a href="<?php echo BASE_URL; ?>/student/explore_programmes">
                    <i class="fas fa-search"></i> Explore Programmes
                </a>
            </li>
            <li <?php echo ($current_page == 'manage_interests.php') ? 'class="active"' : ''; ?>>
                <a href="<?php echo BASE_URL; ?>/student/manage_interests">
                    <i class="fas fa-star"></i> My Interests
                </a>
            </li>
        </ul>
    </div>
</nav>
