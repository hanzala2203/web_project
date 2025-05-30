<?php
require_once __DIR__ . '/../../controllers/StudentController.php';
use App\Controllers\StudentController;

// Check if programme ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {    header('Location: /student-course-hub/student/explore_programmes.php');
    exit;
}

$programmeId = (int)$_GET['id'];
$studentId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$studentController = new StudentController();

try {
    // Get programme details with structure
    $programmeData = $studentController->getProgrammeStructure($programmeId);
    
    // Extract programme details and structure
    $programme = $programmeData['programme'];
    $structure = $programmeData['structure'];
    $totalCredits = $programmeData['total_credits'];
    
    // Check if student has registered interest
    $hasInterest = false;
    if ($studentId) {
        $hasInterest = $studentController->student->hasInterest($studentId, $programmeId);
    }
    
    // Get programme staff members
    $staffMembers = $studentController->programme->getStaffMembers($programmeId);
    
    $pageTitle = "Programme Details: " . htmlspecialchars($programme['title']);
} catch (Exception $e) {
    // Handle errors
    $error = $e->getMessage();
    $pageTitle = "Programme Details";
}

require_once '../layouts/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Course Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #4CAF50;
            --error-color: #ef4444;
            --link-color: #2563eb;
            --link-hover-color: #1d4ed8;
            --bg-color: #f1f5f9;
            --card-color: #ffffff;
        }

        /* Navigation Bar */
        .nav-back {
            margin-bottom: 2rem;
        }

        .nav-back a {
            display: inline-flex;
            align-items: center;
            color: var(--link-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-back a:hover {
            color: var(--link-hover-color);
        }

        .nav-back i {
            margin-right: 0.5rem;
        }
        
        /* Programme Banner */
        .programme-banner {
            height: 250px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .programme-header {
            background: #fff;
            padding: 0;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .programme-info {
            padding: 2rem;
        }

        .programme-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .programme-title h1 {
            font-size: 1.8rem;
            color: #1e293b;
            margin: 0;
        }

        .programme-level {
            font-size: 0.875rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            background: var(--primary-color);
            color: white;
            text-transform: capitalize;
        }

        .programme-meta {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .programme-description {
            color: #475569;
            line-height: 1.6;
        }        .programme-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .programme-modules {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .year-tabs {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            gap: 0.5rem;
        }

        .year-tab {
            padding: 0.5rem 1rem;
            background-color: #e2e8f0;
            border-radius: 0.375rem;
            color: #1e293b;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            white-space: nowrap;
        }

        .year-tab:hover {
            background-color: #cbd5e1;
        }

        .year-tab.active {
            background-color: var(--primary-color);
            color: white;
        }

        .year-content {
            display: none;
        }

        .year-content.active {
            display: block;
        }

        .year-modules {
            margin-bottom: 2rem;
        }

        .year-modules h3 {
            color: #1e293b;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .module-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .module-card {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .module-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .module-title-group {
            flex: 1;
        }

        .module-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            display: block;
            margin-bottom: 0.25rem;
        }

        .module-code {
            font-size: 0.875rem;
            color: #64748b;
        }

        .module-credits {
            background: #f0f9ff;
            color: #0369a1;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .module-body {
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
            margin-bottom: 1rem;
        }

        .module-description {
            color: #475569;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .module-staff {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .staff-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .staff-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .staff-info {
            flex: 1;
        }

        .staff-name {
            font-weight: 600;
            color: #1e293b;
            display: block;
            margin-bottom: 0.25rem;
        }

        .staff-title {
            font-size: 0.875rem;
            color: #64748b;
            display: block;
        }

        .staff-expertise {
            font-size: 0.75rem;
            color: var(--primary-color);
            display: block;
            margin-top: 0.25rem;
        }

        .module-prerequisites {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.5rem;
        }

        .btn-module-details {
            width: 100%;
            padding: 0.75rem;
            background: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            color: #1e293b;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-module-details:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .module-stats {
            display: flex;
            gap: 2rem;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            font-size: 1rem;
            font-weight: 500;
        }

        .stat i {
            font-size: 1.25rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 2rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .module-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .meta-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .meta-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .meta-box i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .meta-box span:first-of-type {
            font-weight: 600;
            color: #1e293b;
        }

        .meta-box .meta-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .module-staff {
            margin-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5rem;
        }

        .module-staff h4 {
            font-size: 1rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .staff-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .staff-card:hover {
            background: #f1f5f9;
        }

        @media (max-width: 768px) {
            .programme-content {
                grid-template-columns: 1fr;
            }

            .programme-meta {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>    <div class="container">
        <div class="nav-back">
            <a href="explore_programmes.php"><i class="fas fa-arrow-left"></i> Back to Programmes</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (isset($programme)): ?>
            <!-- Programme Header -->
            <div class="programme-header">
                <div class="programme-banner" style="background-image: url('<?= htmlspecialchars($programme['image_url'] ?? '/assets/images/default-programme.jpg') ?>')">
                    <span class="programme-level">
                        <?= ucfirst(htmlspecialchars($programme['level'] ?? 'undergraduate')) ?>
                    </span>
                </div>
                <div class="programme-info">
                    <h1 class="programme-title"><?= htmlspecialchars($programme['title']) ?></h1>
                    
                    <div class="programme-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Duration: <?= htmlspecialchars($programme['duration_years'] ?? '3') ?> Years</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-book"></i>
                            <span><?= $programme['module_count'] ?? 0 ?> Modules</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-graduation-cap"></i>
                            <span><?= $totalCredits ?? 0 ?> Total Credits</span>
                        </div>
                    </div>
                    
                    <div class="programme-description">
                        <?= nl2br(htmlspecialchars($programme['description'] ?? 'No description available')) ?>
                    </div>
                    
                    <div class="programme-actions">
                        <?php if ($studentId): ?>
                            <?php if ($hasInterest): ?>
                                <form action="withdraw_interest.php" method="POST">
                                    <input type="hidden" name="programme_id" value="<?= $programmeId ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-bookmark"></i> Remove Interest
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="register_interest.php" method="POST">
                                    <input type="hidden" name="programme_id" value="<?= $programmeId ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="far fa-bookmark"></i> Register Interest
                                    </button>
                                </form>
                            <?php endif; ?>
                            <a href="manage_interests.php" class="btn btn-outline">
                                <i class="fas fa-list"></i> Manage Interests
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/auth/login?redirect=<?= BASE_URL ?>/student/programme_details?id=<?= $programmeId ?>" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login to Register Interest
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="programme-content">                <!-- Modules Section -->
                <div class="programme-modules" id="modules">
                    <div class="section-header">
                        <h2>Programme Structure</h2>
                        <div class="module-stats">
                            <div class="stat">
                                <i class="fas fa-book"></i>
                                <span><?= array_sum(array_map(function($year) { return array_sum(array_map('count', $year)); }, $structure ?? [])) ?> Total Modules</span>
                            </div>
                            <div class="stat">
                                <i class="fas fa-graduation-cap"></i>
                                <span><?= $totalCredits ?> Credits</span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (empty($structure)): ?>
                        <div class="empty-state">
                            <i class="fas fa-info-circle"></i>
                            <p>No structure information available for this programme yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="year-tabs">
                            <?php foreach ($structure as $yearNum => $yearData): ?>
                                <button class="year-tab <?= $yearNum === array_key_first($structure) ? 'active' : '' ?>" 
                                        onclick="showYear(<?= $yearNum ?>)">
                                    Year <?= $yearNum ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php foreach ($structure as $yearNum => $yearData): ?>
                            <div class="year-content <?= $yearNum === array_key_first($structure) ? 'active' : '' ?>" id="year-<?= $yearNum ?>">
                                <?php foreach ($yearData as $semesterNum => $modules): ?>
                                    <div class="year-modules">
                                        <h3>Semester <?= $semesterNum ?></h3>
                                        <div class="module-list">
                                            <?php foreach ($modules as $module): ?>                                            <div class="module-card" data-module="<?= htmlspecialchars(json_encode($module)) ?>">
                                                    <div class="module-header">
                                                        <div class="module-title-group">
                                                            <span class="module-name"><?= htmlspecialchars($module['title']) ?></span>
                                                            <span class="module-code"><?= htmlspecialchars($module['code'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <span class="module-credits"><?= $module['credits'] ?> Credits</span>
                                                    </div>                                                    <div class="module-body">
                                                        <div class="module-description">
                                                            <?= htmlspecialchars($module['description'] ?? 'No description available') ?>
                                                        </div>
                                                        <div class="module-meta">
                                                            <div class="module-meta-grid">
                                                                <div class="meta-box">
                                                                    <i class="fas fa-graduation-cap"></i>
                                                                    <span><?= $module['credits'] ?> Credits</span>
                                                                    <span class="meta-label">Required Credits</span>
                                                                </div>
                                                                <div class="meta-box">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                    <span>Year <?= $yearNum ?>, Semester <?= $semesterNum ?></span>
                                                                    <span class="meta-label">Study Period</span>
                                                                </div>
                                                                <?php if (!empty($module['assessment_method'])): ?>
                                                                <div class="meta-box">
                                                                    <i class="fas fa-tasks"></i>
                                                                    <span><?= htmlspecialchars($module['assessment_method']) ?></span>
                                                                    <span class="meta-label">Assessment Method</span>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <?php if (!empty($module['staff'])): ?>
                                                                <div class="module-staff">
                                                                    <h4>Teaching Staff</h4>
                                                                    <?php foreach ($module['staff'] as $staffMember): ?>
                                                                        <div class="staff-card">
                                                                            <div class="staff-avatar">
                                                                                <?php if (!empty($staffMember['avatar_url'])): ?>
                                                                                    <img src="<?= htmlspecialchars($staffMember['avatar_url']) ?>" 
                                                                                         alt="<?= htmlspecialchars($staffMember['name']) ?>">
                                                                                <?php else: ?>
                                                                                    <i class="fas fa-user"></i>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="staff-info">
                                                                                <span class="staff-name"><?= htmlspecialchars($staffMember['name']) ?></span>
                                                                                <span class="staff-title"><?= htmlspecialchars($staffMember['title'] ?? 'Module Leader') ?></span>
                                                                                <?php if (!empty($staffMember['expertise'])): ?>
                                                                                    <span class="staff-expertise"><?= htmlspecialchars($staffMember['expertise']) ?></span>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="module-staff">
                                                                    <div class="staff-card">
                                                                        <div class="staff-avatar">
                                                                            <i class="fas fa-user"></i>
                                                                        </div>
                                                                        <div class="staff-info">
                                                                            <span class="staff-name">To Be Assigned</span>
                                                                            <span class="staff-title">Module Leader</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($module['prerequisites'])): ?>
                                                                <div class="module-prerequisites">
                                                                    <i class="fas fa-link"></i>
                                                                    <span>Prerequisites: <?= htmlspecialchars(implode(', ', array_column($module['prerequisites'], 'title'))) ?></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-module-details" onclick="showModuleDetails(<?= htmlspecialchars(json_encode($module)) ?>)">
                                                        <i class="fas fa-info-circle"></i> View Details
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="programme-sidebar">
                    <!-- Teaching Staff Section -->
                    <div class="staff-section">
                        <h2>Teaching Staff</h2>
                        <div class="staff-list">
                            <?php if (empty($staffMembers)): ?>
                                <p>No staff information available yet.</p>
                            <?php else: ?>
                                <?php foreach ($staffMembers as $staff): ?>
                            <div class="staff-card">
                                <div class="staff-avatar">
                                    <?php if (!empty($staff['avatar_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($staff['avatar_url']); ?>" alt="<?php echo htmlspecialchars($staff['name']); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="staff-info">
                                    <div class="staff-name"><?php echo htmlspecialchars($staff['name']); ?></div>
                                    <div class="staff-role"><?php echo htmlspecialchars($staff['role']); ?></div>
                                </div>
                            </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Module Detail Modal -->
            <div class="modal" id="moduleDetailModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title"></h3>
                        <button onclick="closeModuleModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="module-info">
                            <div class="module-detail-group">
                                <label>Description</label>
                                <p class="module-detail-description"></p>
                            </div>
                            <div class="module-detail-group">
                                <label>Credits</label>
                                <p class="module-detail-credits"></p>
                            </div>
                            <div class="module-detail-group">
                                <label>Teaching Staff</label>
                                <p class="module-detail-staff"></p>
                            </div>
                            <div class="module-detail-group shared-programmes-list">
                                <label>Shared with Programmes</label>
                                <ul class="shared-programmes"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function registerInterest(programmeId) {
            fetch(`/student-course-hub/api/programmes/${programmeId}/interest`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to register interest. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function withdrawInterest(programmeId) {
            if (!confirm('Are you sure you want to withdraw your interest in this programme?')) {
                return;
            }

            fetch(`/student-course-hub/api/programmes/${programmeId}/interest`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to withdraw interest. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        document.querySelectorAll('.module-card').forEach(card => {
            card.addEventListener('click', () => {
                const moduleData = JSON.parse(card.dataset.module);
                showModuleDetails(moduleData);
            });
        });

        function showModuleDetails(module) {
            const modal = document.getElementById('moduleDetailModal');
            modal.querySelector('.modal-title').textContent = module.title;
            modal.querySelector('.module-detail-description').textContent = module.description;
            modal.querySelector('.module-detail-credits').textContent = `${module.credits} Credits`;
            modal.querySelector('.module-detail-staff').textContent = module.staff_name || 'Not assigned';

            const sharedList = modal.querySelector('.shared-programmes');
            sharedList.innerHTML = '';
            
            if (module.shared_programmes && module.shared_programmes.length > 0) {
                module.shared_programmes.forEach(prog => {
                    const li = document.createElement('li');
                    li.textContent = prog.title;
                    sharedList.appendChild(li);
                });
                modal.querySelector('.shared-programmes-list').style.display = 'block';
            } else {
                modal.querySelector('.shared-programmes-list').style.display = 'none';
            }

            modal.style.display = 'block';
        }

        function closeModuleModal() {
            document.getElementById('moduleDetailModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            const modal = document.getElementById('moduleDetailModal');
            if (event.target === modal) {
                closeModuleModal();
            }
        });
    </script>
</body>
</html>
