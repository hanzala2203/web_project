<?php
$pageTitle = htmlspecialchars($programme['title'] ?? 'Programme Details');
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Load Tailwind and FontAwesome -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="min-h-screen bg-gray-50">
    <?php require_once __DIR__ . '/../layouts/student_sidebar_new.php'; ?>
      <main class="lg:ml-64 p-4 sm:p-6 lg:p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="<?= BASE_URL ?>/student/explore_programmes" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Programmes
            </a>
        </div>
        
        <div class="programme-details">
        <!-- Programme Header -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="relative h-80">
                <img src="<?= htmlspecialchars($programme['image_url']) ?>" alt="<?= htmlspecialchars($programme['title']) ?>" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-black/20 flex items-end">
                    <div class="p-8 text-white w-full">
                        <h1 class="text-4xl font-bold mb-3"><?= htmlspecialchars($programme['title']) ?></h1>
                        <p class="text-xl opacity-90"><?= ucfirst(htmlspecialchars($programme['level'])) ?> Programme</p>
                    </div>
                </div>
            </div>            <!-- Programme Stats -->            
            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200 bg-white">
                <div class="p-6 text-center">
                    <div class="flex items-center justify-center text-indigo-600 mb-2">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">
                        <?= isset($programme['duration']) && $programme['duration'] ? htmlspecialchars($programme['duration']) . ' Years' : 'Full-time' ?>
                    </div>
                    <div class="text-sm text-gray-500">Duration</div>
                </div>
                <div class="p-6 text-center">
                    <div class="flex items-center justify-center text-indigo-600 mb-2">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">
                        <?= isset($programme['level']) ? (strtolower($programme['level']) === 'undergraduate' ? 'Bachelor\'s' : 'Master\'s') : 'Degree' ?>
                    </div>
                    <div class="text-sm text-gray-500">Qualification</div>
                </div>
                <div class="p-6 text-center">
                    <div class="flex items-center justify-center text-indigo-600 mb-2">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 mb-1">
                        <?= isset($programme['staff_count']) ? htmlspecialchars($programme['staff_count']) : '0' ?>
                    </div>
                    <div class="text-sm text-gray-500">Teaching Staff</div>
                </div>
            </div>
            
            <div class="programme-actions p-6 bg-gray-50 border-t border-gray-200">
                <?php if ($studentId): ?>    <form id="interestForm" action="<?= BASE_URL ?>/student/interests/handle" method="POST" class="w-full sm:w-auto">
        <input type="hidden" name="programme_id" value="<?= htmlspecialchars($programme['id'] ?? '') ?>">
        <input type="hidden" name="redirect" value="<?= BASE_URL ?>/student/programme_details?id=<?= htmlspecialchars($programme['id'] ?? '') ?>">
        <?php if ($hasInterest): ?>
            <input type="hidden" name="action" value="withdraw">
            <button type="submit" 
                    class="w-full sm:w-auto px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg 
                           shadow-sm transition duration-150 ease-in-out flex items-center justify-center gap-2">
                <i class="fas fa-bookmark"></i> 
                <span>Withdraw Interest</span>
            </button>
        <?php else: ?>
            <input type="hidden" name="action" value="register">
            <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg 
                           shadow-sm transition duration-150 ease-in-out flex items-center justify-center gap-2">
                <i class="far fa-bookmark"></i> 
                <span>Register Interest</span>
            </button>
        <?php endif; ?>
    </form>
<?php else: ?>
    <a href="<?= htmlspecialchars(BASE_URL) ?>/auth/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
       class="w-full sm:w-auto px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg 
              shadow-sm transition duration-150 ease-in-out flex items-center justify-center gap-2">
        <i class="fas fa-sign-in-alt"></i>
        <span>Login to Register Interest</span>
    </a>
