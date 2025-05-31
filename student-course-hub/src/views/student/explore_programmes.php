<?php
// Data should be passed from the controller
// Set defaults for all expected variables
$programmes = $programmes ?? [];
$interests = $interests ?? [];
$filters = $_GET ?? [];
$departments = $departments ?? [];
$query = $_GET['query'] ?? '';
$level = $_GET['level'] ?? '';
$duration = $_GET['duration'] ?? '';
$department = $_GET['department'] ?? '';
?>

<?php
$pageTitle = "Explore Programmes";
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Load Tailwind and FontAwesome -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<script>    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: '#2563eb',
                    secondary: '#64748b',
                },
                spacing: {
                    '18': '4.5rem',
                    '22': '5.5rem',
                },
                boxShadow: {
                    'soft': '0 2px 15px -3px rgba(0,0,0,0.07), 0 10px 20px -2px rgba(0,0,0,0.04)',
                },
                animation: {
                    'slide-up': 'slideUp 0.5s ease-out',
                }
            },
        },
    }
</script>

<style>    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-fadeIn {
        animation: fadeIn 0.6s ease-out;
    }
    .animate-slide-up {
        animation: slideUp 0.5s ease-out;
    }    .sidebar {
        position: fixed;
        inset-y-0;
        left-0;
        width: 64;
        background: #1e1b4b;
        box-shadow: 4px 0 15px -3px rgba(0,0,0,0.1);
        z-index: 30;
    }
    .programme-card {
        transition: all 0.3s ease;
    }
    .programme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>

