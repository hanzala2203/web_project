<?php
require_once __DIR__ . '/../../controllers/StudentController.php';
use App\Controllers\StudentController;

// Check for session or redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$studentId = $_SESSION['user_id'];
$studentController = new StudentController();

try {
    // Fetch interests using the proper controller method
    $interests = $studentController->getInterests($studentId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = "Manage Programme Interests";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Student Course Hub</title>
    <!-- Load Tailwind directly -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">

<div class="flex">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 hidden lg:block lg:w-72 bg-gray-900 text-white border-r border-gray-800">
        <div class="flex flex-col h-full">
            <!-- Logo section -->
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <span class="text-xl font-semibold">Student Course Hub</span>
            </div>
            <!-- Navigation -->
            <nav class="flex-1 p-4">
                <ul class="space-y-1">
                    <li>
                        <a href="<?= BASE_URL ?>/student/dashboard" 
                           class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-md">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/student/explore_programmes" 
                           class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-md">
                            <i class="fas fa-search"></i>
                            <span>Explore Programmes</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/student/manage_interests" 
                           class="flex items-center gap-3 px-4 py-2 bg-gray-800 text-white rounded-md">
                            <i class="fas fa-star"></i>
                            <span>My Interests</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- Logout button -->
            <div class="p-4 border-t border-gray-800">
                <a href="<?= BASE_URL ?>/logout" 
                   class="flex items-center gap-3 px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-md">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 lg:pl-72">
        <div class="px-4 py-8 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8 sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">My Programme Interests</h1>
                    <p class="mt-2 text-sm text-gray-600">View and manage your registered interests</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="<?= BASE_URL ?>/student/explore_programmes" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Explore Programmes
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 rounded-md bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                <?= htmlspecialchars($_SESSION['success']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Content -->
            <?php if (empty($interests)): ?>
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <i class="fas fa-bookmark text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Registered Interests Yet</h3>
                    <p class="text-gray-500 mb-4">
                        You haven't registered interest in any programmes yet.<br>
                        Start exploring to find programmes that interest you!
                    </p>
                    <a href="<?= BASE_URL ?>/student/explore_programmes" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Explore Programmes
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($interests as $programme): ?>
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg transition-shadow duration-200 hover:shadow-md">
                            <div class="relative h-48">
                                <img class="w-full h-full object-cover" 
                                     src="<?= htmlspecialchars($programme['image_url'] ?? BASE_URL . '/public/assets/images/default-programme.jpg') ?>" 
                                     alt="<?= htmlspecialchars($programme['title']) ?>">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <h3 class="text-lg font-semibold text-white line-clamp-2">
                                        <?= htmlspecialchars($programme['title']) ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center text-sm text-gray-500 mb-4">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span><?= htmlspecialchars($programme['duration'] ?? 'Full-time') ?></span>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-graduation-cap mr-2"></i>
                                    <span><?= ucfirst(htmlspecialchars($programme['level'] ?? 'Degree')) ?></span>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <a href="<?= BASE_URL ?>/student/programme_details?id=<?= $programme['id'] ?>" 
                                       class="text-indigo-600 hover:text-indigo-900 font-medium transition-colors duration-200">
                                        View Details
                                    </a>
                                    <form action="<?= BASE_URL ?>/student/withdraw_interest" method="POST" class="inline">
                                        <input type="hidden" name="programme_id" value="<?= htmlspecialchars($programme['id']) ?>">
                                        <button type="submit" 
                                                onclick="return confirm('Are you sure you want to withdraw your interest from this programme?');"
                                                class="inline-flex items-center text-red-600 hover:text-red-900 font-medium transition-colors duration-200">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Withdraw Interest
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
