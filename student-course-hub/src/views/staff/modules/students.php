<?php
$pageTitle = "Module Students";
$headerText = "Students in " . htmlspecialchars($module['title']);
$breadcrumbText = "Module Students";
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

        /* Table styles */
        .staff-table {
            width: 100%;
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .staff-table th {
            padding: 1rem;
            text-align: left;
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .staff-table td {
            padding: 1rem;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }

        .staff-table tr:last-child td {
            border-bottom: none;
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

        .staff-btn-info {
            background-color: #0ea5e9;
        }

        .staff-btn-info:hover {
            background-color: #0284c7;
        }

        .staff-btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .staff-btn i {
            margin-right: 0.5rem;
        }

        /* Alert styles */
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.375rem;
            border: 1px solid transparent;
        }

        .alert-info {
            background-color: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
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
                        <h2>Enrolled Students</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($students)): ?>
                            <div class="alert alert-info">
                                No students are currently enrolled in this module.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Enrolled Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars(date('d M Y', strtotime($student['enrolled_date']))); ?></td>
                                                <td>
                                                    <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>/students/<?php echo $student['id']; ?>/details" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="/student-course-hub/public/assets/js/admin.js"></script>
</body>
</html>
