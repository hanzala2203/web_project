<?php
$pageTitle = 'My Interested Programmes';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <?php require_once __DIR__ . '/../layouts/student_sidebar_new.php'; ?>
    
    <main class="lg:ml-64 p-4 sm:p-6 lg:p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Interested Programmes</h1>
            <a href="<?= BASE_URL ?>/student/explore_programmes" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition duration-150">
                <i class="fas fa-search mr-2"></i>
                Explore More Programmes
            </a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">            <?php 
            require_once __DIR__ . '/../../controllers/InterestController.php';
            $controller = new InterestController();
            $interests = $controller->getStudentInterests($_SESSION['user_id']);
            
            if (!empty($interests)): 
                foreach ($interests as $programme): 
            ?>
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition duration-150 overflow-hidden">
                    <div class="relative h-48">
                        <img src="<?= htmlspecialchars($programme['image_url'] ?? '/assets/images/default-programme.jpg') ?>" 
                             alt="<?= htmlspecialchars($programme['title']) ?>"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-lg font-semibold text-white">
                                <?= htmlspecialchars($programme['title']) ?>
                            </h3>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <i class="fas fa-clock mr-2"></i>
                            <?= htmlspecialchars($programme['duration'] ?? 'Full-time') ?>
                            <span class="mx-2">•</span>
                            <i class="fas fa-graduation-cap mr-2"></i>
                            <?= ucfirst(htmlspecialchars($programme['level'] ?? 'Degree')) ?>
                        </div>
                        <div class="flex justify-between items-center">
                            <a href="<?= BASE_URL ?>/student/programme_details?id=<?= $programme['id'] ?>" 
                               class="text-indigo-600 hover:text-indigo-700 font-medium">
                                View Details
                            </a>
                            <form action="<?= BASE_URL ?>/student/withdraw_interest" method="POST" class="inline">
                                <input type="hidden" name="programme_id" value="<?= htmlspecialchars($programme['id']) ?>">
                                <button type="submit" 
                                        class="text-red-500 hover:text-red-600 font-medium flex items-center">
                                    <i class="fas fa-minus-circle mr-1"></i>
                                    Withdraw Interest
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach; 
            else: 
            ?>
                <div class="col-span-full text-center py-12">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <i class="fas fa-bookmark text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Interested Programmes Yet</h3>
                        <p class="text-gray-500 mb-4">
                            You haven't registered interest in any programmes yet. 
                            Start exploring to find programmes that interest you!
                        </p>
                        <a href="<?= BASE_URL ?>/student/explore_programmes" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition duration-150">
                            <i class="fas fa-search mr-2"></i>
                            Explore Programmes
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
