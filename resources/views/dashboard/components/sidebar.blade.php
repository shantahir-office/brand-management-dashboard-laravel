{{-- resources/views/dashboard/components/sidebar.blade.php --}}
<aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-transform duration-300 transform -translate-x-full lg:translate-x-0">
    <!-- Sidebar Header / Branding -->
    <div class="h-16 flex items-center justify-between px-5 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center space-x-3">
            <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-700 to-violet-600 flex items-center justify-center text-white shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div>
                <span class="text-base font-bold text-slate-900 dark:text-white tracking-tight">BrandOS</span>
                <span class="block text-[10px] font-medium text-slate-500 dark:text-slate-400">Laravel 11 &bull; Blade</span>
            </div>
        </div>

        <!-- Close Sidebar Button (Mobile) -->
        <button
            type="button"
            onclick="toggleSidebar()"
            class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar Scrollable Navigation Content -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        <!-- Main Navigation -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Main
            </div>
            <div class="space-y-1">
                <button
                    type="button"
                    onclick="setCategoryFilter('all')"
                    data-sidebar-category="all"
                    class="sidebar-nav-item w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        <span>All Brands</span>
                    </div>
                    <span id="sidebar-count-all" class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                        {{ $stats['total'] ?? 0 }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Categories List -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center justify-between">
                <span>Categories</span>
                <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">8 total</span>
            </div>
            <div class="space-y-1" id="sidebar-categories-list">
                @foreach($categories as $cat)
                    <button
                        type="button"
                        onclick="setCategoryFilter('{{ $cat }}')"
                        data-sidebar-category="{{ $cat }}"
                        class="sidebar-nav-item w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl transition text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <div class="flex items-center space-x-2.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                            <span class="truncate">{{ $cat }}</span>
                        </div>
                        <span class="sidebar-cat-count text-[11px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400" data-cat="{{ $cat }}">
                            {{ $stats['categoryCounts'][$cat] ?? 0 }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Quick Status Filters -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                Quick Filters
            </div>
            <div class="space-y-1">
                <button
                    type="button"
                    onclick="quickFilterStatus('Active')"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Active Brands</span>
                    </div>
                    <span id="sidebar-count-active" class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                        {{ $stats['active'] ?? 0 }}
                    </span>
                </button>

                <button
                    type="button"
                    onclick="quickFilterStatus('Development')"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>In Development</span>
                    </div>
                    <span id="sidebar-count-dev" class="text-[11px] font-semibold text-blue-600 dark:text-blue-400">
                        {{ $stats['development'] ?? 0 }}
                    </span>
                </button>

                <button
                    type="button"
                    onclick="quickFilterStatus('Completed')"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span>Completed</span>
                    </div>
                    <span id="sidebar-count-completed" class="text-[11px] font-semibold text-purple-600 dark:text-purple-400">
                        {{ $stats['completed'] ?? 0 }}
                    </span>
                </button>

                <button
                    type="button"
                    onclick="quickFilterLaravel()"
                    class="w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Laravel Only</span>
                    </div>
                    <span id="sidebar-count-laravel" class="text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                        {{ $stats['laravel'] ?? 0 }}
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Footer / Theme Toggle & Storage Info -->
    <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/70 space-y-3">
        <!-- Theme Mode Toggle Button -->
        <button
            type="button"
            onclick="toggleTheme()"
            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition shadow-2xs">
            <div class="flex items-center space-x-2">
                <!-- Sun Icon for Light Mode -->
                <svg class="w-4 h-4 text-amber-500 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <!-- Moon Icon for Dark Mode -->
                <svg class="w-4 h-4 text-indigo-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <span id="theme-text" class="text-xs">Light Mode</span>
            </div>
            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">Toggle</span>
        </button>

        <!-- Storage File Info -->
        <div class="text-[11px] text-slate-500 dark:text-slate-400 px-1 flex items-center justify-between">
            <span class="truncate">storage/app/brands.json</span>
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Backdrop Overlay -->
<div
    id="sidebar-backdrop"
    onclick="toggleSidebar()"
    class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden transition-opacity"></div>
