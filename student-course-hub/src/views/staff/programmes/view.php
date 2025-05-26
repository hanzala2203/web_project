<?php
$pageTitle = "Programme Details";
$headerText = "Programme: " . htmlspecialchars($programme['title']);
$breadcrumbText = "Programme Details";
$activeMenu = "programmes";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/student-course-hub/public/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

        <div class="admin-main">
            <header class="admin-header-main">
                <div class="header-left">
                    <h1><?php echo htmlspecialchars($headerText); ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/student-course-hub/staff/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumbText); ?></li>
                        </ol>
                    </nav>
                </div>
            </header>

            <main class="content-area">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2>Programme Information</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Title:</th>
                                            <td><?php echo htmlspecialchars($programme['title']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Level:</th>
                                            <td><?php echo htmlspecialchars($programme['level']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Description:</th>
                                            <td><?php echo htmlspecialchars($programme['description'] ?? 'No description available.'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>My Modules in this Programme</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($programme['staff_modules'])): ?>
                            <div class="alert alert-info">You don't have any modules in this programme.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Module Code</th>
                                            <th>Title</th>
                                            <th>Credits</th>
                                            <th>Year of Study</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($programme['staff_modules'] as $module): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($module['code'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($module['title']); ?></td>
                                                <td><?php echo htmlspecialchars($module['credits']); ?></td>
                                                <td><?php echo htmlspecialchars($module['year_of_study'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <a href="/student-course-hub/staff/modules/<?php echo $module['id']; ?>" class="btn btn-sm btn-info">
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
            </main>

            <?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
        </div>
    </div>
    <script src="/student-course-hub/public/assets/js/admin.js"></script>
</body>
</html>
