<?php
require_once __DIR__ . '/../../utils/sidebar_helpers.php';
$currentUri = $_SERVER['REQUEST_URI'];
?>

<!-- Student Sidebar -->
<aside class="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white shadow-lg z-30">
    <!-- Logo -->
    <div class="px-6 py-6 border-b border-gray-800">
        <a href="/student-course-hub/student/dashboard" class="flex items-center group">
            <i class="fas fa-graduation-cap text-2xl text-indigo-500 group-hover:text-indigo-400 transition-colors duration-200 mr-3"></i>
            <span class="text-lg font-semibold text-white">Student Hub</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6">
        <!-- Main Navigation -->
        <div class="mb-8">
            <h2 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Main</h2>
            <ul class="space-y-1">
                <li>
                    <a href="/student-course-hub/student/dashboard" 
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?php echo isActive($currentUri, '/student/dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                        <i class="fas fa-home w-5 h-5 mr-3 <?php echo isActive($currentUri, '/student/dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white'; ?>"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/student-course-hub/student/explore_programmes"
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?php echo isActive($currentUri, '/student/explore_programmes') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                        <i class="fas fa-compass w-5 h-5 mr-3 <?php echo isActive($currentUri, '/student/explore_programmes') ? 'text-white' : 'text-gray-400 group-hover:text-white'; ?>"></i>
                        <span>Explore Programmes</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Academic -->
        <div class="mb-8">
            <h2 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Academic</h2>
            <ul class="space-y-1">
                <li>
                    <a href="/student-course-hub/student/my_courses"
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?php echo isActive($currentUri, '/student/my_courses') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                        <i class="fas fa-book w-5 h-5 mr-3 <?php echo isActive($currentUri, '/student/my_courses') ? 'text-white' : 'text-gray-400 group-hover:text-white'; ?>"></i>
                        <span>My Courses</span>
                    </a>
                </li>
                <li>
                    <a href="/student-course-hub/student/manage_interests"
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?php echo isActive($currentUri, '/student/interests') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                        <i class="fas fa-star w-5 h-5 mr-3 <?php echo isActive($currentUri, '/student/interests') ? 'text-white' : 'text-gray-400 group-hover:text-white'; ?>"></i>
                        <span>My Interests</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Account -->
        <div>
            <h2 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Account</h2>
            <ul class="space-y-1">
                <li>
                    <a href="/student-course-hub/student/profile"
                       class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200 <?php echo isActive($currentUri, '/student/profile') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                        <i class="fas fa-user w-5 h-5 mr-3 <?php echo isActive($currentUri, '/student/profile') ? 'text-white' : 'text-gray-400 group-hover:text-white'; ?>"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Footer -->
    <div class="p-4 border-t border-gray-800">
        <a href="/student-course-hub/auth/logout" 
           class="flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 group">
            <i class="fas fa-sign-out-alt mr-2 group-hover:transform group-hover:-translate-x-0.5 transition-transform"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
