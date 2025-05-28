<?php
$pageTitle = "Student Details";
$headerText = "Student Details - " . htmlspecialchars($student['username']);
$breadcrumbText = "Student Details";
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

        /* Staff header */
        .staff-header {
            margin-bottom: 2rem;
        }

        .staff-header h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        /* Progress bar */
        .progress {
            background-color: #e2e8f0;
            border-radius: 9999px;
            height: 1rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .bg-success {
            background-color: #22c55e;
        }

        /* Info text */
        .text-muted {
            color: #64748b;
            font-size: 0.875rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        /* Panel styles */
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

        .panel-header h2, .panel-header h3 {
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

        /* Table styles */
        .staff-table {
            width: 100%;
            margin-bottom: 0;
        }

        .staff-table th {
            padding: 0.75rem 1rem;
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .staff-table td {
            padding: 0.75rem 1rem;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Alert styles */
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

        .alert-danger {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        /* Progress info */
        .progress-info {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8fafc;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>    <div class="staff-container">
        <!-- Staff Sidebar -->
        <?php include_once __DIR__ . '/../../layouts/staff_sidebar.php'; ?>        <!-- Main Content -->
        <main class="staff-main">
            <header class="admin-header-main">
                <div class="header-left">
                    <h1><?php echo htmlspecialchars($headerText); ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/student-course-hub/staff/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>">Module Details</a></li>
                            <li class="breadcrumb-item"><a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>/students">Module Students</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $breadcrumbText; ?></li>
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

            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h2>Student Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-details">
                                    <tbody>
                                        <tr>
                                            <th>Student ID:</th>
                                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Username:</th>
                                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Enrolled Date:</th>
                                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($student['enrolled_date']))); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h3>Module Progress</h3>
                                <?php if (isset($student['module_progress'])): ?>
                                    <div class="progress-info">
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                style="width: <?php echo $student['module_progress']; ?>%" 
                                                aria-valuenow="<?php echo $student['module_progress']; ?>" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                <?php echo $student['module_progress']; ?>%
                                            </div>
                                        </div>
                                        <p class="text-muted mt-2">Last activity: <?php echo htmlspecialchars($student['last_activity_date'] ?? 'No recent activity'); ?></p>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No progress data available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($student['assignments'])): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h2>Assignment Progress</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Assignment</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($student['assignments'] as $assignment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($assignment['due_date']))); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $assignment['status'] === 'Submitted' ? 'success' : 'warning'; ?>">
                                                    <?php echo htmlspecialchars($assignment['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $assignment['grade'] ?? 'Not graded'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="/student-course-hub/public/assets/js/admin.js"></script>
</body>
</html>
