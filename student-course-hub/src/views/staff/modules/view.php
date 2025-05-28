<?php
$pageTitle = "Module Details";
$headerText = "Module: " . htmlspecialchars($module['title']);
$breadcrumbText = "Module Details";
$activeMenu = "modules";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Staff</title>    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Base styles */
        body { 
            margin: 0; 
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
        }

        /* Staff container */
        .staff-container {
            display: flex;
            min-height: 100vh;
        }

        /* Main content area */
        .staff-main {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
        }

        /* Staff panel styles */
        .staff-panel {
            background: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .panel-header {
            padding: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #1e293b;
            font-weight: 600;
        }

        .panel-body {
            padding: 1.5rem;
        }

        /* Grid layout */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .grid-item {
            min-width: 0;
        }

        /* Module description */
        .module-description h3 {
            color: #1e293b;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .module-description p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Table styles */
        .staff-table {
            width: 100%;
            margin-bottom: 0;
        }

        .staff-table.table-details th {
            width: 150px;
            white-space: nowrap;
            padding: 0.75rem 1rem;
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
        }

        .staff-table.table-details td {
            padding: 0.75rem 1rem;
            color: #475569;
        }

        /* Button styles */
        .staff-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-weight: 500;
            color: #fff;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
        }

        .staff-btn-primary {
            background-color: #3b82f6;
        }

        .staff-btn-primary:hover {
            background-color: #2563eb;
        }

        .staff-btn i {
            margin-right: 0.5rem;
        }

        /* Header styles */
        .staff-header {
            margin-bottom: 2rem;
        }

        .staff-header h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        /* Actions container */
        .actions-container {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
        }
    </style>
</head>
<body>    <div class="staff-container">
        <!-- Staff Sidebar -->
        <?php include_once __DIR__ . '/../../layouts/staff_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="staff-main">
            <header class="staff-header">
                <div class="header-left">
                    <h1><?php echo htmlspecialchars($headerText); ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/student-course-hub/staff/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/student-course-hub/staff/modules">Modules</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Module Details</li>
                        </ol>
                    </nav>
                </div>
            </header>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="content-area">                <div class="staff-panel">
                    <div class="panel-header">
                        <h2>Module Information</h2>
                    </div>
                    <div class="panel-body">
                        <div class="grid-container">
                            <div class="grid-item">
                                <table class="staff-table table-details">
                                    <tbody>
                                        <tr>
                                            <th>Module Code:</th>
                                            <td><?php echo htmlspecialchars($module['code'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Level:</th>
                                            <td><?php echo htmlspecialchars($module['level'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Credits:</th>
                                            <td><?php echo htmlspecialchars($module['credits'] ?? 'N/A'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>                            </div>
                            <div class="grid-item">
                                <div class="module-description">
                                    <h3>Description</h3>
                                    <p><?php echo nl2br(htmlspecialchars($module['description'] ?? 'No description available.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="actions-container">
                    <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>/students" 
                       class="staff-btn staff-btn-primary">
                        <i class="fas fa-users"></i> View Students
                    </a>
                </div>
            </div>
        </main>
    </div>
    <script src="/student-course-hub/public/assets/js/staff.js"></script>
</body>
</html>
