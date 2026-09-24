{{-- Brand Table Component: Responsive table with badges, token toggle, repo link, and edit/delete actions --}}

@if(count($brands) === 0)
    {{-- Empty State (Requirement 14) --}}
    <div id="empty-state" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-12 text-center shadow-xs">
        <div class="mx-auto w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">No brands found</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
            @if(!empty($filters['category']) || !empty($filters['search']) || !empty($filters['status']) || !empty($filters['owner']))
                No brands match your active filters or search criteria.
            @else
                There are currently no brands in this category.
            @endif
        </p>
        <div class="mt-6 flex items-center justify-center gap-3">
            @if(!empty($filters['category']) || !empty($filters['search']) || !empty($filters['status']) || !empty($filters['owner']))
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-semibold rounded-lg text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Clear Filters
                </a>
            @endif
            <button
                type="button"
                data-twe-toggle="modal"
                data-twe-target="#addBrandModal"
                data-twe-ripple-init
                class="inline-flex items-center px-4 py-2 text-xs font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs transition">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Brand
            </button>
        </div>
    </div>
@else
    {{-- Responsive Brand Table (Requirement 8, 20, 21) --}}
    <div id="table-container" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800 text-left text-sm">
                <thead class="bg-slate-50/80 dark:bg-slate-800/80 text-xs uppercase tracking-wider font-semibold text-slate-600 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 sm:px-6">Brand Name</th>
                        <th scope="col" class="py-3.5 px-3">Status</th>
                        <th scope="col" class="py-3.5 px-3">Owner</th>
                        <th scope="col" class="py-3.5 px-3 text-center">Technology</th>
                        <th scope="col" class="py-3.5 px-3">Token</th>
                        <th scope="col" class="py-3.5 px-3">Repository</th>
                        <th scope="col" class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="brands-tbody" class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                    @foreach($brands as $brand)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition-colors group" id="brand-row-{{ $brand['id'] }}">
                        {{-- 1. Brand Name & Category --}}
                        <td class="py-4 px-4 sm:px-6">
                            <div class="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition flex items-center gap-2">
                                <span>{{ $brand['brand_name'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">
                                    {{ $brand['category'] }}
                                </span>
                                @if(!empty($brand['notes']))
                                    <span class="truncate max-w-[200px] text-slate-400 dark:text-slate-500" title="{{ $brand['notes'] }}">
                                        &bull; {{ $brand['notes'] }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- 2. Status Badge --}}
                        <td class="py-4 px-3 whitespace-nowrap">
                            @php
                                $status = $brand['status'] ?? 'Pending';
                                $statusLower = strtolower($status);
                                $badgeClass = match($statusLower) {
                                    'active'      => 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800',
                                    'development' => 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200/80 dark:border-blue-800',
                                    'completed'   => 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200/80 dark:border-purple-800',
                                    'on hold'     => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700',
                                    default       => 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200/80 dark:border-amber-800', // Pending
                                };
                                $dotClass = match($statusLower) {
                                    'active'      => 'bg-emerald-500',
                                    'development' => 'bg-blue-500',
                                    'completed'   => 'bg-purple-500',
                                    'on hold'     => 'bg-slate-400',
                                    default       => 'bg-amber-500',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $status }}
                            </span>
                        </td>

                        {{-- 3. Owner --}}
                        <td class="py-4 px-3 whitespace-nowrap">
                            @if(!empty($brand['owner']))
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold text-[10px] flex items-center justify-center uppercase">
                                        {{ substr($brand['owner'], 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $brand['owner'] }}</span>
                                </div>
                            @else
                                <span class="inline-flex items-center text-xs text-amber-600 dark:text-amber-400 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Missing Owner
                                </span>
                            @endif
                        </td>

                        {{-- 4. Technology Badge --}}
                        <td class="py-4 px-3 whitespace-nowrap text-center">
                            @php
                                $tech = $brand['technology'] ?? (!empty($brand['laravel']) ? 'Laravel' : 'PHP');
                                $techLower = strtolower($tech);
                                $techBadgeClass = match($techLower) {
                                    'laravel' => 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                    'php'     => 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                    'react'   => 'bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
                                    'html'    => 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    'next.js', 'nextjs' => 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-700',
                                    default   => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border {{ $techBadgeClass }}">
                                {{ $tech }}
                            </span>
                        </td>

                        {{-- 5. Token Masked with Eye toggle --}}
                        <td class="py-4 px-3 whitespace-nowrap">
                            @if(!empty($brand['token']))
                                <div class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-600 dark:text-slate-300" id="token-box-{{ $brand['id'] }}">
                                    <span class="token-hidden tracking-widest text-slate-500 dark:text-slate-400 font-bold select-none">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                                    <span class="token-raw hidden text-slate-800 dark:text-slate-100">{{ $brand['token'] }}</span>
                                    <button
                                        type="button"
                                        onclick="toggleTokenDisplay({{ $brand['id'] }})"
                                        title="Toggle Token Visibility"
                                        class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition p-0.5 focus:outline-none">
                                        <svg class="w-3.5 h-3.5 eye-show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg class="w-3.5 h-3.5 eye-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span class="inline-flex items-center text-xs text-amber-600 dark:text-amber-400 font-medium">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Missing Token
                                </span>
                            @endif
                        </td>

                        {{-- 6. Repository Button / Link & Owner --}}
                        <td class="py-4 px-3 whitespace-nowrap">
                            <div class="flex flex-col gap-1 items-start">
                                @if(!empty($brand['repo_link']))
                                    <a href="{{ $brand['repo_link'] }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 border border-indigo-200/80 dark:border-indigo-800 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                        </svg>
                                        <span>View Repo</span>
                                        <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @else
                                    <span class="inline-flex items-center text-xs text-rose-600 dark:text-rose-400 font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Missing Repo
                                    </span>
                                @endif
                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700" title="Repository Owner">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>{{ $brand['repo_owner'] ?? 'ahmedzafar-devTeam' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 7. Actions (Edit & Delete via TW Elements Modals) --}}
                        <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- Edit Button --}}
                                <button
                                    type="button"
                                    onclick="openEditBrandModal({{ json_encode($brand) }})"
                                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 rounded-lg shadow-2xs transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>

                                {{-- Delete Button --}}
                                <button
                                    type="button"
                                    onclick="openDeleteBrandModal({{ $brand['id'] }}, '{{ addslashes($brand['brand_name']) }}')"
                                    class="inline-flex items-center px-2 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-lg shadow-2xs transition focus:outline-none focus:ring-2 focus:ring-rose-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    <span class="sr-only">Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Table Footer Summary --}}
        <div class="px-6 py-3 bg-slate-50/80 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <div>
                Showing <span id="showing-count" class="font-semibold text-slate-700 dark:text-slate-200">{{ count($brands) }}</span> brands in <span id="showing-category" class="font-semibold text-slate-700 dark:text-slate-200">{{ !empty($filters['category']) ? $filters['category'] : 'All Categories' }}</span>
            </div>
            <div>
                Storage: <code class="font-mono text-indigo-600 dark:text-indigo-400">storage/app/brands.json</code>
            </div>
        </div>
    </div>
@endif
