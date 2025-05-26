


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Course Hub - Your Learning Journey Starts Here</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header styles */
        .header {
            background-color: #fff;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 3rem;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 2rem;
        }

        .nav-link {
            color: #1e293b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #3b82f6;
        }

        /* Hero section */
        .hero {
            text-align: center;
            margin-bottom: 4rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.25rem;
            color: #64748b;
            margin-bottom: 2rem;
        }

        /* CTA Buttons */
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 2rem 0;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: #fff;
            color: #1e293b;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        /* Features section */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2rem;
            color: #3b82f6;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #64748b;
        }

        /* Footer */
        .footer {
            margin-top: 4rem;
            padding: 2rem 0;
            background-color: #fff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            text-align: center;
            color: #64748b;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <nav>
                <ul class="nav-list">
                    <li><a href="/student-course-hub/" class="nav-link">Home</a></li>
                    <li><a href="/student-course-hub/auth/login" class="nav-link">Login</a></li>
                    <li><a href="/student-course-hub/auth/register" class="nav-link">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <section class="hero">
            <h1>Welcome to Student Course Hub</h1>
            <p>Your gateway to educational excellence. Access courses, track progress, and connect with fellow students all in one place.</p>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="cta-buttons">
                    <a href="/student-course-hub/auth/register" class="btn btn-primary">Get Started</a>
                    <a href="/student-course-hub/auth/login" class="btn btn-secondary">Sign In</a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="/student-course-hub/<?php echo $_SESSION['role']; ?>/dashboard" class="btn btn-primary">Go to Dashboard</a>
                </div>
            <?php endif; ?>
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Diverse Courses</h3>
                <p>Access a wide range of academic programs and professional courses.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Track Progress</h3>
                <p>Monitor your learning journey with detailed progress tracking.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Community Learning</h3>
                <p>Connect with fellow students and share your learning experience.</p>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Student Course Hub. All rights reserved.</p>
        </div>
    </footer>
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