<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/StudentController.php';

// Initialize controllers
$auth = new AuthController();
$studentController = new StudentController();

// Check student authentication
$auth->requireRole('student');

// Get student data
$userId = $_SESSION['user_id'];
$dashboardData = $studentController->viewDashboard($userId);

// Extract data from dashboard data with defaults
$student = $dashboardData['student'];
$enrolledCourses = $dashboardData['enrolled_courses'] ?? [];
$deadlines = $dashboardData['deadlines'] ?? [];
$interestedCourses = $dashboardData['interested_courses'] ?? [];
$recommendedCourses = $dashboardData['recommended_courses'] ?? [];

// Calculate statistics
$enrolledCoursesCount = count($enrolledCourses);
$completedCoursesCount = array_reduce($enrolledCourses, function($count, $course) {
    return $count + ($course['progress'] === 100 ? 1 : 0);
}, 0);
$overallProgress = $enrolledCoursesCount > 0 ? 
    array_reduce($enrolledCourses, function($sum, $course) {
        return $sum + ($course['progress'] ?? 0);
    }, 0) / $enrolledCoursesCount : 
    0;

require_once __DIR__ . '/../layouts/header.php';
?>
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
        <div class="dashboard-section">
            <h2>Current Courses</h2>
            <div class="courses-list">
                <?php if (!empty($enrolledCourses)): ?>
                    <?php foreach($enrolledCourses as $course): ?>
                        <div class="course-item">
                            <div class="course-info">
                                <h3><?= htmlspecialchars($course['title']) ?></h3>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?= $course['progress'] ?? 0 ?>%"></div>
                                </div>
                                <span class="progress-text"><?= $course['progress'] ?? 0 ?>% Complete</span>
                            </div>
                            <a href="/student-course-hub/course/view/<?= $course['id'] ?>" class="btn-continue">Continue</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>You haven't enrolled in any courses yet.</p>
                        <a href="/student-course-hub/student/courses" class="btn-primary">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Upcoming Deadlines</h2>
            <div class="deadlines-list">
                <?php if (!empty($deadlines)): ?>
                    <?php foreach($deadlines as $deadline): ?>
                        <div class="deadline-item">
                            <span class="deadline-date"><?= date('M d', strtotime($deadline['due_date'])) ?></span>
                            <div class="deadline-info">
                                <h4><?= htmlspecialchars($deadline['title']) ?></h4>
                                <p><?= htmlspecialchars($deadline['course_name']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No upcoming deadlines.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Recommended Courses</h2>
            <div class="courses-grid">
                <?php if (!empty($recommendedCourses)): ?>
                    <?php foreach($recommendedCourses as $course): ?>
                        <div class="course-card">
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <p><?= htmlspecialchars($course['description']) ?></p>
                            <a href="/student-course-hub/course/view/<?= $course['id'] ?>" class="btn-view">View Course</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No course recommendations available yet.</p>
                        <a href="/student-course-hub/student/courses" class="btn-primary">Browse All Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>