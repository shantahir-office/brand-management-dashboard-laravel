@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Section (Requirement 5) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Brand Management
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Manage development brands, repositories, tokens and ownership from one place.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <button
                type="button"
                data-twe-toggle="modal"
                data-twe-target="#addBrandModal"
                data-twe-ripple-init
                class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 shadow-sm shadow-indigo-200 dark:shadow-none transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>+ Add New Brand</span>
            </button>
        </div>
    </div>

    <!-- Dashboard Statistics & Data Quality (Requirement 6, 15) -->
    @include('dashboard.components.stats')

    <!-- Search & Filter Controls (Requirement 12, 13) -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/90 dark:border-slate-800 p-4 shadow-xs">
        <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            {{-- Retain current category filter if set --}}
            @if(!empty($filters['category']))
                <input type="hidden" name="category" value="{{ $filters['category'] }}">
            @endif

            {{-- Search Bar (searches Brand Name, Owner, Repo) --}}
            <div class="lg:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    id="filter-search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search brand name, owner, or repo link..."
                    class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-slate-50/50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500">
            </div>

            {{-- Status Filter --}}
            <div class="lg:col-span-3">
                <select
                    name="status"
                    id="filter-status"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>
                            {{ $st }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Owner Filter --}}
            <div class="lg:col-span-2">
                <select
                    name="owner"
                    id="filter-owner"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                    <option value="">All Owners</option>
                    @foreach($owners as $own)
                        <option value="{{ $own }}" {{ ($filters['owner'] ?? '') === $own ? 'selected' : '' }}>
                            {{ $own }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit & Clear Buttons --}}
            <div class="lg:col-span-2 flex items-center gap-2">
                <button
                    type="submit"
                    class="flex-1 px-4 py-2 text-xs font-semibold rounded-lg text-white bg-slate-800 hover:bg-slate-900 dark:bg-indigo-600 dark:hover:bg-indigo-700 transition">
                    Filter
                </button>
                @if(!empty($filters['search']) || !empty($filters['status']) || !empty($filters['owner']) || !empty($filters['category']))
                    <a
                        href="{{ route('dashboard') }}"
                        title="Clear all filters"
                        class="px-2.5 py-2 text-xs font-semibold rounded-lg text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Category Tabs Navigation (Requirement 4, 7) -->
    @include('dashboard.components.category-tabs')

    <!-- Responsive Brand Table (Requirement 8, 14) -->
    @include('dashboard.components.brand-table')
</div>
@endsection
