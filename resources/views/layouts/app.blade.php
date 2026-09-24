{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Brand Management Dashboard' }}</title>
    <meta name="description" content="Manage development brands, repositories, tokens and ownership from one place with clean Laravel structure, Tailwind CSS 4, and TW Elements.">
    <meta property="og:title" content="Brand Management Dashboard">
    <meta property="og:description" content="Manage development brands, repositories, tokens and ownership from one place with clean Laravel structure, Tailwind CSS 4, and TW Elements.">
    <meta property="og:type" content="website">

    <!-- Anti-flash Theme Initialization Script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Vite Stylesheet & JavaScript -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        code, pre, .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-200">

    <!-- Side Panel / Sidebar Component -->
    @include('dashboard.components.sidebar')

    <!-- Main Content Wrapper (shifted left on desktop for sidebar) -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-30 bg-white/95 dark:bg-slate-900/95 border-b border-slate-200/80 dark:border-slate-800 shadow-2xs backdrop-blur-md transition-colors">
            <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left: Mobile Sidebar Toggle + Brand Title -->
                    <div class="flex items-center space-x-3">
                        <button
                            type="button"
                            onclick="toggleSidebar()"
                            class="lg:hidden p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition focus:outline-none">
                            <span class="sr-only">Open Sidebar</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <div class="flex items-center space-x-2">
                            <h2 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                                Brand Dashboard
                            </h2>
                            <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                Laravel 11
                            </span>
                        </div>
                    </div>

                    <!-- Right Side: Dark Mode Toggle, Storage Badge & Add Button -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <!-- Dark / Light Mode Toggle Button -->
                        <button
                            type="button"
                            onclick="toggleTheme()"
                            title="Toggle Light / Dark Mode"
                            class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition">
                            <!-- Sun for Light Mode -->
                            <svg class="w-4 h-4 text-amber-500 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Moon for Dark Mode -->
                            <svg class="w-4 h-4 text-indigo-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>

                        <div class="hidden md:flex items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-300 bg-slate-100/80 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Storage: <code class="text-indigo-600 dark:text-indigo-400 font-mono">brands.json</code></span>
                        </div>

                        <button
                            type="button"
                            data-twe-toggle="modal"
                            data-twe-target="#addBrandModal"
                            data-twe-ripple-init
                            class="inline-flex items-center px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200 dark:shadow-none transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="hidden sm:inline">+ Add New Brand</span>
                            <span class="sm:hidden">+ Add</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 py-6 sm:py-8">
            <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Flash Toast Notifications -->
                @include('dashboard.components.toast')

                @yield('content')
            </div>
        </main>

        <!-- Clean Footer -->
        <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 py-6 text-xs text-slate-500 dark:text-slate-400 transition-colors">
            <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <span class="font-medium text-slate-700 dark:text-slate-300">BrandOS</span>
                    <span>&bull;</span>
                    <span>Laravel &bull; Blade &bull; Tailwind CSS 4 &bull; TW Elements</span>
                </div>
                <div class="flex items-center space-x-4 text-slate-400 dark:text-slate-500">
                    <span>storage/app/brands.json</span>
                    <span>&bull;</span>
                    <span>No Database Driver</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Modals -->
    @include('dashboard.components.add-brand-modal')
    @include('dashboard.components.edit-brand-modal')
    @include('dashboard.components.delete-modal')

    @stack('scripts')
</body>
</html>
