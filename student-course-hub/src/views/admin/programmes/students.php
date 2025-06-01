<?php
$pageTitle = "Programme Students";
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="admin-container">
    <?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>

    <main class="admin-main">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Students - <?php echo htmlspecialchars($programme['title']); ?></h1>
                <a href="/student-course-hub/admin/programmes" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Programmes
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Registered Students</h5>
                        <a href="/student-course-hub/admin/programmes/<?php echo $programme['id']; ?>/students/export" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Export List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($students)): ?>
                        <div class="alert alert-info">No students are currently registered for this programme.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($student['registered_at']))); ?></td>
                                            <td>
                                                <span class="badge <?php echo $student['status'] === 'active' ? 'badge-success' : 'badge-warning'; ?>">
                                                    <?php echo ucfirst(htmlspecialchars($student['status'] ?? 'interested')); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/student-course-hub/admin/students/<?php echo $student['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
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

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
