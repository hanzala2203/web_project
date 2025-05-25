<?php
// dashboard.php

require_once __DIR__ . '/../../controllers/AdminController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Initialize controllers
$auth = new AuthController();
$admin = new AdminController();

// Check admin authentication
$auth->requireRole('admin');

// Get dashboard statistics
$stats = $admin->getDashboardStats();

// Include header
include_once __DIR__ . '/../layouts/header.php';
?>

<div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <nav>
            <ul>
                <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="courses.php"><i class="fas fa-graduation-cap"></i> Programmes</a></li>
                <li><a href="students.php"><i class="fas fa-users"></i> Students</a></li>
                <li><a href="modules.php"><i class="fas fa-book"></i> Modules</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="/auth/logout.php" class="btn btn-sm btn-logout">Logout</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Programmes</h3>
                    <p class="stat-number"><?php echo $stats['total_programmes']; ?></p>
                    <span class="stat-label">Active: <?php echo $stats['active_programmes']; ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Students</h3>
                    <p class="stat-number"><?php echo $stats['total_students']; ?></p>
                    <span class="stat-label">New this month: <?php echo $stats['new_students']; ?></span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-details">
                    <h3>Pending Interests</h3>
                    <p class="stat-number"><?php echo $stats['pending_interests']; ?></p>
                    <span class="stat-label">Last 7 days</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-details">
                    <h3>Total Modules</h3>
                    <p class="stat-number"><?php echo $stats['total_modules']; ?></p>
                    <span class="stat-label">Active: <?php echo $stats['active_modules']; ?></span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="dashboard-section">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php foreach ($stats['recent_activities'] as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-<?php echo $activity['icon']; ?>"></i>
                        </div>
                        <div class="activity-details">
                            <p><?php echo htmlspecialchars($activity['description']); ?></p>
                            <span class="activity-time"><?php echo $activity['time']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-section">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="courses.php?action=new" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New Programme</span>
                </a>
                <a href="modules.php?action=new" class="action-card">
                    <i class="fas fa-book-medical"></i>
                    <span>Create Module</span>
                </a>
                <a href="students.php?action=export" class="action-card">
                    <i class="fas fa-file-export"></i>
                    <span>Export Student Data</span>
                </a>
                <a href="reports.php" class="action-card">
                    <i class="fas fa-chart-bar"></i>
                    <span>View Reports</span>
                </a>
            </div>
        </div>
    </main>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>