<?php
// Variables that should be available:
// $programme - Programme details
// $structure - Programme structure by year/semester
// $totalCredits - Total programme credits
// $staffMembers - Programme staff members
// $hasInterest - Whether current student has registered interest
// $studentId - Current student ID if logged in

if (!isset($programme)) {
    header('Location: ' . BASE_URL . '/error');
    exit;
}

$programmeId = $programme['id'];
$pageTitle = "Programme Details: " . htmlspecialchars($programme['title']);

require_once BASE_PATH . '/src/views/layouts/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Course Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <?php require_once '../layouts/student_sidebar_new.php'; ?>

        <main class="lg:pl-72">
            <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
                <?php if (isset($error)): ?>
                <div class="rounded-md bg-red-50 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <p class="text-sm text-red-700 mt-2"><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                </div>
                <?php else: ?>

                <!-- Programme Header -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
                    <div class="h-48 w-full bg-gradient-to-r from-blue-500 to-blue-600 relative">
                        <?php if (!empty($programme['image_url'])): ?>
                        <img src="<?= htmlspecialchars($programme['image_url']) ?>" 
                             alt="<?= htmlspecialchars($programme['title']) ?>"
                             class="w-full h-full object-cover mix-blend-overlay">
                        <?php endif; ?>
                        <span class="absolute top-4 right-4 px-4 py-1 rounded-full text-sm font-semibold uppercase
                                   <?= strtolower($programme['level']) === 'undergraduate' ? 'bg-blue-500' : 'bg-purple-500' ?> text-white">
                            <?= htmlspecialchars($programme['level']) ?>
                        </span>
                    </div>                        <div class="p-6">
                        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                                    <?= htmlspecialchars($programme['title']) ?>
                                </h1>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-graduation-cap text-blue-500 mr-1"></i>
                                        <?= htmlspecialchars(ucfirst($programme['level'])) ?>
                                    </span>
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-university text-blue-500 mx-2"></i>
                                        <?= htmlspecialchars($programme['department'] ?? 'All Departments') ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <?php if (!empty($programme['key_features'])): ?>
                                    <?php foreach ($programme['key_features'] as $feature): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700">
                                            <?= htmlspecialchars($feature) ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt text-blue-500 mr-3 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Duration</p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($programme['duration_years'] ?? '3') ?> Years</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-book text-blue-500 mr-3 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Total Modules</p>
                                    <p class="text-sm text-gray-600"><?= $programme['module_count'] ?? 0 ?> Modules</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-graduation-cap text-blue-500 mr-3 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Total Credits</p>
                                    <p class="text-sm text-gray-600"><?= $totalCredits ?? 0 ?> Credits</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-500 mr-3 text-xl"></i>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Teaching Staff</p>
                                    <p class="text-sm text-gray-600"><?= count($staffMembers ?? []) ?> Members</p>
                                </div>
                            </div>
                        </div>

                        <div class="prose max-w-none mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-3">About the Programme</h2>
                            <div class="text-gray-600 space-y-4">
                                <?= nl2br(htmlspecialchars($programme['description'] ?? 'No description available')) ?>
                            </div>
                        </div>

                        <?php if (!empty($programme['career_prospects'])): ?>
                        <div class="prose max-w-none mb-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-3">Career Prospects</h2>
                            <div class="text-gray-600 space-y-4">
                                <?= nl2br(htmlspecialchars($programme['career_prospects'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flex gap-3">
                            <?php if ($studentId): ?>
                                <?php if ($hasInterest): ?>
                                    <button type="button" onclick="withdrawInterest(<?= $programmeId ?>)"
                                            class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-bookmark mr-2"></i>
                                        Remove Interest
                                    </button>
                                <?php else: ?>
                                    <button type="button" onclick="registerInterest(<?= $programmeId ?>)"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="far fa-bookmark mr-2"></i>
                                        Register Interest
                                    </button>
                                <?php endif; ?>
                                <a href="manage_interests.php" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-cog mr-2"></i>
                                    Manage Interests
                                </a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/auth/login?redirect=<?= BASE_URL ?>/student/programme_details?id=<?= $programmeId ?>" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    Login to Register Interest
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content: Programme Structure -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="border-b border-gray-200">
                                <nav class="px-4" aria-label="Tabs">
                                    <?php if (!empty($structure)): ?>
                                        <div class="flex space-x-4 overflow-x-auto py-4" role="tablist">
                                            <?php foreach ($structure as $yearNum => $yearData): ?>
                                                <button type="button"
                                                        onclick="showYear(<?= $yearNum ?>)"
                                                        class="year-tab whitespace-nowrap px-4 py-2 rounded-md text-sm font-medium <?= $yearNum === array_key_first($structure) ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' ?>"
                                                        role="tab">
                                                    Year <?= $yearNum ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </nav>
                            </div>

                            <div class="p-6">
                                <?php if (empty($structure)): ?>
                                    <div class="text-center py-12">
                                        <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-600">No structure information available for this programme yet.</p>
                                    </div>
                                <?php else: foreach ($structure as $yearNum => $yearData): ?>
                                    <div class="year-content <?= $yearNum === array_key_first($structure) ? 'block' : 'hidden' ?>" id="year-<?= $yearNum ?>">
                                        <?php foreach ($yearData as $semesterNum => $modules): ?>
                                            <div class="mb-8">
                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Semester <?= $semesterNum ?></h3>
                                                <div class="space-y-4">
                                                    <?php foreach ($modules as $module): ?>
                                                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition duration-150 ease-in-out">
                                                            <div class="flex justify-between items-start">
                                                                <div>
                                                                    <h4 class="text-base font-medium text-gray-900"><?= htmlspecialchars($module['title']) ?></h4>
                                                                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($module['code'] ?? 'N/A') ?></p>
                                                                </div>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                    <?= $module['credits'] ?> Credits
                                                                </span>
                                                            </div>
                                                            <p class="mt-2 text-sm text-gray-600"><?= htmlspecialchars($module['description'] ?? 'No description available') ?></p>
                                                            <?php if (!empty($module['prerequisites'])): ?>
                                                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                                                    <i class="fas fa-link mr-2"></i>
                                                                    Prerequisites: <?= htmlspecialchars(implode(', ', array_column($module['prerequisites'], 'title'))) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($module['staff'])): ?>
                                                                <div class="mt-4 flex items-center">
                                                                    <div class="flex-shrink-0 h-8 w-8">
                                                                        <?php if (!empty($module['staff']['avatar_url'])): ?>
                                                                            <img class="h-8 w-8 rounded-full" src="<?= htmlspecialchars($module['staff']['avatar_url']) ?>" alt="">
                                                                        <?php else: ?>
                                                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                                                <i class="fas fa-user text-gray-400"></i>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($module['staff']['name']) ?></p>
                                                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($module['staff']['role'] ?? 'Module Leader') ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar: Teaching Staff -->
                    <div class="space-y-6">
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Teaching Staff</h2>
                                <?php if (empty($staffMembers)): ?>
                                    <p class="text-gray-600">No staff information available yet.</p>
                                <?php else: ?>
                                    <div class="space-y-4">
                                        <?php foreach ($staffMembers as $staff): ?>
                                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <?php if (!empty($staff['avatar_url'])): ?>
                                                        <img class="h-12 w-12 rounded-full" src="<?= htmlspecialchars($staff['avatar_url']) ?>" alt="">
                                                    <?php else: ?>
                                                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-400"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($staff['name']) ?></h4>
                                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($staff['role']) ?></p>
                                                    <?php if (!empty($staff['expertise'])): ?>
                                                        <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($staff['expertise']) ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($programme['key_features'])): ?>
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Key Features</h2>
                                <div class="space-y-3">
                                    <?php foreach ($programme['key_features'] as $feature): ?>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                            <?= htmlspecialchars($feature) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    function showYear(yearNum) {
        // Hide all year content
        document.querySelectorAll('.year-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show selected year content
        document.getElementById(`year-${yearNum}`).classList.remove('hidden');
        
        // Update tab styles
        document.querySelectorAll('.year-tab').forEach(tab => {
            tab.classList.remove('bg-blue-100', 'text-blue-700');
            tab.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-100');
        });
        event.target.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:bg-gray-100');
        event.target.classList.add('bg-blue-100', 'text-blue-700');
    }

    async function registerInterest(programmeId) {
        try {
            const response = await fetch(`${BASE_URL}/student/register_interest.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ programme_id: programmeId })
            });
            
            if (response.ok) {
                location.reload();
            } else {
                throw new Error('Failed to register interest');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to register interest. Please try again later.');
        }
    }

    async function withdrawInterest(programmeId) {
        if (!confirm('Are you sure you want to withdraw your interest from this programme?')) {
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/student/withdraw_interest.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ programme_id: programmeId })
            });
            
            if (response.ok) {
                location.reload();
            } else {
                throw new Error('Failed to withdraw interest');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to withdraw interest. Please try again later.');
        }
    }
    </script>
</body>
</html>
