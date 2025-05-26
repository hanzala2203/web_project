<?php
$pageTitle = "My Teaching Modules";
$headerText = "My Teaching Modules";
$breadcrumbText = "Modules";
$activeMenu = "modules";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Staff</title>
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
            line-height: 1.5;
            background-color: #f1f5f9;
        }

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

        .staff-header {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .staff-header h1 {
            color: #1e293b;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .module-card {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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

        .module-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .module-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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
            background-color: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .no-modules {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            background: #fff;
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
        <?php include_once __DIR__ . '/../../layouts/staff_sidebar.php'; ?>

        <main class="staff-main">
            <div class="staff-header">
                <h1><?php echo htmlspecialchars($headerText); ?></h1>
            </div>

            <div class="module-grid">
                <?php if (!empty($modules)): 
                    foreach ($modules as $module): ?>
                        <div class="module-card">
                            <div class="module-info">
                                <h3><?php echo htmlspecialchars($module['name']); ?></h3>
                                <p class="module-code"><?php echo htmlspecialchars($module['code']); ?></p>
                                
                                <div class="module-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo htmlspecialchars($module['student_count']); ?> Students</span>
                                    </div>
                                </div>

                                <div class="module-actions">
                                    <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>" class="module-btn btn-primary">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                    <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>/students" class="module-btn btn-secondary">
                                        <i class="fas fa-users"></i>
                                        Students
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;
                else: ?>
                    <div class="no-modules">
                        <i class="fas fa-book"></i>
                        <p>No modules have been assigned to you yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
