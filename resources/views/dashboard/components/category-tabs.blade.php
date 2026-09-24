{{-- Category Tabs: Dynamically generated from $categories array --}}

<div class="border-b border-slate-200 dark:border-slate-800 mb-6 overflow-x-auto">
    <nav class="flex space-x-1 sm:space-x-2 min-w-max pb-px" id="category-tabs-nav" aria-label="Brand Categories">
        {{-- All Brands Tab --}}
        @php
            $isAllActive = empty($filters['category']) || $filters['category'] === 'all';
        @endphp
        <a href="{{ route('dashboard', array_merge($filters, ['category' => ''])) }}"
           class="inline-flex items-center gap-2 py-3 px-3.5 text-xs sm:text-sm font-medium rounded-t-lg transition border-b-2 {{ $isAllActive ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40 font-semibold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700' }}">
            <span>All Categories</span>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $isAllActive ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                {{ $stats['total'] ?? 0 }}
            </span>
        </a>

        {{-- Dynamic Category Tabs --}}
        @foreach($categories as $cat)
            @php
                $isActive = (isset($filters['category']) && $filters['category'] === $cat);
                $catCount = $stats['categoryCounts'][$cat] ?? 0;
            @endphp
            <a href="{{ route('dashboard', array_merge($filters, ['category' => $cat])) }}"
               class="inline-flex items-center gap-2 py-3 px-3.5 text-xs sm:text-sm font-medium rounded-t-lg transition border-b-2 {{ $isActive ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40 font-semibold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700' }}">
                <span>{{ $cat }}</span>
                <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $isActive ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">
                    {{ $catCount }}
                </span>
            </a>
        @endforeach
    </nav>
</div>
