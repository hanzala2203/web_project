<?php require_once '../layouts/header.php'; ?>

<div class="courses-container">
    <div class="courses-header">
        <h1>Available Courses</h1>
        <div class="search-filters">
            <input type="text" id="courseSearch" placeholder="Search courses...">
            <select id="categoryFilter">
                <option value="">All Categories</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
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
                            <span class="duration"><i class="fas fa-clock"></i> <?= $course['duration'] ?></span>
                            <span class="instructor"><i class="fas fa-user"></i> <?= htmlspecialchars($course['instructor']) ?></span>
                        </div>
                        <div class="course-actions">
                            <?php if (!$course['is_enrolled']): ?>
                                <form action="/course/enroll" method="POST">
                                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                    <button type="submit" class="btn-enroll">Enroll Now</button>
                                </form>
                            <?php else: ?>
                                <button class="btn-enrolled" disabled>Enrolled</button>
                                <a href="/course/view/<?= $course['id'] ?>" class="btn-view">View Course</a>
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

<?php require_once '../layouts/footer.php'; ?>