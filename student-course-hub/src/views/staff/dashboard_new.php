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

        /* Module Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .module-card {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .module-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
        }

        .module-code {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-programme {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            color: #fff;
            background-color: #3b82f6;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            gap: 0.5rem;
        }

        .btn:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        /* Alert Styles */
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #dcfce7;
            border-color: #86efac;
            color: #166534;
        }

        .alert-error {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
        }

        .empty-state i {
            font-size: 2rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #64748b;
            font-size: 1rem;
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
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid">
                <?php if (isset($modules) && !empty($modules)): ?>
                    <?php foreach ($modules as $module): ?>
                        <div class="module-card">
                            <h3 class="module-title"><?php echo htmlspecialchars($module['title']); ?></h3>
                            <?php if (isset($module['code'])): ?>
                                <div class="module-code">
                                    <i class="fas fa-hashtag"></i> <?php echo htmlspecialchars($module['code']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($module['programme_name'])): ?>
                                <div class="module-programme">
                                    <i class="fas fa-university"></i> <?php echo htmlspecialchars($module['programme_name']); ?>
                                </div>
                            <?php endif; ?>
                            <div style="margin-top: auto;">
                                <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>" class="btn">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="module-card empty-state">
                        <i class="fas fa-info-circle"></i>
                        <p>No modules are currently assigned to you.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
