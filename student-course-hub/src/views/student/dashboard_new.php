<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<!-- Include Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Include FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

<div class="min-h-screen bg-gray-100">
    <?php require_once __DIR__ . '/../layouts/student_sidebar_new.php'; ?>
    <main class="ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Welcome Hero Section -->
            <section class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-4xl font-bold mb-4">Welcome back, <?= htmlspecialchars($student['name'] ?? 'Student') ?>! 👋</h1>
                    <p class="text-xl opacity-90 mb-8">Track your progress and stay on top of your coursework</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/student-course-hub/student/explore_programmes" 
                           class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 rounded-lg transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-compass mr-2"></i>
                            Explore New Courses
                        </a>
                        <a href="/student-course-hub/student/my_courses" 
                           class="inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 rounded-lg transition-all duration-300 backdrop-blur-sm">
                            <i class="fas fa-book mr-2"></i>
                            Continue Learning
                        </a>
                    </div>
                </div>
                <div class="absolute right-0 top-0 h-full w-1/3 opacity-10">
                    <i class="fas fa-graduation-cap text-9xl transform rotate-12 translate-x-1/4 translate-y-1/4"></i>
                </div>
            </section>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Enrolled Courses</h3>
                        <span class="bg-indigo-100 text-indigo-800 p-2 rounded-lg">
                            <i class="fas fa-book-open"></i>
                        </span>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2"><?= $enrolledCoursesCount ?></div>
                    <p class="flex items-center text-green-600 text-sm">
                        <i class="fas fa-arrow-up mr-1"></i>
                        Active Learner
                    </p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Completed Courses</h3>
                        <span class="bg-yellow-100 text-yellow-800 p-2 rounded-lg">
                            <i class="fas fa-trophy"></i>
                        </span>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2"><?= $completedCoursesCount ?></div>
                    <p class="flex items-center text-yellow-600 text-sm">
                        <i class="fas fa-star mr-1"></i>
                        Achievement Unlocked
                    </p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-600 font-medium">Overall Progress</h3>
                        <span class="bg-green-100 text-green-800 p-2 rounded-lg">
                            <i class="fas fa-chart-line"></i>
                        </span>
                    </div>
                    <div class="text-4xl font-bold text-gray-800 mb-2"><?= round($overallProgress) ?>%</div>
                    <p class="flex items-center text-green-600 text-sm">
                        <i class="fas fa-check-circle mr-1"></i>
                        Keep it up!
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <!-- Course Progress -->
                    <section class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-800">Current Courses</h2>
                            <a href="/student-course-hub/student/my_courses" 
                               class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200 flex items-center">
                                View All
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        <?php if (!empty($enrolledCourses)): ?>
                            <div class="space-y-4">
                                <?php foreach(array_slice($enrolledCourses, 0, 3) as $course): ?>
                                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                            <?= htmlspecialchars($course['title']) ?>
                                        </h3>
                                        <div class="relative h-2 bg-gray-200 rounded-full mb-3">
                                            <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full transition-all duration-500"
                                                 style="width: <?= $course['progress'] ?>%"></div>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600">
                                                <i class="fas fa-chart-pie mr-1"></i>
                                                <?= $course['progress'] ?>% Complete
                                            </span>
                                            <a href="/student-course-hub/student/course/<?= $course['id'] ?>"
                                               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                                                Continue
                                                <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12 bg-gray-50 rounded-lg">
                                <i class="fas fa-book-open text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-4">You haven't enrolled in any courses yet.</p>
                                <a href="/student-course-hub/student/explore_programmes" 
                                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                                    <i class="fas fa-search mr-2"></i>
                                    Find Courses
                                </a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <!-- Recent Activity -->
                    <section class="bg-white rounded-xl shadow-md p-6 mt-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Recent Activity</h2>
                        <div class="space-y-6">
                            <div class="relative pl-8 pb-6 border-l-2 border-indigo-200">
                                <div class="absolute left-0 top-0 transform -translate-x-1/2 w-4 h-4 rounded-full bg-indigo-500"></div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-1">Course Enrollment</h4>
                                <p class="text-gray-600 mb-2">You enrolled in a new course</p>
                                <small class="text-gray-500">2 days ago</small>
                            </div>
                            <div class="relative pl-8 pb-6 border-l-2 border-indigo-200">
                                <div class="absolute left-0 top-0 transform -translate-x-1/2 w-4 h-4 rounded-full bg-indigo-500"></div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-1">Assignment Submitted</h4>
                                <p class="text-gray-600 mb-2">You submitted your assignment for Module 3</p>
                                <small class="text-gray-500">5 days ago</small>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="lg:col-span-1">
                    <!-- Upcoming Deadlines -->
                    <section class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Upcoming Deadlines</h2>
                        <?php if (!empty($deadlines)): ?>
                            <div class="space-y-4">
                                <?php foreach ($deadlines as $deadline): ?>
                                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                        <div class="flex-shrink-0 bg-indigo-100 text-indigo-800 px-3 py-2 rounded-lg text-center">
                                            <div class="text-sm font-semibold"><?= date('M', strtotime($deadline['due_date'])) ?></div>
                                            <div class="text-lg font-bold"><?= date('d', strtotime($deadline['due_date'])) ?></div>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 mb-1"><?= htmlspecialchars($deadline['title']) ?></h4>
                                            <p class="text-sm text-gray-600"><?= htmlspecialchars($deadline['course_name']) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="far fa-calendar-check text-3xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600">No upcoming deadlines</p>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
            
            <!-- Add some bottom padding -->
            <div class="pb-8"></div>
        </div>
    </main>
</div>

<script>
    // Add animation to progress bars
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('[class*="from-indigo-500"]');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
    });
</script>
