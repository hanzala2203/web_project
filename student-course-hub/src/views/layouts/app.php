<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Student Course Hub'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/student-course-hub" class="text-xl font-bold text-primary-600">
                            Student Course Hub
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="ml-3 relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-primary-600 focus:outline-none">
                                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <i class="fas fa-user-circle text-xl"></i>
                            </button>
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1" role="menu">
                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="/student-course-hub/admin/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Dashboard</a>
                                    <?php elseif ($_SESSION['role'] === 'student'): ?>
                                        <a href="/student-course-hub/student/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Dashboard</a>
                                    <?php elseif ($_SESSION['role'] === 'staff'): ?>
                                        <a href="/student-course-hub/staff/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Dashboard</a>
                                    <?php endif; ?>
                                    <a href="/student-course-hub/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/student-course-hub/login" class="text-gray-700 hover:text-primary-600 px-3 py-2">Login</a>
                        <a href="/student-course-hub/register" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Page content will be inserted here -->
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow-md mt-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500">© <?php echo date('Y'); ?> Student Course Hub. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
