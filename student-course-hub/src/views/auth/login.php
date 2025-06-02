<?php
$pageTitle = 'Login';

// Set base URL
$base_url = '/student-course-hub';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header('Location: ' . $base_url . '/admin/dashboard');
    } else {
        header('Location: ' . $base_url . '/student/dashboard');
    }
    exit();
}

// Update error initialization to include session errors
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
$error = $error ?? '';

// Start output buffering
ob_start();
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Welcome back! Please login to continue.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            <?php echo htmlspecialchars($error); ?>
                        </h3>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="<?php echo $base_url; ?>/auth/login" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" required autofocus
                               class="appearance-none rounded-t-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
                               placeholder="Email address">
                    </div>
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="appearance-none rounded-b-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm" 
                               placeholder="Password">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>

                <div class="text-sm">
                    <a href="<?php echo $base_url; ?>/auth/forgot-password" class="font-medium text-primary-600 hover:text-primary-500">
                        Forgot password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-primary-500 group-hover:text-primary-400"></i>
                    </span>
                    Sign in
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="/student-course-hub/auth/register" class="font-medium text-primary-600 hover:text-primary-500">
                        Register here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>