<?php endif; ?>
            </div>
        </div>

        <!-- Programme Details -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Description -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">About the Programme</h2>
                    <div class="prose max-w-none">
                        <?= nl2br(htmlspecialchars($programme['description'])) ?>
                    </div>
                </div>                <!-- Modules -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Programme Modules</h2>
                    <?php 
                    $modules = $programme['modules'] ?? [];
                    if (!empty($modules)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($modules as $module): ?>
                                <div class="relative group">
                                    <div class="bg-white border border-gray-200 rounded-lg p-5 hover:shadow-md transition duration-150 ease-in-out">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            <?= htmlspecialchars($module['title'] ?? 'Untitled Module') ?>
                                        </h3>
                                        <div class="text-sm text-gray-500 space-y-2">
                                            <p class="flex items-center gap-2">
                                                <i class="fas fa-calendar text-indigo-600"></i>
                                                Year <?= htmlspecialchars($module['year_of_study'] ?? 'N/A') ?>
                                            </p>
                                            <?php if (isset($module['staff_name'])): ?>
                                            <p class="flex items-center gap-2">
                                                <i class="fas fa-user text-indigo-600"></i>
                                                <?= htmlspecialchars($module['staff_name']) ?>
                                            </p>
                                            <?php endif; ?>
                                            <?php if (isset($module['credits'])): ?>
                                            <p class="flex items-center gap-2">
                                                <i class="fas fa-star text-indigo-600"></i>
                                                <?= htmlspecialchars($module['credits']) ?> Credits
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <div class="mb-4 text-4xl">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <p>No modules are currently available for this programme.</p>
                        </div>
                    <?php endif; ?>
                        <?php foreach ($modules as $module): ?>
                        <div class="border rounded-lg p-4">
                            <div class="h-32 mb-4">
                                <img src="<?= htmlspecialchars($module['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($module['title']) ?>"
                                     class="w-full h-full object-cover rounded">
                            </div>
                            <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($module['title']) ?></h3>
                            <p class="text-gray-600 mb-2"><?= htmlspecialchars($module['description'] ?? '') ?></p>
                            <div class="text-sm text-gray-500">
                                <span class="mr-4">
                                    <i class="fas fa-award"></i> <?= htmlspecialchars($module['credits']) ?> Credits
                                </span>
                                <span>
                                    <i class="fas fa-calendar"></i> Year <?= htmlspecialchars($module['year_of_study']) ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Shared Modules -->
                <?php if (!empty($sharedModules)): ?>
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Shared Modules</h2>
                    <div class="space-y-4">
                        <?php foreach ($sharedModules as $module): ?>
                        <div class="border-b pb-4 last:border-b-0 last:pb-0">
                            <h3 class="font-bold mb-2"><?= htmlspecialchars($module['title']) ?></h3>
                            <p class="text-sm text-gray-600 mb-2">Shared with:</p>
                            <ul class="text-sm text-gray-500">
                                <?php foreach ($module['shared_with'] as $prog): ?>
                                <li class="flex items-center">
                                    <i class="fas fa-share-alt mr-2"></i>
                                    <a href="?id=<?= $prog['id'] ?>" class="hover:text-blue-500">
                                        <?= htmlspecialchars($prog['title']) ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Key Information -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4">Key Information</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Department</span>
                            <span class="font-medium"><?= htmlspecialchars($programme['department']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Level</span>
                            <span class="font-medium"><?= htmlspecialchars($programme['level']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Duration</span>
                            <span class="font-medium"><?= htmlspecialchars($programme['duration']) ?></span>
                        </div>
                        <?php if (isset($programme['start_date'])): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Start Date</span>
                            <span class="font-medium"><?= htmlspecialchars($programme['start_date']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/js/alpine.min.js"></script>
    <script>
        async function registerInterest(programmeId) {
            try {
                const response = await fetch(`${BASE_URL}/student/register_interest_api.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ programme_id: programmeId })
                });
                
                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.error || 'Failed to register interest');
                }
                
                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    throw new Error(data.error || 'Failed to register interest');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to register interest. Please try again later.');
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
                
                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.error || 'Failed to withdraw interest');
                }
                
                const data = await response.json();
                if (data.success) {
                    location.reload();
                } else {
                    throw new Error(data.error || 'Failed to withdraw interest');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to withdraw interest. Please try again later.');
            }
        }

        function registerInterest(programmeId) {
            const btn = document.getElementById('registerBtn');
            if (!btn) return;

            // Disable button and show loading state
            btn.disabled = true;
            const originalContent = btn.innerHTML;
            btn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Registering...</span>
            `;

            // Send AJAX request
            fetch('<?= BASE_URL ?>/api/register_interest.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ programme_id: programmeId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the page to show updated state
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to register interest');
                }
            })
            .catch(error => {
                // Show error and restore button state
                alert(error.message);
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        }

        // Add loading state to withdrawal form submission
        document.addEventListener('DOMContentLoaded', function() {
            const withdrawForm = document.querySelector('form[action="withdraw_interest.php"]');
            if (withdrawForm) {
                withdrawForm.addEventListener('submit', function(e) {
                    const btn = this.querySelector('button');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = `
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Withdrawing...</span>
                        `;
                    }
                });
            }
        });
    </script>
</body>
</html>