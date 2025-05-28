<?php
require_once __DIR__ . '/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Error</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo htmlspecialchars($_SESSION['error']); 
                            unset($_SESSION['error']); // Clear the error message
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            An unexpected error occurred. Please try again later.
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary">Return to Home</a>
                        <button onclick="window.history.back()" class="btn btn-secondary">Go Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
