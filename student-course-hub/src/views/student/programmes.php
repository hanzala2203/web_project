<?php
$pageTitle = "Browse Programmes";
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
        /* Reset and base styles */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        :root {
            --primary-color: #3b82f6;
            --secondary-color: #4CAF50;
            --error-color: #ef4444;
            --link-color: #2563eb;
            --link-hover-color: #1d4ed8;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Accessibility improvements */
        *:focus {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
        }

        /* Container */
        .programmes-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header Section */
        .programmes-header {
            background: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .programmes-header h1 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .programmes-header p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 800px;
        }

        /* Search and Filters */
        .search-filters {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 1rem;
            align-items: start;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            background-color: #fff;
            min-width: 150px;
        }

        /* Programmes Grid */
        .programmes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Programme Card */
        .programme-card {
            background: #fff;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .programme-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .programme-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .programme-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .programme-card:hover .programme-image img {
            transform: scale(1.05);
        }

        .programme-level {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #fff;
        }

        .programme-level.undergraduate {
            background-color: var(--primary-color);
        }

        .programme-level.postgraduate {
            background-color: #8b5cf6;
        }

        .programme-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .programme-title {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .programme-description {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .programme-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            font-size: 0.875rem;
            color: #64748b;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }

        .meta-item i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .programme-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .feature-badge {
            padding: 0.25rem 0.75rem;
            background: #f0f9ff;
            color: #0369a1;
            border-radius: 1rem;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .programme-actions {
            padding: 1.5rem;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 1rem;
        }

        .interest-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: var(--link-hover-color);
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        .btn-secondary.active {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .year-modules {
            margin-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5rem;
        }

        .year-title {
            font-size: 1rem;
            color: #1e293b;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .modules-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .module-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
        }

        .module-info {
            flex: 1;
        }

        .module-name {
            font-size: 0.875rem;
            color: #1e293b;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .module-staff {
            font-size: 0.75rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-credits {
            font-size: 0.75rem;
            color: #64748b;
            padding: 0.25rem 0.5rem;
            background: #e2e8f0;
            border-radius: 1rem;
            white-space: nowrap;
        }

        /* Interest Modal */
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
            background: #fff;
            max-width: 500px;
            margin: 4rem auto;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .modal-title {
            font-size: 1.25rem;
            color: #1e293b;
            margin: 0;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-text {
            color: #64748b;
            margin-bottom: 1rem;
        }

        .modal-actions {
            padding: 1.5rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .empty-state i {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .programmes-container {
                padding: 1rem;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .programmes-grid {
                grid-template-columns: 1fr;
            }

            .programme-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .programme-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        /* High Contrast Support */
        @media (prefers-contrast: high) {
            :root {
                --primary-color: #000;
                --secondary-color: #000;
                --link-color: #000;
                --link-hover-color: #333;
            }

            .programme-card {
                border: 2px solid #000;
            }

            .programme-meta {
                border-color: #000;
            }

            .module-item {
                border: 2px solid #000;
            }

            .programme-level {
                border: 2px solid #000;
            }
        }

        /* Loading State */
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
            backdrop-filter: blur(4px);
        }

        .loading i {
            font-size: 2rem;
            color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Shared Modules Badge */
        .shared-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: #f0f9ff;
            color: #0369a1;
            border-radius: 1rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="programmes-container">
        <!-- Header Section -->
        <div class="programmes-header">
            <h1>Explore Our Programmes</h1>
            <p>Discover undergraduate and postgraduate programmes designed to help you achieve your academic and career goals.</p>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters">
            <div class="filters-grid">                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" id="searchProgrammes" placeholder="Search by programme name, keywords, or descriptions...">
                </div>
                <div class="filter-group">
                    <select class="filter-select" id="levelFilter">
                        <option value="">All Levels</option>
                        <option value="undergraduate">Undergraduate</option>
                        <option value="postgraduate">Postgraduate</option>
                    </select>
                    <select class="filter-select" id="durationFilter">
                        <option value="">All Durations</option>
                        <option value="1 year">1 Year</option>
                        <option value="2 years">2 Years</option>
                        <option value="3 years">3 Years</option>
                        <option value="4 years">4 Years</option>
                    </select>
                    <select class="filter-select" id="departmentFilter">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div class="loading" id="loadingState">
            <i class="fas fa-spinner"></i>
        </div>

        <!-- Programmes Grid -->
        <div class="programmes-grid" id="programmesGrid">
            <?php if (!empty($programmes)): ?>
                <?php foreach($programmes as $programme): ?>
                    <div class="programme-card">
                        <div class="programme-image">
                            <?php if (!empty($programme['image_url'])): ?>
                                <img src="<?= htmlspecialchars($programme['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($programme['title']) ?> - Programme Image">
                            <?php else: ?>
                                <img src="/student-course-hub/public/assets/images/default-programme.jpg" 
                                     alt="Default Programme Image">
                            <?php endif; ?>
                            <span class="programme-level <?= htmlspecialchars(strtolower($programme['level'])) ?>">
                                <?= htmlspecialchars($programme['level']) ?>
                            </span>
                        </div>
                        <div class="programme-content">
                            <h3 class="programme-title"><?= htmlspecialchars($programme['title']) ?></h3>
                            <p class="programme-description"><?= htmlspecialchars($programme['description']) ?></p>                        <div class="programme-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Duration: <?= htmlspecialchars($programme['duration']) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-book"></i>
                                    <span><?= count($programme['modules'] ?? []) ?> Modules</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span><?= htmlspecialchars(ucfirst($programme['level'])) ?></span>
                                </div>
                                <?php if (!empty($programme['department'])): ?>
                                <div class="meta-item">
                                    <i class="fas fa-university"></i>
                                    <span><?= htmlspecialchars($programme['department']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($programme['key_features'])): ?>
                            <div class="programme-features">
                                <?php foreach ($programme['key_features'] as $feature): ?>
                                <span class="feature-badge"><?= htmlspecialchars($feature) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="programme-actions">
                            <a href="/student-course-hub/programmes/<?= $programme['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-info-circle"></i> View Details
                            </a>
                            <div class="interest-actions">
                                <?php if (!isset($programme['interest_registered'])): ?>
                                    <button class="btn btn-secondary" onclick="registerInterest(<?= $programme['id'] ?>)">
                                        <i class="far fa-bookmark"></i> Register Interest
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary active" onclick="withdrawInterest(<?= $programme['id'] ?>)">
                                        <i class="fas fa-bookmark"></i> Registered
                                        <span class="interest-count"><?= $programme['interest_count'] ?? 0 ?> interested</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>No Programmes Found</h3>
                    <p>Try adjusting your search or filters to find available programmes.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Interest Registration Modal -->
    <div class="modal" id="interestModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Register Interest</h3>
            </div>
            <div class="modal-body">
                <p class="modal-text">Would you like to receive updates and further details about this programme?</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="confirmInterest()">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        let selectedProgrammeId = null;

        // Search and filter functionality
        document.getElementById('searchProgrammes').addEventListener('input', filterProgrammes);
        document.getElementById('levelFilter').addEventListener('change', filterProgrammes);
        document.getElementById('durationFilter').addEventListener('change', filterProgrammes);
        document.getElementById('departmentFilter').addEventListener('change', filterProgrammes);

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function filterProgrammes() {
            const searchTerm = document.getElementById('searchProgrammes').value.toLowerCase();
            const levelFilter = document.getElementById('levelFilter').value;
            const durationFilter = document.getElementById('durationFilter').value;
            const departmentFilter = document.getElementById('departmentFilter').value;
            
            // Show loading state
            document.getElementById('loadingState').style.display = 'block';
            
            // Fetch filtered results from server
            const params = new URLSearchParams({
                search: searchTerm,
                level: levelFilter,
                duration: durationFilter,
                department: departmentFilter
            });

            fetch(`/student-course-hub/api/programmes/filter?${params}`)
                .then(response => response.json())
                .then(data => {
                    const grid = document.getElementById('programmesGrid');
                    grid.innerHTML = '';

                    if (data.programmes.length === 0) {
                        grid.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-graduation-cap"></i>
                                <h3>No Programmes Found</h3>
                                <p>Try adjusting your search or filters to find available programmes.</p>
                            </div>
                        `;
                    } else {
                        data.programmes.forEach(programme => {
                            grid.appendChild(createProgrammeCard(programme));
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    document.getElementById('loadingState').style.display = 'none';
                });
        }

        const debouncedFilter = debounce(filterProgrammes, 300);
            
            // Hide loading state after brief delay
            setTimeout(() => {
                document.getElementById('loadingState').style.display = 'none';
            }, 500);
        }

        // Interest registration functionality
        function registerInterest(programmeId) {
            selectedProgrammeId = programmeId;
            document.getElementById('interestModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('interestModal').style.display = 'none';
            selectedProgrammeId = null;
        }

        function confirmInterest() {
            if (selectedProgrammeId) {
                fetch(`/student-course-hub/api/programmes/${selectedProgrammeId}/interest`, {
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
            closeModal();
        }

        function withdrawInterest(programmeId) {
            if (confirm('Are you sure you want to withdraw your interest in this programme?')) {
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
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('interestModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        function createProgrammeCard(programme) {
            const card = document.createElement('div');
            card.className = 'programme-card';
            
            card.innerHTML = `
                <div class="programme-image">
                    ${programme.image_url ? 
                        `<img src="${programme.image_url}" alt="${programme.title} - Programme Image">` :
                        `<img src="/student-course-hub/public/assets/images/default-programme.jpg" alt="Default Programme Image">`
                    }
                    <span class="programme-level ${programme.level.toLowerCase()}">${programme.level}</span>
                </div>
                <div class="programme-content">
                    <h3 class="programme-title">${programme.title}</h3>
                    <p class="programme-description">${programme.description}</p>
                    <div class="programme-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Duration: ${programme.duration}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-book"></i>
                            <span>${programme.module_count} Modules</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-university"></i>
                            <span>${programme.department || 'General'}</span>
                        </div>
                    </div>
                    ${programme.key_features ? `
                        <div class="programme-features">
                            ${programme.key_features.map(feature => `
                                <span class="feature-badge">${feature}</span>
                            `).join('')}
                        </div>
                    ` : ''}
                </div>
                <div class="programme-actions">
                    <a href="/student-course-hub/programmes/${programme.id}" class="btn btn-primary">
                        <i class="fas fa-info-circle"></i> View Details
                    </a>
                    <div class="interest-actions">
                        ${programme.interest_registered ?
                            `<button class="btn btn-secondary active" onclick="withdrawInterest(${programme.id})">
                                <i class="fas fa-bookmark"></i> Registered
                                <span class="interest-count">${programme.interest_count} interested</span>
                            </button>` :
                            `<button class="btn btn-secondary" onclick="registerInterest(${programme.id})">
                                <i class="far fa-bookmark"></i> Register Interest
                            </button>`
                        }
                    </div>
                </div>
            `;
            
            return card;
        }
    </script>
</body>
</html>
