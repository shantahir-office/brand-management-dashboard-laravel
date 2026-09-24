{{-- Dashboard Statistics & Data Quality Cards (Dynamically computed from brands.json) --}}

<div class="mb-8 space-y-4">
    <!-- Top 4 Primary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Brands Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Brands</span>
                <span class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 group-hover:scale-105 transition transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white" id="stat-total">{{ $stats['total'] ?? 0 }}</span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Across {{ count($categories ?? []) }} categories</span>
            </div>
        </div>

        <!-- Active Brands Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Brands</span>
                <span class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400" id="stat-active">{{ $stats['active'] ?? 0 }}</span>
                <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/80 px-2 py-0.5 rounded-full" id="stat-active-pct">
                    {{ ($stats['total'] ?? 0) > 0 ? round((($stats['active'] ?? 0) / $stats['total']) * 100) : 0 }}% of total
                </span>
            </div>
        </div>

        <!-- In Development Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">In Development</span>
                <span class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold tracking-tight text-blue-600 dark:text-blue-400" id="stat-development">{{ $stats['development'] ?? 0 }}</span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Work in progress</span>
            </div>
        </div>

        <!-- Completed Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Completed</span>
                <span class="p-2 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 group-hover:scale-105 transition transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline justify-between">
                <span class="text-3xl font-extrabold tracking-tight text-purple-600 dark:text-purple-400" id="stat-completed">{{ $stats['completed'] ?? 0 }}</span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Production ready</span>
            </div>
        </div>
    </div>

    <!-- Secondary Metric & Data Quality Banner (Missing Token, Missing Repo, Laravel Brands) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Laravel Brands -->
        <div class="bg-gradient-to-r from-rose-50/70 to-orange-50/70 dark:from-rose-950/30 dark:to-orange-950/30 rounded-xl p-4 border border-rose-200/80 dark:border-rose-900/50 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 rounded-lg bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-rose-800 dark:text-rose-300">Laravel Stack</div>
                    <div class="text-xl font-bold text-rose-900 dark:text-rose-100"><span id="stat-laravel">{{ $stats['laravel'] ?? 0 }}</span> <span class="text-xs font-normal text-rose-700 dark:text-rose-400">brands powered by Laravel</span></div>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-rose-200/80 dark:bg-rose-900/70 text-rose-900 dark:text-rose-200">
                YES
            </span>
        </div>

        <!-- Missing Token Warning Indicator -->
        <div class="bg-amber-50/70 dark:bg-amber-950/30 rounded-xl p-4 border border-amber-200/80 dark:border-amber-900/50 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 rounded-lg bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">Missing Token</div>
                    <div class="text-xl font-bold text-amber-900 dark:text-amber-100"><span id="stat-missing-token">{{ $stats['missing_token'] ?? 0 }}</span> <span class="text-xs font-normal text-amber-700 dark:text-amber-400">brands need auth token</span></div>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-200 dark:bg-amber-900/70 text-amber-900 dark:text-amber-200">
                Attention
            </span>
        </div>

        <!-- Missing Repository Warning Indicator -->
        <div class="bg-slate-100/70 dark:bg-slate-900 rounded-xl p-4 border border-slate-300/80 dark:border-slate-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-3">
                <div class="p-2.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-400">Missing Repository</div>
                    <div class="text-xl font-bold text-slate-900 dark:text-slate-100"><span id="stat-missing-repo">{{ $stats['missing_repo'] ?? 0 }}</span> <span class="text-xs font-normal text-slate-600 dark:text-slate-400">brands without Git link</span></div>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 dark:bg-rose-950/80 text-rose-800 dark:text-rose-300">
                Needs Repo
            </span>
        </div>
    </div>
</div>
