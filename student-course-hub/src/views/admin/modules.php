<?php
$pageTitle = "Manage Modules";
$headerText = "Modules";
$breadcrumbText = "Manage Modules";
$activeMenu = "modules";

error_log("=== Admin Modules View START ===");
error_log("Time: " . date('Y-m-d H:i:s'));
error_log("Modules variable set: " . (isset($modules) ? 'yes' : 'no'));
if (isset($modules)) {
    error_log("Number of modules: " . count($modules));
    if (!empty($modules)) {
        error_log("First module data: " . print_r($modules[0], true));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">    <link rel="stylesheet" href="../../public/assets/css/admin-dashboard.css">
</head>
<body>
    <div class="admin-container">
        <?php include_once __DIR__ . '/../layouts/sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-content">
                    <h1><?php echo htmlspecialchars($headerText); ?></h1>
                    <nav class="breadcrumb">
                        <a href="/student-course-hub/admin/dashboard">Dashboard</a> /
                        <span><?php echo htmlspecialchars($breadcrumbText); ?></span>
                    </nav>
                </div>
            </header>

            <div class="admin-content">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                        <?php unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                        <?php unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h2>All Modules</h2>
                    <a href="/student-course-hub/admin/modules/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Module
                    </a>
                </div>                <div class="table-responsive">
                    <?php 
                    error_log("Modules in view: " . print_r($modules ?? 'No modules variable', true));
                    if (empty($modules)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No modules found in the system.
                        </div>
                    <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Programme</th>
                                <th>Credits</th>
                                <th>Year of Study</th>
                                <th>Staff Assigned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>                            <?php
                                error_log("Modules array in view: " . print_r($modules ?? 'No modules variable', true));
                                if (!empty($modules)): ?>
                                <?php foreach ($modules as $module): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($module['title'] ?? 'No Title'); ?></td>
                                        <td><?php echo htmlspecialchars($module['programme_name'] ?? 'Not Assigned'); ?></td>
                                        <td><?php echo $module['credits'] ?? 'N/A'; ?></td>
                                        <td><?php echo $module['year_of_study'] ?? 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($module['staff_name'] ?? 'Not Assigned'); ?></td>
                                        <td class="actions">
                                            <a href="/student-course-hub/admin/modules/<?php echo $module['id']; ?>/edit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="/student-course-hub/admin/modules/<?php echo $module['id']; ?>/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this module?');">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../public/assets/js/admin.js"></script>
</body>
</html>
