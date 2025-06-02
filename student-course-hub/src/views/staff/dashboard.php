<?php
$pageTitle = "Staff Dashboard";
$headerText = "My Teaching Responsibilities";
$breadcrumbText = "Dashboard";
$activeMenu = "dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style type="text/css">
        /* Reset and base styles */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            background-color: #f1f5f9;
        }

        /* Staff Dashboard Layout */
        .staff-container {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
        }

        .staff-main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
        }

        /* Header Styles */
        .staff-header {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .staff-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .staff-header p {
            color: #64748b;
            font-size: 1rem;
            margin: 0;
        }

        /* Welcome Section */
        .staff-welcome {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .staff-welcome h2 {
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .staff-welcome p {
            color: #64748b;
            font-size: 1.1rem;
        }

        /* Status Cards */
        .staff-status-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .status-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }

        .status-card i {
            font-size: 2rem;
            color: #3b82f6;
            margin-right: 1rem;
        }

        .status-info h3 {
            color: #1e293b;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .status-info p {
            color: #64748b;
        }

        /* Module Section Styles */
        .staff-modules {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            color: #1e293b;
            font-size: 1.5rem;
        }

        .staff-btn {
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: #fff;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            transition: background-color 0.2s;
        }

        .staff-btn:hover {
            background-color: #2563eb;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .module-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .module-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .module-info h3 {
            color: #1e293b;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .module-code {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .module-students {
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-link {
            display: inline-flex;
            align-items: center;
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.875rem;
            margin-top: 1rem;
            gap: 0.5rem;
        }

        .module-link:hover {
            color: #2563eb;
        }

        .no-modules {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            color: #64748b;
        }

        .no-modules i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="staff-container">
        <!-- Staff Sidebar -->
        <?php include_once __DIR__ . '/../layouts/staff_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="staff-main">
            <div class="staff-header">
                <h1><?php echo htmlspecialchars($headerText); ?></h1>
            </div>

            <div class="staff-welcome">
                <h2>Welcome Back!</h2>
                <p>Access and manage your teaching modules and student information from this dashboard.</p>
            </div>

            <!-- Teaching Modules Section -->
            <section class="staff-modules">
                <div class="section-header">
                    <h2>My Teaching Modules</h2>
                    <a href="/student-course-hub/staff/modules" class="staff-btn">View All Modules</a>
                </div>
                <div class="module-grid">
                    <?php
                    // Assuming you have a getTeachingModules function in your StaffController
                    $modules = isset($modules) ? $modules : [];
                    if (!empty($modules)): 
                        foreach ($modules as $module): ?>
                            <div class="module-card">
                                <div class="module-info">
                                    <h3><?php echo htmlspecialchars($module['name']); ?></h3>
                                    <p class="module-code"><?php echo htmlspecialchars($module['code']); ?></p>
                                    <p class="module-students">
                                        <i class="fas fa-users"></i> 
                                        <?php echo htmlspecialchars($module['student_count'] ?? 0); ?> Students
                                    </p>
                                </div>
                                <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>" class="module-link">
                                        <!-- <i class="fas fa-eye"></i> -->
                                        View Details
                                        <i class="fas fa-arrow-right"></i>
                                 </a>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <div class="no-modules">
                            <i class="fas fa-book"></i>
                            <p>No modules assigned yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <div class="staff-status-cards">
                <div class="status-card">
                    <i class="fas fa-calendar"></i>
                    <div class="status-info">
                        <h3>Current Term</h3>
                        <p><?php echo date('F Y'); ?></p>
                    </div>
                </div>
                <div class="status-card">
                    <i class="fas fa-clock"></i>
                    <div class="status-info">
                        <h3>Last Login</h3>
                        <p><?php echo isset($_SESSION['last_login']) ? date('d M Y H:i', strtotime($_SESSION['last_login'])) : 'First Login'; ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