<div class="min-h-screen bg-[#f8fafc]">
    <?php require_once __DIR__ . '/../layouts/student_sidebar_new.php'; ?>
    <main class="ml-64 p-8">
        <div class="max-w-7xl mx-auto animate-fadeIn">
            <!-- Header Section -->
            <header class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Explore Study Programmes</h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Discover undergraduate and postgraduate programmes to advance your academic journey and career prospects
                </p>
            </header>                <!-- Search and Filters Section -->
            <div class="bg-white rounded-2xl shadow-soft p-6 mb-10 animate-fadeIn">
                <form action="" method="GET" class="space-y-6">                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Search Input -->
                        <div class="col-span-1 md:col-span-2 lg:col-span-4">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                Search Programmes
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" name="query" id="search" 
                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg
                                              focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                                              text-gray-900 placeholder:text-gray-400"
                                       placeholder="Search programmes by name or keyword"
                                       value="<?= htmlspecialchars($query ?? '') ?>">
                            </div>
                        </div>
                        
                        <!-- Programme Level Filter -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                                Programme Level
                            </label>
                            <select id="level" name="level" 
                                    class="block w-full pl-3 pr-10 py-2.5 border border-gray-300 rounded-lg
                                           focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                                           bg-white">
                            <option value="">All Levels</option>
                            <option value="undergraduate" <?= isset($filters['level']) && $filters['level'] === 'undergraduate' ? 'selected' : '' ?>>
                                Undergraduate
                            </option>
                            <option value="postgraduate" <?= isset($filters['level']) && $filters['level'] === 'postgraduate' ? 'selected' : '' ?>>
                                Postgraduate
                            </option>
                        </select>
                    </div>

                    <!-- Duration Filter -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Duration
                        </label>
                        <select id="duration" name="duration"
                                class="block w-full pl-3 pr-10 py-2.5 border border-gray-300 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                                       bg-white">
                        <option value="">Any Duration</option>                                    <option value="1">1 Year</option>
                        <option value="2">2 Years</option>
                        <option value="3">3 Years</option>
                        <option value="4">4 Years</option>
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Department
                    </label>
                    <select id="department" name="department"
                            class="block w-full pl-3 pr-10 py-2.5 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                                   bg-white">
                    <option value="">All Departments</option><?php if (isset($departments) && is_array($departments)): ?>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                        <?php endforeach; ?>                            <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-span-1 md:col-span-2 lg:col-span-4 flex justify-end">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>        <?php if (empty($programmes)): ?>
        <div class="text-center py-16 bg-white rounded-2xl shadow-soft animate-fadeIn">
            <div class="max-w-md mx-auto">
                <i class="fas fa-search text-6xl text-primary/20 mb-6"></i>
                <h3 class="text-2xl font-semibold text-gray-800 mb-3">No programmes found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search criteria or explore all available programmes</p>
                <button onclick="window.location.href='?'" 
                        class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200">
                    View All Programmes
                </button>
            </div>
        </div><?php else: ?>            <!-- Programmes Grid -->            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fadeIn">
            <?php foreach ($programmes as $programme): ?>
                <div class="programme-card group bg-white rounded-2xl shadow-soft overflow-hidden">
                    <!-- Programme Image -->
                    <div class="h-48 bg-cover bg-center relative" 
                         style="background-image: url('<?= htmlspecialchars($programme['image_url'] ?? '/assets/images/default-programme.jpg') ?>')">
                        <div class="absolute inset-0 bg-black bg-opacity-40 transition-opacity group-hover:bg-opacity-30"></div>
                        <span class="absolute top-4 right-4 bg-primary text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm">
                            <?= ucfirst(htmlspecialchars($programme['level'] ?? 'undergraduate')) ?>
                        </span>
                    </div>
                    
                    <!-- Programme Details -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                            <?= htmlspecialchars($programme['title']) ?>
                        </h3>
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                            <?= htmlspecialchars($programme['description'] ?? 'No description available') ?>
                        </p>                            <!-- Programme Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg text-center transition-all duration-200 hover:bg-gray-100 hover:-translate-y-0.5">
                                <i class="fas fa-book text-primary text-xl mb-2"></i>
                                <span class="block text-sm font-semibold text-gray-900"><?= ($programme['module_count'] ?? 0) ?> Modules</span>
                                <span class="text-xs text-gray-500">Total Modules</span>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center transition-all duration-200 hover:bg-gray-100 hover:-translate-y-0.5">                                    <i class="fas fa-clock text-primary text-xl mb-2"></i>
                                <span class="block text-sm font-semibold text-gray-900"><?= htmlspecialchars($programme['duration'] ?? '3 Years') ?></span>
                                <span class="text-xs text-gray-500">Duration</span>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center transition-all duration-200 hover:bg-gray-100 hover:-translate-y-0.5">
                                <i class="fas fa-graduation-cap text-primary text-xl mb-2"></i>
                                <span class="block text-sm font-semibold text-gray-900"><?= htmlspecialchars($programme['qualification'] ?? 'Degree') ?></span>
                                <span class="text-xs text-gray-500">Qualification</span>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center transition-all duration-200 hover:bg-gray-100 hover:-translate-y-0.5">
                                <i class="fas fa-users text-primary text-xl mb-2"></i>
                                <span class="block text-sm font-semibold text-gray-900"><?= ($programme['staff_count'] ?? 0) ?> Staff</span>
                                <span class="text-xs text-gray-500">Teaching Staff</span>
                            </div>                                <?php if (!empty($programme['department'])): ?>
                            <div class="col-span-2 bg-gray-50 p-4 rounded-lg text-center transition-all duration-200 hover:bg-gray-100 hover:-translate-y-0.5">
                                <i class="fas fa-university text-primary text-xl mb-2"></i>
                                <span class="block text-sm font-semibold text-gray-900"><?= htmlspecialchars($programme['department']) ?></span>
                                <span class="text-xs text-gray-500">Department</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Programme Features -->
                        <?php if (!empty($programme['key_features'])): ?>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <?php foreach ($programme['key_features'] as $feature): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    <?= htmlspecialchars($feature) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="flex gap-4 mt-auto">
                            <a href="<?= BASE_URL ?>/student/programme_details?id=<?= $programme['id'] ?>" 
                               class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-primary text-white rounded-lg
                                      hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                                      transition-all duration-200 font-medium">
                                <i class="fas fa-info-circle mr-2"></i> View Details
                            </a>                            <?php if (isset($_SESSION['user_id'])): ?>
                            <button data-programme-id="<?= $programme['id'] ?>" 
                                    data-action="<?= isset($programme['interest_registered']) ? 'withdraw' : 'register' ?>"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 
                                           <?= isset($programme['interest_registered']) 
                                               ? 'bg-red-100 text-red-700 hover:bg-red-200' 
                                               : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> 
                                           rounded-lg font-medium
                                           focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 
                                           transition-all duration-200">
                            <i class="<?= isset($programme['interest_registered']) ? 'fas' : 'far' ?> fa-bookmark mr-2"></i>
                            <?= isset($programme['interest_registered']) ? 'Withdraw Interest' : 'Register Interest' ?>
                        </button>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/auth/login?redirect=<?= urlencode(BASE_URL . '/student/explore_programmes') ?>" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2.5 
                                  bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg font-medium
                                  focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 
                                  transition-all duration-200">
                            <i class="far fa-bookmark mr-2"></i> Login to Register
                        </a>                                <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debounce function definition
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
    }

    // Function to attach interest button listeners
    function attachInterestButtonListeners() {
        const interestButtons = document.querySelectorAll('button[data-programme-id]');
        interestButtons.forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();
                const programmeId = this.dataset.programmeId;
                const currentAction = this.dataset.action;
                const isRegistering = currentAction === 'register';
                
                try {
                    const response = await fetch('<?= BASE_URL ?>/student/register_interest_api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            programme_id: programmeId,
                            action: currentAction
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        alert(isRegistering ? 'Successfully registered interest!' : 'Successfully withdrawn interest!');
                        
                        this.dataset.action = isRegistering ? 'withdraw' : 'register';
                        this.innerHTML = `
                            <i class="${isRegistering ? 'fas' : 'far'} fa-bookmark mr-2"></i>
                            ${isRegistering ? 'Withdraw Interest' : 'Register Interest'}
                        `;
                        this.className = `flex-1 inline-flex justify-center items-center px-4 py-2.5 ${
                            isRegistering ? 
                            'bg-red-100 text-red-700 hover:bg-red-200' : 
                            'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        } rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200`;
                    } else {
                        throw new Error(data.error || 'Failed to process request');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to process request. Please try again later.');
                }
            });
        });
    }

    // Initial attachment of interest button listeners
    attachInterestButtonListeners();

    // Search and filter functionality
    const searchInput = document.getElementById('search');
    const levelSelect = document.getElementById('level');
    const durationSelect = document.getElementById('duration');
    const departmentSelect = document.getElementById('department');
    const form = document.querySelector('form');
    const programmesContainer = document.querySelector('.grid');

    // Function to handle form submission via AJAX
    async function handleFilters() {
        // Show loading state
        if (programmesContainer) {
            programmesContainer.style.opacity = '0.5';
        }

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        try {
            const response = await fetch(`${window.location.pathname}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGrid = doc.querySelector('.grid');
            
            if (newGrid && programmesContainer) {
                programmesContainer.innerHTML = newGrid.innerHTML;
                // Reattach event listeners to the new content
                attachInterestButtonListeners();
            }
            
            // Update URL without reloading
            window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
            
        } catch (error) {
            console.error('Error:', error);
        } finally {
            // Remove loading state
            if (programmesContainer) {
                programmesContainer.style.opacity = '1';
            }
        }
    }

    // Add event listeners for real-time filtering
    searchInput.addEventListener('input', debounce(handleFilters, 300));
    levelSelect.addEventListener('change', handleFilters);
    durationSelect.addEventListener('change', handleFilters);
    departmentSelect.addEventListener('change', handleFilters);
    
    // Prevent form submission and handle it via AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        handleFilters();
    });
});
</script>
</body>
</html>
