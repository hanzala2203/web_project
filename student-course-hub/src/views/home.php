


<?php require_once __DIR__ . '/layouts/header.php'; ?>

<main class="home-main">
    <section class="hero-section">
        <div class="hero-content">
            <h1>Welcome to Student Course Hub</h1>
            <p class="hero-text">Your gateway to educational excellence. Access courses, track progress, and connect with fellow students all in one place.</p>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="cta-buttons">
                    <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-primary">Get Started</a>
                    <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-secondary">Sign In</a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="<?php echo BASE_URL; ?>/<?php echo $_SESSION['role']; ?>/dashboard" class="btn btn-primary">Go to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="features-section">
        <h2>Why Choose Student Course Hub?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-graduation-cap"></i>
                <h3>Diverse Courses</h3>
                <p>Access a wide range of academic programs and professional courses.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Track Progress</h3>
                <p>Monitor your learning journey with detailed progress tracking.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-users"></i>
                <h3>Community</h3>
                <p>Connect with fellow students and share your learning experience.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-certificate"></i>
                <h3>Certificates</h3>
                <p>Earn certificates upon successful course completion.</p>
            </div>
        </div>
    </section>

    <section class="quick-access">
        <h2>Quick Access</h2>
        <div class="quick-access-grid">
            <a href="<?php echo BASE_URL; ?>/courses" class="quick-access-card">
                <i class="fas fa-book"></i>
                <span>Browse Courses</span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>/<?php echo $_SESSION['role']; ?>/dashboard" class="quick-access-card">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/about" class="quick-access-card">
                <i class="fas fa-info-circle"></i>
                <span>About Us</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/contact" class="quick-access-card">
                <i class="fas fa-envelope"></i>
                <span>Contact Us</span>
            </a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>