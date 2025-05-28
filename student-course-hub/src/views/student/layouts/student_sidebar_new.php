<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="fixed inset-y-0 flex w-72 flex-col">
    <!-- Sidebar component -->
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 ring-1 ring-white/5">
        <div class="flex h-16 shrink-0 items-center">
            <h1 class="text-xl font-semibold text-white">Student Course Hub</h1>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        <li>
                            <a href="<?= BASE_URL ?>/student/dashboard" 
                               class="<?= $current_page == 'dashboard.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <i class="fas fa-home h-6 w-6 shrink-0"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/student/explore_programmes" 
                               class="<?= $current_page == 'programmes_new.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <i class="fas fa-search h-6 w-6 shrink-0"></i>
                                Explore Programmes
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/student/manage_interests" 
                               class="<?= $current_page == 'manage_interests.php' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?> group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                                <i class="fas fa-star h-6 w-6 shrink-0"></i>
                                My Interests
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="mt-auto">
                    <a href="<?= BASE_URL ?>/logout" 
                       class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white">
                        <i class="fas fa-sign-out-alt h-6 w-6 shrink-0"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
