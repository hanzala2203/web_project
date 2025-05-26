<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/StudentController.php';

use App\Controllers\AuthController;
use App\Controllers\StudentController;

// Initialize controllers
$auth = new AuthController();
$studentController = new StudentController();

// Check student authentication
$auth->requireRole('student');

// Get student data
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: /student-course-hub/auth/login');
    exit;
}

try {
    $dashboardData = $studentController->viewDashboard($userId);

    // Extract data from dashboard data with defaults
    $student = $dashboardData['student'] ?? null;
    $enrolledCourses = $dashboardData['enrolled_courses'] ?? [];
    $deadlines = $dashboardData['deadlines'] ?? [];
    $interestedCourses = $dashboardData['interested_courses'] ?? [];
    $recommendedCourses = $dashboardData['recommended_courses'] ?? [];
    $currentProgramme = $dashboardData['current_programme'] ?? null;
    $modulePrerequisites = $dashboardData['module_prerequisites'] ?? [];
    $notifications = $dashboardData['notifications'] ?? [];
    $interestAnalytics = $dashboardData['interest_analytics'] ?? [];
} catch (Exception $e) {
    // Log the error
    error_log("Error loading dashboard: " . $e->getMessage());
    // Set empty defaults
    $student = null;
    $enrolledCourses = [];
    $deadlines = [];
    $interestedCourses = [];
    $recommendedCourses = [];
    $currentProgramme = null;
    $modulePrerequisites = [];
    $notifications = [];
    $interestAnalytics = [];
}

