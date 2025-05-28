<?php
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-1 fw-bold">404</h1>
            <h2 class="mb-4">Page Not Found</h2>
            <p class="lead mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <div>
                <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Return to Home</a>
                <button onclick="window.history.back()" class="btn btn-secondary">Go Back</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
