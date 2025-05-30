<?php
$pageTitle = "Browse Programmes";
require_once __DIR__ . '/layouts/header.php';
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
<body class="bg-gray-100">
    <div class="min-h-screen">
        <?php require_once __DIR__ . '/layouts/student_sidebar_new.php'; ?>        <main class="lg:pl-72">
            <div class="px-4 py-8 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold text-gray-900">Browse Programmes</h1>
                    <p class="mt-2 text-sm text-gray-600">Explore our range of undergraduate and postgraduate programmes</p>
                </div>

                <!-- Search and Filters Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text"
                                   id="searchInput"
                                   class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm"
                                   placeholder="Search programmes..."
                            >
                        </div>                        <select id="levelFilter" class="block w-full rounded-md border-0 py-2.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm">
                            <option value="">All Levels</option>
                            <option value="undergraduate">Undergraduate</option>
                            <option value="postgraduate">Postgraduate</option>
                        </select>
                        <select id="departmentFilter" class="block w-full rounded-md border-0 py-2.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm">
                            <option value="">All Departments</option>
                            <?php if (isset($departments)): foreach ($departments as $dept): ?>
                                <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden">
                    <div class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    </div>
                </div>                <!-- Programme Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6" id="programmesGrid">
                    <?php if (!empty($programmes)): foreach($programmes as $programme): ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <!-- Programme Image -->
                        <div class="relative h-48 bg-gray-200">
                            <?php if (!empty($programme['image_url'])): ?>
                                <img src="<?= htmlspecialchars($programme['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($programme['title']) ?>"
                                     class="w-full h-full object-cover">
                            <?php endif; ?>
                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold uppercase
                                       <?= strtolower($programme['level']) === 'undergraduate' ? 'bg-blue-500' : 'bg-purple-500' ?> text-white">
                                <?= htmlspecialchars($programme['level']) ?>
                            </span>
                        </div>

                        <!-- Programme Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($programme['title']) ?></h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?= htmlspecialchars($programme['description'] ?? 'No description available') ?></p>

                            <!-- Programme Meta -->
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="text-center">
                                    <i class="fas fa-calendar-alt text-blue-500 mb-1"></i>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($programme['duration'] ?? '3 Years') ?></p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-book text-blue-500 mb-1"></i>
                                    <p class="text-xs text-gray-600"><?= count($programme['modules'] ?? []) ?> Modules</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-graduation-cap text-blue-500 mb-1"></i>
                                    <p class="text-xs text-gray-600"><?= htmlspecialchars($programme['credits'] ?? '360') ?> Credits</p>
                                </div>
                            </div>

                            <!-- Key Features -->
                            <?php if (!empty($programme['key_features'])): ?>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php foreach ($programme['key_features'] as $feature): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    <?= htmlspecialchars($feature) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>                            <!-- Actions -->                            <div class="flex gap-3">                                <a href="<?= BASE_URL ?>/student/programme_details?id=<?= $programme['id'] ?>" 
                                   class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    View Details
                                </a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <?php if (!isset($programme['interest_registered'])): ?>
                                        <button onclick="registerInterest(<?= $programme['id'] ?>)" 
                                                class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="far fa-bookmark mr-2"></i>
                                            Register Interest
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/student/manage_interests" 
                                           class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-600 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <i class="fas fa-bookmark mr-2"></i>
                                            Registered
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Programmes Found</h3>
                            <p class="text-gray-600">Try adjusting your search or filters to find what you're looking for.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination (if needed) -->
                <?php if (isset($pagination)): ?>
                <div class="mt-6 flex justify-center">
                    <nav class="relative z-0 inline-flex shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <a href="?page=<?= $i ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= $i === $pagination['current_page'] ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
    // Search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const levelFilter = document.getElementById('levelFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    const programmesGrid = document.getElementById('programmesGrid');
    const loadingState = document.getElementById('loadingState');

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
    }    async function filterProgrammes() {
        const searchTerm = searchInput.value;
        const level = levelFilter.value;
        const department = departmentFilter.value;
        
        // Show loading state
        loadingState.classList.remove('hidden');
        programmesGrid.classList.add('opacity-50');
        
        try {
            // Fetch filtered results from the server
            const params = new URLSearchParams({
                query: searchTerm,
                level: level,
                department: department
            });

            const response = await fetch(`${BASE_URL}/api/programmes/filter?${params}`);
            const data = await response.json();

            // Update the grid with new results
            const programmesGrid = document.getElementById('programmesGrid');
            programmesGrid.innerHTML = '';

            if (data.programmes.length === 0) {
                programmesGrid.innerHTML = `
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Programmes Found</h3>
                            <p class="text-gray-600">Try adjusting your search or filters to find what you're looking for.</p>
                        </div>
                    </div>
                `;
            } else {
                data.programmes.forEach(programme => {                    programmesGrid.innerHTML += `
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <div class="relative h-48 bg-gray-200">
                                ${programme.image_url ? `
                                    <img src="${programme.image_url}" 
                                         alt="${programme.title}"
                                         class="w-full h-full object-cover">
                                ` : ''}
                                <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold uppercase
                                           ${programme.level.toLowerCase() === 'undergraduate' ? 'bg-blue-500' : 'bg-purple-500'} text-white">
                                    ${programme.level}
                                </span>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">${programme.title}</h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">${programme.description || 'No description available'}</p>
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="text-center">
                                        <i class="fas fa-calendar-alt text-blue-500 mb-1"></i>
                                        <p class="text-xs text-gray-600">${programme.duration_years} Years</p>
                                    </div>
                                    <div class="text-center">
                                        <i class="fas fa-book text-blue-500 mb-1"></i>
                                        <p class="text-xs text-gray-600">${programme.module_count} Modules</p>
                                    </div>
                                    <div class="text-center">
                                        <i class="fas fa-graduation-cap text-blue-500 mb-1"></i>
                                        <p class="text-xs text-gray-600">${programme.credits || '360'} Credits</p>
                                    </div>
                                </div>
                                ${programme.key_features ? `
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        ${programme.key_features.map(feature => `
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                                ${feature}
                                            </span>
                                        `).join('')}
                                    </div>
                                ` : ''}
                                <div class="flex gap-3">
                                    <a href="<?= BASE_URL ?>/student/programme_details?id=${programme.id}" 
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
        } catch (error) {
            console.error('Error filtering programmes:', error);
        } finally {
            // Hide loading state
            loadingState.classList.add('hidden');
            programmesGrid.classList.remove('opacity-50');
        }
    }

    // Add event listeners with debounce
    const debouncedFilter = debounce(filterProgrammes, 300);
    searchInput.addEventListener('input', debouncedFilter);
    levelFilter.addEventListener('change', filterProgrammes);
    departmentFilter.addEventListener('change', filterProgrammes);

    // Register interest function    async function registerInterest(programmeId) {
        try {
            const response = await fetch('register_interest_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ programme_id: programmeId })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Update UI to show registered state
                const button = document.querySelector(`button[onclick="registerInterest(${programmeId})"]`);
                if (button) {
                    const parent = button.parentElement;
                    parent.innerHTML = `
                        <a href="manage_interests.php" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-600 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-bookmark mr-2"></i>
                            Registered
                        </a>
                    `;
                }
                // Show success message
                alert('Successfully registered interest in this programme!');
            } else {
                throw new Error(data.error || 'Failed to register interest');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Failed to register interest. Please try again later.');
        }
    }
    </script>
</body>
</html>
