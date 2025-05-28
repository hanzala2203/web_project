<?php
require_once __DIR__ . '/../../controllers/StudentController.php';
use App\Controllers\StudentController;

// Check for session or redirect to login
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$studentId = $_SESSION['user_id'];
$studentController = new StudentController();

try {
    // Get all interested programmes
    $interestedProgrammes = $studentController->student->getInterestedCourses($studentId);
    
    // Get student details
    $student = $studentController->student->findById($studentId);
    
    $pageTitle = "Manage Programme Interests";
} catch (Exception $e) {
    $error = $e->getMessage();
    $pageTitle = "Manage Interests";
}

require_once '../layouts/header.php';
?>

<div class="container">
    <div class="nav-back">
        <a href="/student-course-hub/student/explore_programmes.php"><i class="fas fa-arrow-left"></i> Back to Programmes</a>
    </div>
    
    <header>
        <h1>Manage Your Programme Interests</h1>
        <p>View and manage the programmes you're interested in</p>
    </header>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php else: ?>
        <?php if (empty($interestedProgrammes)): ?>
            <div class="no-interests">
                <i class="fas fa-info-circle"></i>
                <h3>No registered interests</h3>
                <p>You haven't registered interest in any programmes yet. <a href="explore_programmes.php">Explore programmes</a> to find ones that interest you.</p>
            </div>
        <?php else: ?>
            <div class="interests-count">
                <p>You have registered interest in <strong><?= count($interestedProgrammes) ?></strong> programme(s).</p>
            </div>
            
            <div class="interests-grid">
                <?php foreach ($interestedProgrammes as $programme): ?>
                    <div class="interest-card">
                        <div class="interest-image" style="background-image: url('<?= htmlspecialchars($programme['image_url'] ?? '/assets/images/default-programme.jpg') ?>')">
                            <span class="interest-level">
                                <?= ucfirst(htmlspecialchars($programme['level'] ?? 'undergraduate')) ?>
                            </span>
                        </div>
                        <div class="interest-content">
                            <h3 class="interest-title"><?= htmlspecialchars($programme['title']) ?></h3>
                            <p class="interest-description">
                                <?= htmlspecialchars(substr($programme['description'] ?? 'No description available', 0, 150)) . (strlen($programme['description'] ?? '') > 150 ? '...' : '') ?>
                            </p>
                            
                            <div class="interest-actions">
                                <a href="programme_details.php?id=<?= $programme['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-info-circle"></i> View Details
                                </a>
                                <form action="withdraw_interest.php" method="POST">
                                    <input type="hidden" name="programme_id" value="<?= $programme['id'] ?>">
                                    <input type="hidden" name="redirect" value="manage_interests.php">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to withdraw your interest in this programme?');">
                                        <i class="fas fa-times-circle"></i> Withdraw Interest
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    header {
        margin-bottom: 2rem;
        text-align: center;
    }

    header h1 {
        font-size: 2rem;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    header p {
        color: #64748b;
    }

    .nav-back {
        margin-bottom: 2rem;
    }

    .nav-back a {
        display: inline-flex;
        align-items: center;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .nav-back a:hover {
        color: #2563eb;
    }

    .nav-back i {
        margin-right: 0.5rem;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background-color: #dcfce7;
        color: #166534;
        border-left: 4px solid #10b981;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }

    .no-interests {
        text-align: center;
        padding: 3rem;
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .no-interests i {
        font-size: 3rem;
        color: #94a3b8;
        margin-bottom: 1rem;
    }

    .no-interests h3 {
        color: #1e293b;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .no-interests p {
        color: #64748b;
    }

    .no-interests a {
        color: #3b82f6;
        text-decoration: none;
    }

    .interests-count {
        margin-bottom: 1.5rem;
    }

    .interests-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }

    .interest-card {
        background-color: #ffffff;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .interest-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .interest-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        background-color: #e2e8f0;
        position: relative;
    }

    .interest-level {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background-color: #3b82f6;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .interest-content {
        padding: 1.5rem;
    }

    .interest-title {
        font-size: 1.25rem;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .interest-description {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .interest-actions {
        display: flex;
        gap: 1rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
        flex: 1;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-danger {
        background-color: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background-color: #dc2626;
    }

    @media (max-width: 768px) {
        .interests-grid {
            grid-template-columns: 1fr;
        }
        
        .interest-actions {
            flex-direction: column;
        }
    }
</style>

<?php require_once '../layouts/footer.php'; ?>
