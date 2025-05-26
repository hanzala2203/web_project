<?php
$pageTitle = "Available Courses";
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

        /* Courses Container */
        .courses-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Courses Header */
        .courses-header {
            margin-bottom: 2rem;
        }

        .courses-header h1 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .courses-header p {
            color: #64748b;
            font-size: 1.1rem;
            max-width: 800px;
        }

        /* Search and Filters */
        .search-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .search-filters input,
        .search-filters select {
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .search-filters input {
            flex: 1;
            min-width: 200px;
        }

        .search-filters select {
            width: 150px;
            background-color: #fff;
        }

        .search-filters input:focus,
        .search-filters select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Courses Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        /* Course Card */
        .course-card {
            background: #fff;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            border: 1px solid #e2e8f0;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .course-image {
            width: 100%;
            height: 160px;
            overflow: hidden;
            position: relative;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .course-card:hover .course-image img {
            transform: scale(1.05);
        }

        .course-info {
            padding: 1.5rem;
        }

        .course-info h3 {
            font-size: 1.25rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .course-description {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }

        /* Course Meta */
        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            color: #64748b;
            padding: 0.5rem 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .course-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .course-meta i {
            color: var(--primary-color);
        }

        /* Course Actions */
        .course-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-weight: 500;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-enroll {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-enroll:hover {
            background-color: var(--link-hover-color);
        }

        .btn-view {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .btn-view:hover {
            background-color: #cbd5e1;
        }

        .btn-enrolled {
            background-color: var(--secondary-color);
            color: #fff;
            cursor: not-allowed;
            opacity: 0.8;
        }

        /* Empty State */
        .no-courses {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            color: #64748b;
            border: 2px dashed #e2e8f0;
        }

        .no-courses i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #94a3b8;
        }

        .no-courses h3 {
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .search-filters {
                flex-direction: column;
            }

            .search-filters input,
            .search-filters select {
                width: 100%;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }

            .courses-container {
                padding: 1rem;
            }

            .course-card {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .courses-header h1 {
                font-size: 1.5rem;
            }

            .course-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
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

            .course-card {
                border: 2px solid #000;
            }

            .btn {
                border: 2px solid currentColor;
            }

            .course-meta {
                border-color: #000;
            }
        }
    </style>
</head>
<body>
    <div class="courses-container">
        <div class="courses-header">
            <h1>Available Courses</h1>
            <div class="search-filters">
                <input type="text" id="courseSearch" placeholder="Search courses...">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="courses-grid">
            <?php if (!empty($courses)): ?>
                <?php foreach($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-image">
                            <img src="<?= htmlspecialchars($course['image_url']) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                        </div>
                        <div class="course-info">
                            <h3><?= htmlspecialchars($course['title']) ?></h3>
                            <p class="course-description"><?= htmlspecialchars($course['description']) ?></p>
                            <div class="course-meta">
                                <span class="duration"><i class="fas fa-clock"></i> <?= htmlspecialchars($course['duration']) ?></span>
                                <span class="instructor"><i class="fas fa-user"></i> <?= htmlspecialchars($course['instructor']) ?></span>
                            </div>
                            <div class="course-actions">
                                <?php if (!$course['is_enrolled']): ?>
                                    <form action="/student-course-hub/course/enroll" method="POST">
                                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                        <button type="submit" class="btn-enroll">Enroll Now</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-enrolled" disabled>Enrolled</button>
                                    <a href="/student-course-hub/course/view/<?= $course['id'] ?>" class="btn-view">View Course</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-courses">
                    <p>No courses available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('courseSearch').addEventListener('input', function(e) {
            // Add your search functionality here
        });

        document.getElementById('categoryFilter').addEventListener('change', function(e) {
            // Add your filter functionality here
        });
    </script>
</body>
</html>