<?php
// register.php

// Include dependencies
require_once __DIR__ . '/../../controllers/AuthController.php';

// Initialize controller
$auth = new AuthController();

// Initialize variables
$error = '';
$success = '';

require_once __DIR__ . '/../../views/layouts/header.php';

// Initialize variables
$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
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
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}


?>

<div class="auth-container">
    <div class="auth-box">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join our community of students and explore courses!</p>
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

        <form method="POST" class="auth-form" id="registerForm">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required 
                           autofocus
                           minlength="3"
                           maxlength="50"
                           pattern="[A-Za-z0-9_-]+"
                           title="Username can only contain letters, numbers, underscores and hyphens"
                           placeholder="Choose a username">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required
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
                           minlength="8"
                           placeholder="Create a password">
                    <button type="button" class="toggle-password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required
                           placeholder="Confirm your password">
                </div>
            </div>

            <div class="form-group terms">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                    I agree to the <a href="/terms.php">Terms of Service</a> and 
                    <a href="/privacy.php">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="/auth/login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/layouts/footer.php'; ?>