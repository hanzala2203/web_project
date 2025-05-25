<?php
require_once '../../controllers/AdminController.php';
require_once '../../controllers/AuthController.php';

// Initialize controllers
$auth = new AuthController();
$admin = new AdminController();

// Check admin authentication
$auth->requireRole('admin');

// Handle export action
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $admin->exportStudentData();
    exit();
}

// Get students list with filters
$filters = [
    'programme' => $_GET['programme'] ?? null,
    'search' => $_GET['search'] ?? null
];

$students = $admin->getStudentsList($filters);
$programmes = $admin->listProgrammes();

include_once '../layouts/header.php';
?>

<div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="courses.php"><i class="fas fa-graduation-cap"></i> Programmes</a></li>
                <li class="active"><a href="students.php"><i class="fas fa-users"></i> Students</a></li>
                <li><a href="modules.php"><i class="fas fa-book"></i> Modules</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Students</h1>
            <div class="header-actions">
                <a href="?action=export" class="btn btn-success">
                    <i class="fas fa-file-export"></i> Export Data
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form action="" method="GET" class="filters-form">
                <div class="form-group">
                    <input type="text" 
                           name="search" 
                           placeholder="Search students..." 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                           class="form-control">
                </div>
                
                <div class="form-group">
                    <select name="programme" class="form-control">
                        <option value="">All Programmes</option>
                        <?php foreach ($programmes as $programme): ?>
                            <option value="<?php echo $programme['id']; ?>"
                                    <?php echo (isset($_GET['programme']) && $_GET['programme'] == $programme['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($programme['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <!-- Students Table -->
        <div class="table-responsive">
            <table class="table students-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Interested Programmes</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td>
                                <?php foreach ($student['interests'] as $interest): ?>
                                    <span class="badge badge-primary">
                                        <?php echo htmlspecialchars($interest['title']); ?>
                                    </span>
                                <?php endforeach; ?>
                            </td>
                            <td><?php echo date('d M Y', strtotime($student['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" 
                                        onclick="viewDetails(<?php echo $student['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" 
                                        onclick="removeStudent(<?php echo $student['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Student Details Modal -->
<div id="studentDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Student Details</h2>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<?php include_once '../layouts/footer.php'; ?>