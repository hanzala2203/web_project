<?php
// Start session and include necessary files
require_once __DIR__ . '/../../controllers/AuthController.php';

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

$error = '';

// Include header
require_once __DIR__ . '/../../views/layouts/header.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $auth = new AuthController();
        $auth->login($_POST['email'], $_POST['password']);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Login</h1>
            <p>Welcome back! Please login to your account.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           autofocus
                           placeholder="Enter your email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="Enter your password">
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="<?php echo $base_url; ?>/auth/forgot-password" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="<?php echo $base_url; ?>/auth/register">Register here</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>