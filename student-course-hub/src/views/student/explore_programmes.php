<?php
// Data should be passed from the controller
// Set defaults for all expected variables
$programmes = $programmes ?? [];
$interests = $interests ?? [];
$filters = $_GET ?? [];
?>

<?php
$pageTitle = "Explore Programmes";
require_once __DIR__ . '/../layouts/header.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/student.css">

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <style>
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
            --bg-color: #f1f5f9;
            --card-color: #ffffff;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            background-color: var(--bg-color);
            color: #1e293b;
            min-height: 100vh;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        header {
            margin-bottom: 2rem;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        header p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Search and Filter Section */
        .search-filter {
            background: var(--card-color);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .search-filter form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-filter .form-group {
            flex: 1;
            min-width: 200px;
        }

        .search-filter label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #1e293b;
        }

        .search-filter input, 
        .search-filter select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 1rem;
            color: #1e293b;
        }

        .search-filter button {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.2s;
            align-self: flex-end;
        }

        .search-filter button:hover {
            background-color: var(--link-hover-color);
        }

        /* Programmes Grid */
        .programmes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .programme-card {
            background-color: var(--card-color);
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .programme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .programme-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: #e2e8f0;
            position: relative;
        }

        .programme-level {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .programme-content {
            padding: 1.5rem;
        }

        .programme-title {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .programme-description {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }        .programme-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background: #f8fafc;
            transition: background-color 0.2s;
        }

        .meta-item:hover {
            background: #f1f5f9;
        }

        .meta-item span:first-of-type {
            color: #1e293b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .meta-item .meta-label {
            color: #64748b;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .meta-item i {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
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
            margin-top: 1rem;
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
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            background-color: var(--link-hover-color);
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #1e293b;
            flex: 1;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 3rem;
            background-color: var(--card-color);
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .no-results i {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .no-results h3 {
            color: #1e293b;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .no-results p {
            color: #64748b;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .programmes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container">
        <header>
            <h1>Explore Study Programmes</h1>
            <p>Discover undergraduate and postgraduate programmes to advance your academic journey and career prospects</p>
        </header>

        <div class="search-filter">
            <form action="" method="GET">
                <div class="form-group">
                    <label for="query">Search Programmes</label>
                    <input type="text" id="query" name="query" placeholder="e.g., Computer Science, Cyber Security" value="<?= htmlspecialchars($query ?? '') ?>">
                </div>                <div class="form-group">
                    <label for="level">Programme Level</label>
                    <select id="level" name="level">
                        <option value="">All Levels</option>
                        <option value="undergraduate" <?= $level === 'undergraduate' ? 'selected' : '' ?>>Undergraduate</option>
                        <option value="postgraduate" <?= $level === 'postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <select id="duration" name="duration">
                        <option value="">Any Duration</option>
                        <option value="1">1 Year</option>
                        <option value="2">2 Years</option>
                        <option value="3">3 Years</option>
                        <option value="4">4 Years</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department">
                        <option value="">All Departments</option>
                        <?php foreach ($result['departments'] as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>

        <?php if (empty($programmes)): ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No programmes found</h3>
                <p>Try adjusting your search criteria or explore all available programmes</p>
            </div>
        <?php else: ?>
            <div class="programmes-grid">
                <?php foreach ($programmes as $programme): ?>
                    <div class="programme-card">
                        <div class="programme-image" style="background-image: url('<?= htmlspecialchars($programme['image_url'] ?? '/assets/images/default-programme.jpg') ?>')">
                            <span class="programme-level">
                                <?= ucfirst(htmlspecialchars($programme['level'] ?? 'undergraduate')) ?>
                            </span>
                        </div>
                        <div class="programme-content">
                            <h3 class="programme-title"><?= htmlspecialchars($programme['title']) ?></h3>
                            <p class="programme-description">
                                <?= htmlspecialchars($programme['description'] ?? 'No description available') ?>
                            </p>                            <div class="programme-meta">
                                <div class="meta-item">
                                    <i class="fas fa-book"></i>
                                    <span><?= ($programme['module_count'] ?? 0) ?> Modules</span>
                                    <span class="meta-label">Total Modules</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?= htmlspecialchars($programme['duration_years'] ?? '3') ?> Years</span>
                                    <span class="meta-label">Duration</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span><?= htmlspecialchars($programme['qualification'] ?? 'Degree') ?></span>
                                    <span class="meta-label">Qualification</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users"></i>
                                    <span><?= ($programme['staff_count'] ?? 0) ?> Staff</span>
                                    <span class="meta-label">Teaching Staff</span>
                                </div>
                                <?php if (!empty($programme['department'])): ?>
                                <div class="meta-item">
                                    <i class="fas fa-university"></i>
                                    <span><?= htmlspecialchars($programme['department']) ?></span>
                                    <span class="meta-label">Department</span>
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
                            <div class="programme-actions">                            <a href="<?= BASE_URL ?>/student/programme_details?id=<?= $programme['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-info-circle"></i> View Details
                            </a>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if (!isset($programme['interest_registered'])): ?>
                                    <a href="<?= BASE_URL ?>/student/register_interest.php?id=<?= $programme['id'] ?>" class="btn btn-secondary">
                                        <i class="far fa-bookmark"></i> Register Interest
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/student/manage_interests.php" class="btn btn-secondary active">
                                        <i class="fas fa-bookmark"></i> Registered
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
    </div>
</div>
</body>
</html>
