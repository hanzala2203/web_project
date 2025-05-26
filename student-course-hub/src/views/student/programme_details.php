<?php
$pageTitle = "Programme Details: " . htmlspecialchars($programme['title']);
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
        }

        .programme-header {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
        }

        .programme-content {
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
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            cursor: pointer;
        }

        .module-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .module-name {
            font-weight: 500;
            color: #1e293b;
        }

        .module-credits {
            font-size: 0.75rem;
            color: #64748b;
            padding: 0.25rem 0.5rem;
            background: #e2e8f0;
            border-radius: 1rem;
            white-space: nowrap;
        }

        .module-description {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .module-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.75rem;
            color: #64748b;
        }

        .module-shared {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .programme-sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .staff-section {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .staff-section h2 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .staff-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .staff-card {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .staff-avatar {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
        }

        .staff-info {
            flex: 1;
        }

        .staff-name {
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .staff-role {
            font-size: 0.875rem;
            color: #64748b;
        }

        .interest-section {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            width: 100%;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--link-hover-color);
        }

        .btn-secondary {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: #f0f7ff;
        }

        /* Module Detail Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            position: relative;
            background: #fff;
            width: 90%;
            max-width: 600px;
            margin: 2rem auto;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.25rem;
            color: #1e293b;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
            line-height: 1;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .module-detail-group {
            margin-bottom: 1.5rem;
        }

        .module-detail-group:last-child {
            margin-bottom: 0;
        }

        .module-detail-group label {
            display: block;
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .shared-programmes {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .shared-programmes li {
            padding: 0.5rem;
            background: #f8fafc;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: #1e293b;
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
<body>
    <div class="container">
        <!-- Programme Header -->
        <div class="programme-header">
            <div class="programme-title">
                <h1><?php echo htmlspecialchars($programme['title']); ?></h1>
                <span class="programme-level"><?php echo htmlspecialchars($programme['level']); ?></span>
            </div>
            <div class="programme-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Duration: <?php echo htmlspecialchars($programme['duration']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-book"></i>
                    <span><?php echo count($programme['modules'] ?? []); ?> Modules</span>
                </div>
            </div>
            <div class="programme-description">
                <?php echo nl2br(htmlspecialchars($programme['description'])); ?>
            </div>
        </div>

        <div class="programme-content">
            <!-- Modules Section -->
            <div class="programme-modules">
                <h2>Programme Structure</h2>
                <?php foreach ($programme['modules'] as $year => $modules): ?>
                    <div class="year-modules">
                        <h3>Year <?php echo htmlspecialchars($year); ?></h3>
                        <div class="module-list">
                            <?php foreach ($modules as $module): ?>
                                <div class="module-card" data-module='<?php echo json_encode($module); ?>'>
                                    <div class="module-header">
                                        <span class="module-name"><?php echo htmlspecialchars($module['title']); ?></span>
                                        <span class="module-credits"><?php echo htmlspecialchars($module['credits']); ?> Credits</span>
                                    </div>
                                    <div class="module-description">
                                        <?php echo htmlspecialchars($module['description']); ?>
                                    </div>
                                    <div class="module-meta">
                                        <?php if (!empty($module['shared_programmes'])): ?>
                                            <div class="module-shared">
                                                <i class="fas fa-share-alt"></i>
                                                <span>Shared with <?php echo count($module['shared_programmes']); ?> other programmes</span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($module['staff_name'])): ?>
                                            <div class="module-staff">
                                                <i class="fas fa-user"></i>
                                                <span><?php echo htmlspecialchars($module['staff_name']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Sidebar -->
            <div class="programme-sidebar">
                <!-- Interest Registration -->
                <div class="interest-section">
                    <?php if (!isset($programme['interest_registered'])): ?>
                        <button class="btn btn-primary" onclick="registerInterest(<?php echo $programme['id']; ?>)">
                            <i class="far fa-bookmark"></i>
                            Register Interest
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary" onclick="withdrawInterest(<?php echo $programme['id']; ?>)">
                            <i class="fas fa-bookmark"></i>
                            Withdraw Interest
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Teaching Staff Section -->
                <div class="staff-section">
                    <h2>Teaching Staff</h2>
                    <div class="staff-list">
                        <?php foreach ($programme['staff'] as $staff): ?>
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