// Calculate statistics with error handling
try {
    $enrolledCoursesCount = count($enrolledCourses);
    $completedCoursesCount = array_reduce($enrolledCourses, function($count, $course) {
        return $count + ($course['progress'] === 100 ? 1 : 0);
    }, 0);
    $overallProgress = $enrolledCoursesCount > 0 ? 
        array_reduce($enrolledCourses, function($sum, $course) {
            return $sum + ($course['progress'] ?? 0);
        }, 0) / $enrolledCoursesCount : 
        0;
} catch (Exception $e) {
    error_log("Error calculating statistics: " . $e->getMessage());
    $enrolledCoursesCount = 0;
    $completedCoursesCount = 0;
    $overallProgress = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Course Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Reset and base styles */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        :root {
            --primary-color: #3b82f6;
            --secondary-color: #4CAF50;
            --error-color: #ef4444;
            --link-color: #2563eb;
            --link-hover-color: #1d4ed8;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Accessibility improvements */
        *:focus {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Dashboard Container */
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Welcome Section */
        .welcome-section {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .welcome-section h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Dashboard Stats */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card h3 {
            color: #1e293b;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        /* Dashboard Sections */
        .dashboard-section {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .dashboard-section h2 {
            color: #1e293b;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
        }

        /* Course List */
        .courses-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .course-item:hover {
            background: #f1f5f9;
            transform: translateX(4px);
        }

        .course-info {
            flex: 1;
        }

        .course-info h3 {
            color: #1e293b;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .course-info p {
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Progress Bar */
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }

        .progress {
            height: 100%;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Deadlines List */
        .deadlines-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .deadline-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }

        .deadline-date {
            min-width: 80px;
            color: var(--primary-color);
            font-weight: 500;
        }

        .deadline-info h4 {
            color: #1e293b;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .deadline-info p {
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--link-hover-color);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
            background: #f8fafc;
            border-radius: 0.5rem;
            border: 2px dashed #e2e8f0;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #94a3b8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-stats {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .stat-card {
                width: 100%;
            }

            .welcome-section {
                text-align: left;
            }

            .course-item {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                padding: 1rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .welcome-section h1 {
                font-size: 1.5rem;
            }
        }

        /* High Contrast Support */
        @media (prefers-contrast: high) {
            :root {
                --primary-color: #000;
                --secondary-color: #000;
                --link-color: #000;
                --link-hover-color: #333;
            }

            .course-item,
            .deadline-item {
                border: 2px solid #000;
            }

            .progress-bar {
                border: 2px solid #000;
            }

            .btn {
                border: 2px solid currentColor;
            }
        }

        /* Course Info Styles */
        .course-info p {
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Programme Structure Section */
        .programme-structure {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .semester-view {
            display: grid;
            gap: 1.5rem;
        }

        .semester-block {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
        }

        .semester-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        /* Module Prerequisites */
        .prerequisites-list {
            list-style: none;
            padding: 0;
        }

        .prerequisite-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        /* Staff Profiles */
        .staff-profiles {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .staff-card {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .staff-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            overflow: hidden;
            background: #e2e8f0;
        }

        /* Interest Analytics */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .analytics-card {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        /* Notifications */
        .notifications-list {
            list-style: none;
            padding: 0;
        }

        .notification-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .notification-icon {
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .notification-content {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>Welcome, <?= htmlspecialchars($student['username']) ?></h1>
            <p>Your Email: <?= htmlspecialchars($student['email']) ?></p>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Enrolled Courses</h3>
                <p class="stat-number"><?= $enrolledCoursesCount ?></p>
            </div>
            <div class="stat-card">
                <h3>Completed Courses</h3>
                <p class="stat-number"><?= $completedCoursesCount ?></p>
            </div>
            <div class="stat-card">
                <h3>Overall Progress</h3>
                <p class="stat-number"><?= round($overallProgress) ?>%</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <!-- Programme Structure -->
                <?php if ($currentProgramme): ?>
                <section class="programme-structure">
                    <h2>Programme Structure</h2>
                    <div class="semester-view">
                        <?php foreach ($currentProgramme['years'] as $year => $semesters): ?>
                            <?php foreach ($semesters as $semester => $modules): ?>
                                <div class="semester-block">
                                    <div class="semester-header">
                                        <h3>Year <?php echo htmlspecialchars($year); ?> - Semester <?php echo htmlspecialchars($semester); ?></h3>
                                    </div>
                                    <div class="courses-list">
                                        <?php foreach ($modules as $module): ?>
                                            <div class="course-item">
                                                <div class="course-info">
                                                    <h3><?php echo htmlspecialchars($module['title']); ?></h3>
                                                    <p>
                                                        <i class="fas fa-graduation-cap"></i>
                                                        <?php echo htmlspecialchars($module['credits']); ?> Credits
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Module Prerequisites -->
                <?php if (!empty($modulePrerequisites)): ?>
                <section class="dashboard-section">
                    <h2>Module Prerequisites</h2>
                    <ul class="prerequisites-list">
                        <?php foreach ($modulePrerequisites as $module): ?>
                            <li class="prerequisite-item">
                                <i class="fas fa-code-branch"></i>
                                <div>
                                    <h4><?php echo htmlspecialchars($module['title']); ?></h4>
                                    <?php if (!empty($module['prerequisites'])): ?>
                                        <p>Required: <?php echo htmlspecialchars(implode(', ', array_column($module['prerequisites'], 'title'))); ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>

                <!-- Interest Analytics -->
                <?php if (!empty($interestAnalytics)): ?>
                <section class="dashboard-section">
                    <h2>Interest Analytics</h2>
                    <div class="analytics-grid">
                        <?php foreach ($interestAnalytics as $stat): ?>
                            <div class="analytics-card">
                                <h3><?php echo htmlspecialchars($stat['label']); ?></h3>
                                <div class="stat-number"><?php echo htmlspecialchars($stat['value']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <div class="sidebar-content">
                <!-- Teaching Staff -->
                <?php if (!empty($currentProgramme['staff'])): ?>
                <section class="dashboard-section">
                    <h2>Teaching Staff</h2>
                    <div class="staff-profiles">
                        <?php foreach ($currentProgramme['staff'] as $staff): ?>
                            <div class="staff-card">
                                <div class="staff-avatar">
                                    <?php if (!empty($staff['avatar_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($staff['avatar_url']); ?>" alt="<?php echo htmlspecialchars($staff['name']); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <h3><?php echo htmlspecialchars($staff['name']); ?></h3>
                                <p><?php echo htmlspecialchars($staff['role']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Notifications -->
                <?php if (!empty($notifications)): ?>
                <section class="dashboard-section">
                    <h2>Notifications</h2>
                    <ul class="notifications-list">
                        <?php foreach ($notifications as $notification): ?>
                            <li class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-<?php echo htmlspecialchars($notification['icon'] ?? 'bell'); ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small><?php echo htmlspecialchars($notification['time']); ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>
</html>