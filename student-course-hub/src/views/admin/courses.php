<?php
require_once '../../controllers/AdminController.php';
require_once '../../controllers/AuthController.php';

// Initialize controllers
$auth = new AuthController();
$admin = new AdminController();

// Check admin authentication
$auth->requireRole('admin');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
                    $admin->createProgram($_POST);
                    $success = "Programme created successfully";
                    break;
                case 'update':
                    $admin->updateProgram($_POST['id'], $_POST);
                    $success = "Programme updated successfully";
                    break;
                case 'delete':
                    $admin->deleteProgram($_POST['id']);
                    $success = "Programme deleted successfully";
                    break;
                case 'publish':
                    $admin->publishProgram($_POST['id']);
                    $success = "Programme published successfully";
                    break;
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all programmes
$programmes = $admin->listProgrammes();

include_once '../layouts/header.php';
?>

<div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="active"><a href="courses.php">Programmes</a></li>
                <li><a href="students.php">Students</a></li>
                <li><a href="modules.php">Modules</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Manage Programmes</h1>
            <button class="btn btn-primary" onclick="showModal('createProgramModal')">
                <i class="fas fa-plus"></i> Add New Programme
            </button>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Programmes Grid -->
        <div class="programmes-grid">
            <?php foreach ($programmes as $programme): ?>
                <div class="programme-card <?php echo $programme['is_published'] ? 'published' : 'draft'; ?>">
                    <div class="programme-header">
                        <h3><?php echo htmlspecialchars($programme['title']); ?></h3>
                        <div class="status-badge">
                            <?php echo $programme['is_published'] ? 'Published' : 'Draft'; ?>
                        </div>
                    </div>
                    
                    <div class="programme-body">
                        <p><?php echo htmlspecialchars(substr($programme['description'], 0, 150)) . '...'; ?></p>
                        <div class="programme-meta">
                            <span>Level: <?php echo ucfirst($programme['level']); ?></span>
                            <span>Duration: <?php echo $programme['duration']; ?></span>
                        </div>
                    </div>

                    <div class="programme-actions">
                        <button class="btn btn-sm btn-edit" 
                                onclick="editProgramme(<?php echo $programme['id']; ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        
                        <form action="" method="POST" class="d-inline">
                            <input type="hidden" name="action" value="<?php echo $programme['is_published'] ? 'unpublish' : 'publish'; ?>">
                            <input type="hidden" name="id" value="<?php echo $programme['id']; ?>">
                            <button type="submit" class="btn btn-sm <?php echo $programme['is_published'] ? 'btn-unpublish' : 'btn-publish'; ?>">
                                <i class="fas fa-<?php echo $programme['is_published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                <?php echo $programme['is_published'] ? 'Unpublish' : 'Publish'; ?>
                            </button>
                        </form>

                        <button class="btn btn-sm btn-danger" 
                                onclick="confirmDelete(<?php echo $programme['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>

<!-- Create Programme Modal -->
<div id="createProgramModal" class="modal">
    <div class="modal-content">
        <h2>Add New Programme</h2>
        <form action="" method="POST">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="title">Programme Title</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="level">Level</label>
                    <select id="level" name="level" required>
                        <option value="undergraduate">Undergraduate</option>
                        <option value="postgraduate">Postgraduate</option>
                    </select>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" required>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="hideModal('createProgramModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Programme</button>
            </div>
        </form>
    </div>
</div>

<?php include_once '../layouts/footer.php'; ?>

<!-- Add the corresponding CSS -->