<?php
$pageTitle = 'Register';

// Set base URL
$base_url = '/student-course-hub';

// Initialize variables
$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $base_url . '/dashboard');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $auth = new AuthController();
        $auth->register([
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'confirm_password' => $_POST['confirm_password']
        ]);
        $success = "Registration successful! Please login.";
        header('Location: ' . $base_url . '/login');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

ob_start();
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Join our community of students and explore courses!
            </p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
                <script>
                    setTimeout(() => {
                        window.location.href = '<?php echo $base_url; ?>/auth/login';
                    }, 3000);
                </script>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="<?php echo $base_url; ?>/register" method="POST" id="registerForm">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required autofocus
                               minlength="3" maxlength="50" pattern="[A-Za-z0-9_-]+"
                               class="appearance-none rounded-t-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Choose a username"
                               title="Username can only contain letters, numbers, underscores and hyphens">
                    </div>
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email" required
                               class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Enter your email">
                    </div>
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required minlength="8"
                               class="appearance-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Create a password">
                    </div>
                </div>
                <div>
                    <label for="confirm_password" class="sr-only">Confirm Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="appearance-none rounded-b-md relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Confirm your password">
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="terms" name="terms" required
                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-900">
                    I agree to the 
                    <a href="/terms" class="font-medium text-primary-600 hover:text-primary-500">Terms of Service</a>
                    and
                    <a href="/privacy" class="font-medium text-primary-600 hover:text-primary-500">Privacy Policy</a>
                </label>
            </div>

            <div>
                <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-primary-500 group-hover:text-primary-400"></i>
                    </span>
                    Create Account
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="<?php echo $base_url; ?>/login" class="font-medium text-primary-600 hover:text-primary-500">
                        Login here
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