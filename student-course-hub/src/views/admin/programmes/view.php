<?php
$pageTitle = "View Programme";
$headerText = "Programme Details: " . htmlspecialchars($programme['title']);
$breadcrumbText = "View Programme";
$activeMenu = "programmes"; // For highlighting the active menu item in the sidebar
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <title><?php echo htmlspecialchars($pageTitle); ?> - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="flex min-h-screen"><?php include_once __DIR__ . '/../../layouts/sidebar.php'; ?>        <div class="flex-1 ml-64 p-8">
            <header class="mb-8 pb-4 border-b border-gray-200">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($headerText); ?></h1>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                    </svg>
                                    <a href="<?php echo BASE_URL; ?>/admin/programmes" class="text-blue-600 hover:text-blue-800">Programmes</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                    </svg>
                                    <span class="text-gray-500"><?php echo htmlspecialchars($breadcrumbText); ?></span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
            </header>

            <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Title</h3>
                        <p class="text-gray-900"><?php echo htmlspecialchars($programme['title']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Level</h3>
                        <p class="text-gray-900"><?php echo htmlspecialchars($programme['level']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Duration</h3>
                        <p class="text-gray-900"><?php echo htmlspecialchars($programme['duration']); ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Status</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium <?php echo $programme['is_published'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $programme['is_published'] ? 'Published' : 'Draft'; ?>
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Description</h3>
                    <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($programme['description'] ?? 'No description available.')); ?></p>
                </div>                <?php if (!empty($programme['modules'])): ?>
                <div class="mt-8">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-4">Modules</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($programme['modules'] as $module): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($module['code'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($module['title']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($module['credits']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($module['year_of_study'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="mt-6 space-x-4">                <a href="<?php echo BASE_URL; ?>/admin/programmes/<?php echo $programme['id']; ?>/edit" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-edit mr-2"></i> Edit Programme
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/programmes" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</body>
</html>
