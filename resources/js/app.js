/**
 * Brand Management Dashboard - Plain JavaScript Application Entry
 * 
 * Uses TW Elements 2.0 for Bootstrap-style components (Modal, Ripple, Tab, Dropdown).
 * Supports full Dark/Light mode, multi-category bulk actions, and custom status tracking.
 */

import {
    Modal,
    Ripple,
    Tab,
    Dropdown,
    initTWE
} from 'tw-elements';

// Initialize TW Elements components on DOM load
initTWE({ Modal, Ripple, Tab, Dropdown });

// Default categories matching Laravel BrandService.php
export const CATEGORIES = [
    'TM Brands',
    'FTP Details',
    'Design Brands',
    'Books Brands / POS',
    'OC Brands',
    'DBA Brands',
    'HR Brand',
    'PPC Brands',
];

// Current client-side filter state
let currentFilters = {
    category: 'all', // 'all', 'custom_manager', or any category from CATEGORIES
    search: '',
    status: '',
    owner: '',
    laravelOnly: false,
    customStatus: 'all', // 'all', 'Updated', 'Not Updated'
};

let allBrandsData = [];
let selectedBrandIds = new Set();

/**
 * Toggle Light and Dark Mode across the entire project.
 */
window.toggleTheme = function () {
    const isDark = document.documentElement.classList.contains('dark');
    if (isDark) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        updateThemeUI('light');
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        updateThemeUI('dark');
    }
};

function updateThemeUI(theme) {
    const themeTexts = document.querySelectorAll('#theme-text');
    themeTexts.forEach(el => {
        el.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
    });
}

/**
 * Toggle Mobile Sidebar / Side Panel.
 */
window.toggleSidebar = function () {
    const sidebar = document.getElementById('app-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!sidebar) return;

    const isOpen = sidebar.classList.contains('translate-x-0');
    if (isOpen) {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
    } else {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        if (backdrop) backdrop.classList.remove('hidden');
    }
};

/**
 * Quick Filter Status from Sidebar.
 */
window.quickFilterStatus = function (status) {
    currentFilters.status = status;
    const statusSelect = document.getElementById('filter-status');
    if (statusSelect) statusSelect.value = status;
    updateDashboard();

    // Close mobile sidebar if open
    if (window.innerWidth < 1024) {
        window.toggleSidebar();
    }
};

/**
 * Quick Filter Laravel Only from Sidebar.
 */
window.quickFilterLaravel = function () {
    currentFilters.laravelOnly = !currentFilters.laravelOnly;
    updateDashboard();

    // Close mobile sidebar if open
    if (window.innerWidth < 1024) {
        window.toggleSidebar();
    }
};

/**
 * Show a clean toast notification.
 */
export function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const isSuccess = type === 'success';
    const bgClass = isSuccess 
        ? 'bg-emerald-50 dark:bg-emerald-950/80 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200' 
        : 'bg-rose-50 dark:bg-rose-950/80 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200';
    const iconBg = isSuccess 
        ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400' 
        : 'bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400';

    const toast = document.createElement('div');
    toast.className = `flex items-center justify-between p-4 ${bgClass} border rounded-xl shadow-xs transition-all duration-300`;
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="p-1 rounded-full ${iconBg}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${isSuccess
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                    }
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold">${message}</p>
            </div>
        </div>
        <button type="button" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100/50 dark:hover:bg-slate-800/50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;

    toast.querySelector('button').addEventListener('click', () => toast.remove());
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}

/**
 * Calculate dynamic statistics from the brands array.
 */
function calculateStats(brands) {
    const stats = {
        total: brands.length,
        active: 0,
        development: 0,
        completed: 0,
        pending: 0,
        on_hold: 0,
        laravel: 0,
        missing_token: 0,
        missing_repo: 0,
        missing_owner: 0,
        categoryCounts: {},
        customUpdated: 0,
        customNotUpdated: 0,
    };

    CATEGORIES.forEach(c => {
        stats.categoryCounts[c] = 0;
    });

    brands.forEach(b => {
        const st = (b.status || '').toLowerCase();
        if (st === 'active') stats.active++;
        else if (st === 'development') stats.development++;
        else if (st === 'completed') stats.completed++;
        else if (st === 'pending') stats.pending++;
        else if (st === 'on hold') stats.on_hold++;

        if (b.laravel) stats.laravel++;
        if (!b.token) stats.missing_token++;
        if (!b.repo_link) stats.missing_repo++;
        if (!b.owner) stats.missing_owner++;

        const cat = b.category || 'TM Brands';
        if (stats.categoryCounts[cat] !== undefined) {
            stats.categoryCounts[cat]++;
        } else {
            stats.categoryCounts[cat] = 1;
        }

        // Custom field / Number update stats
        if (b.custom_status === 'Updated') {
            stats.customUpdated++;
        } else {
            stats.customNotUpdated++;
        }
    });

    return stats;
}

/**
 * Render the dashboard statistics cards & sidebar counters.
 */
function renderStatsCards(stats) {
    const totalEl = document.getElementById('stat-total');
    const activeEl = document.getElementById('stat-active');
    const activePctEl = document.getElementById('stat-active-pct');
    const devEl = document.getElementById('stat-development');
    const completedEl = document.getElementById('stat-completed');
    const laravelEl = document.getElementById('stat-laravel');
    const missingTokenEl = document.getElementById('stat-missing-token');
    const missingRepoEl = document.getElementById('stat-missing-repo');

    if (totalEl) totalEl.textContent = stats.total;
    if (activeEl) activeEl.textContent = stats.active;
    if (activePctEl) {
        const pct = stats.total > 0 ? Math.round((stats.active / stats.total) * 100) : 0;
        activePctEl.textContent = `${pct}% of total`;
    }
    if (devEl) devEl.textContent = stats.development;
    if (completedEl) completedEl.textContent = stats.completed;
    if (laravelEl) laravelEl.textContent = stats.laravel;
    if (missingTokenEl) missingTokenEl.textContent = stats.missing_token;
    if (missingRepoEl) missingRepoEl.textContent = stats.missing_repo;

    // Sidebar quick filter counters
    const sbAll = document.getElementById('sidebar-count-all');
    const sbActive = document.getElementById('sidebar-count-active');
    const sbDev = document.getElementById('sidebar-count-dev');
    const sbCompleted = document.getElementById('sidebar-count-completed');
    const sbLaravel = document.getElementById('sidebar-count-laravel');

    if (sbAll) sbAll.textContent = stats.total;
    if (sbActive) sbActive.textContent = stats.active;
    if (sbDev) sbDev.textContent = stats.development;
    if (sbCompleted) sbCompleted.textContent = stats.completed;
    if (sbLaravel) sbLaravel.textContent = stats.laravel;

    // Sidebar category counters
    const catBadges = document.querySelectorAll('.sidebar-cat-count');
    catBadges.forEach(badge => {
        const catName = badge.getAttribute('data-cat');
        if (catName && stats.categoryCounts[catName] !== undefined) {
            badge.textContent = stats.categoryCounts[catName];
        }
    });

    // Update Custom Status Tracker in Hero Panel
    updateCustomStatusTracker(stats);
}

/**
 * Update the Custom Status / Number Update Tracker card and Quick Pills.
 */
function updateCustomStatusTracker(stats) {
    const bar = document.getElementById('custom-status-progress-bar');
    const pctEl = document.getElementById('custom-status-progress-pct');
    const updatedCountEl = document.getElementById('custom-status-updated-count');
    const pendingCountEl = document.getElementById('custom-status-pending-count');
    const totalCountEl = document.getElementById('custom-status-total-count');
    const tmPill = document.getElementById('tm-brand-count-pill');

    const total = stats.total || allBrandsData.length || 0;
    const updated = stats.customUpdated || 0;
    const pending = total - updated;
    const pct = total > 0 ? Math.round((updated / total) * 100) : 0;

    if (bar) bar.style.width = `${pct}%`;
    if (pctEl) pctEl.textContent = `${pct}%`;
    if (updatedCountEl) updatedCountEl.textContent = updated;
    if (pendingCountEl) pendingCountEl.textContent = pending;
    if (totalCountEl) totalCountEl.textContent = total;

    if (tmPill) {
        const tmCount = stats.categoryCounts['TM Brands'] || 0;
        tmPill.textContent = tmCount;
    }
}

/**
 * Render dynamic category tabs with counts, including the Custom Status Manager tab.
 */
function renderCategoryTabs(stats) {
    const tabsContainer = document.getElementById('category-tabs-nav');
    if (!tabsContainer) return;

    const isAllActive = currentFilters.category === 'all';
    const isCustomManagerActive = currentFilters.category === 'custom_manager';

    let html = `
        <!-- All Categories Tab -->
        <button
            type="button"
            onclick="setCategoryFilter('all')"
            class="inline-flex items-center gap-2 py-3 px-3.5 text-xs sm:text-sm font-medium rounded-t-lg transition border-b-2 ${isAllActive ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40 font-semibold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700'}">
            <span>All Categories</span>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full ${isAllActive ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'}">
                ${stats.total}
            </span>
        </button>

        <!-- Custom Status Manager Tab (User Requested) -->
        <button
            type="button"
            onclick="setCategoryFilter('custom_manager')"
            class="inline-flex items-center gap-2 py-3 px-3.5 text-xs sm:text-sm font-medium rounded-t-lg transition border-b-2 ${isCustomManagerActive ? 'border-violet-600 text-violet-700 dark:text-violet-300 bg-violet-50/70 dark:bg-violet-950/50 font-bold' : 'border-transparent text-violet-600/80 dark:text-violet-400/80 hover:text-violet-800 dark:hover:text-violet-300 hover:border-violet-300 dark:hover:border-violet-700'}">
            <span class="flex items-center gap-1.5">
                <span class="text-sm">⚡</span>
                <span>Custom Status Manager</span>
            </span>
            <span class="inline-flex items-center justify-center px-2 py-0.5 text-[11px] font-bold rounded-full ${isCustomManagerActive ? 'bg-violet-200 dark:bg-violet-900 text-violet-900 dark:text-violet-100 shadow-2xs' : 'bg-violet-100/70 dark:bg-violet-950/70 text-violet-700 dark:text-violet-300'}">
                All Brands Check & Bulk Edit
            </span>
        </button>
    `;

    CATEGORIES.forEach(cat => {
        const isActive = currentFilters.category === cat;
        const count = stats.categoryCounts[cat] || 0;
        html += `
            <button
                type="button"
                onclick="setCategoryFilter('${cat}')"
                class="inline-flex items-center gap-2 py-3 px-3.5 text-xs sm:text-sm font-medium rounded-t-lg transition border-b-2 ${isActive ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-950/40 font-semibold' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700'}">
                <span>${cat}</span>
                <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full ${isActive ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'}">
                    ${count}
                </span>
            </button>
        `;
    });

    tabsContainer.innerHTML = html;

    // Highlight active item in sidebar
    const sidebarNavItems = document.querySelectorAll('.sidebar-nav-item');
    sidebarNavItems.forEach(item => {
        const itemCat = item.getAttribute('data-sidebar-category');
        if (itemCat === currentFilters.category) {
            item.className = 'sidebar-nav-item w-full flex items-center justify-between px-3 py-2 text-xs font-semibold rounded-xl transition text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50';
        } else {
            item.className = 'sidebar-nav-item w-full flex items-center justify-between px-3 py-2 text-xs font-medium rounded-xl transition text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800';
        }
    });

    // Toggle the Hero Custom Manager Panel
    const customPanel = document.getElementById('custom-manager-panel');
    if (customPanel) {
        if (isCustomManagerActive) {
            customPanel.classList.remove('hidden');
        } else {
            customPanel.classList.add('hidden');
        }
    }
}

/**
 * Render Owner filter dropdown options dynamically.
 */
function renderOwnerDropdown(brands) {
    const select = document.getElementById('filter-owner');
    if (!select) return;

    const owners = Array.from(new Set(brands.map(b => b.owner).filter(Boolean))).sort();
    const currentVal = select.value;

    let html = '<option value="">All Owners</option>';
    owners.forEach(owner => {
        html += `<option value="${escapeHtml(owner)}" ${currentVal === owner ? 'selected' : ''}>${escapeHtml(owner)}</option>`;
    });

    select.innerHTML = html;
}

/**
 * Escape string for HTML injection safety.
 */
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Render stylish technology badge (Laravel, PHP, React, HTML, Next.js).
 */
export function getTechnologyBadge(tech) {
    const t = (tech || 'Laravel').trim();
    const lower = t.toLowerCase();
    if (lower === 'laravel') {
        return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">Laravel</span>`;
    }
    if (lower === 'php') {
        return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">PHP</span>`;
    }
    if (lower === 'react') {
        return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">React</span>`;
    }
    if (lower === 'html') {
        return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">HTML</span>`;
    }
    if (lower === 'next.js' || lower === 'nextjs') {
        return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700">Next.js</span>`;
    }
    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">${escapeHtml(t)}</span>`;
}

/**
 * Filter brands based on current filters.
 */
function getFilteredBrands() {
    return allBrandsData.filter(brand => {
        // Category filter: 'custom_manager' shows all brands across every category!
        if (currentFilters.category !== 'all' && currentFilters.category !== 'custom_manager') {
            if (brand.category !== currentFilters.category) {
                return false;
            }
        }

        // Custom status filter in custom manager mode
        if (currentFilters.customStatus && currentFilters.customStatus !== 'all') {
            if (brand.custom_status !== currentFilters.customStatus) {
                return false;
            }
        }

        // Standard Status filter
        if (currentFilters.status && brand.status !== currentFilters.status) {
            return false;
        }

        // Laravel only filter
        if (currentFilters.laravelOnly && !brand.laravel) {
            return false;
        }

        // Owner filter
        if (currentFilters.owner && brand.owner !== currentFilters.owner) {
            return false;
        }

        // Search filter (Brand Name, Owner, Repo, Repo Owner, Custom Status, Technology)
        if (currentFilters.search) {
            const q = currentFilters.search.toLowerCase();
            const name = (brand.brand_name || '').toLowerCase();
            const owner = (brand.owner || '').toLowerCase();
            const repoOwner = (brand.repo_owner || '').toLowerCase();
            const repo = (brand.repo_link || '').toLowerCase();
            const customStatus = (brand.custom_status || '').toLowerCase();
            const customField = (brand.custom_field_name || '').toLowerCase();
            const tech = (brand.technology || (brand.laravel ? 'Laravel' : 'PHP')).toLowerCase();
            if (!name.includes(q) && !owner.includes(q) && !repoOwner.includes(q) && !repo.includes(q) && !customStatus.includes(q) && !customField.includes(q) && !tech.includes(q)) {
                return false;
            }
        }

        return true;
    });
}

/**
 * Render the Brand Table rows or Empty State with full dark mode support.
 */
function renderTable() {
    const tableContainer = document.getElementById('table-container');
    const emptyState = document.getElementById('empty-state');
    const tbody = document.getElementById('brands-tbody');
    const countDisplay = document.getElementById('showing-count');
    const catDisplay = document.getElementById('showing-category');
    const masterCheckbox = document.getElementById('master-select-checkbox');

    if (!tbody || !tableContainer || !emptyState) return;

    const filtered = getFilteredBrands();

    if (filtered.length === 0) {
        tableContainer.classList.add('hidden');
        emptyState.classList.remove('hidden');
        updateBulkActionBar();
        return;
    }

    tableContainer.classList.remove('hidden');
    emptyState.classList.add('hidden');

    tbody.innerHTML = filtered.map(brand => {
        // Standard Status badge styling
        const st = (brand.status || 'Pending').toLowerCase();
        let badgeColor = 'bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border-amber-200/80 dark:border-amber-800';
        let dotColor = 'bg-amber-500';

        if (st === 'active') {
            badgeColor = 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800';
            dotColor = 'bg-emerald-500';
        } else if (st === 'development') {
            badgeColor = 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200/80 dark:border-blue-800';
            dotColor = 'bg-blue-500';
        } else if (st === 'completed') {
            badgeColor = 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200/80 dark:border-purple-800';
            dotColor = 'bg-purple-500';
        } else if (st === 'on hold') {
            badgeColor = 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-300 dark:border-slate-700';
            dotColor = 'bg-slate-400';
        }

        // Custom Status styling
        const cs = brand.custom_status || 'Not Set';
        const csField = brand.custom_field_name || 'Number Update';
        let csBadgeClass = 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700';
        let csDotClass = 'bg-slate-400';

        if (cs === 'Updated') {
            csBadgeClass = 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800 font-semibold';
            csDotClass = 'bg-emerald-500';
        } else if (cs === 'Not Updated') {
            csBadgeClass = 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200/80 dark:border-rose-800 font-semibold';
            csDotClass = 'bg-rose-500';
        } else if (cs === 'In Progress') {
            csBadgeClass = 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200/80 dark:border-blue-800 font-semibold';
            csDotClass = 'bg-blue-500 animate-pulse';
        } else if (cs === 'Pending') {
            csBadgeClass = 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200/80 dark:border-amber-800 font-semibold';
            csDotClass = 'bg-amber-500';
        }

        const isChecked = selectedBrandIds.has(Number(brand.id));
        const brandJson = escapeHtml(JSON.stringify(brand));

        return `
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors group ${isChecked ? 'bg-indigo-50/40 dark:bg-indigo-950/30' : ''}" id="brand-row-${brand.id}">
                <!-- 0. Row Checkbox -->
                <td class="py-4 pl-4 sm:pl-6 pr-2 w-10 text-center">
                    <input
                        type="checkbox"
                        class="brand-select-checkbox w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-800 dark:checked:bg-indigo-600 cursor-pointer transition"
                        value="${brand.id}"
                        ${isChecked ? 'checked' : ''}
                        onchange="handleBrandCheckboxChange(${brand.id}, this.checked)">
                </td>

                <!-- 1. Brand Name & Category -->
                <td class="py-4 px-3">
                    <div class="font-semibold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition flex items-center gap-2">
                        <span>${escapeHtml(brand.brand_name)}</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                        <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">
                            ${escapeHtml(brand.category)}
                        </span>
                        ${brand.notes ? `<span class="truncate max-w-[200px] text-slate-400 dark:text-slate-500" title="${escapeHtml(brand.notes)}">&bull; ${escapeHtml(brand.notes)}</span>` : ''}
                    </div>
                </td>

                <!-- 2. Status Badge -->
                <td class="py-4 px-3 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${badgeColor}">
                        <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>
                        ${escapeHtml(brand.status || 'Pending')}
                    </span>
                </td>

                <!-- 3. Custom Field / Status (Number Update) with 1-click Toggle -->
                <td class="py-4 px-3 whitespace-nowrap">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs border ${csBadgeClass}">
                            <span class="w-1.5 h-1.5 rounded-full ${csDotClass}"></span>
                            <span class="text-[11px] font-medium opacity-80">${escapeHtml(csField)}:</span>
                            <strong class="font-bold">${escapeHtml(cs)}</strong>
                        </span>

                        <!-- Quick Inline Toggle Button -->
                        <button
                            type="button"
                            onclick="toggleSingleBrandCustomStatus(${brand.id})"
                            title="Click to toggle between Updated and Not Updated"
                            class="p-1 rounded-md text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-[11px]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </td>

                <!-- 4. Owner -->
                <td class="py-4 px-3 whitespace-nowrap">
                    ${brand.owner ? `
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold text-[10px] flex items-center justify-center uppercase">
                                ${escapeHtml(brand.owner.charAt(0))}
                            </div>
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">${escapeHtml(brand.owner)}</span>
                        </div>
                    ` : `
                        <span class="inline-flex items-center text-xs text-amber-600 dark:text-amber-400 font-medium">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Missing Owner
                        </span>
                    `}
                </td>

                <!-- 5. Technology Badge -->
                <td class="py-4 px-3 whitespace-nowrap text-center">
                    ${getTechnologyBadge(brand.technology || (brand.laravel ? 'Laravel' : 'PHP'))}
                </td>

                <!-- 6. Token Masked with Eye toggle -->
                <td class="py-4 px-3 whitespace-nowrap">
                    ${brand.token ? `
                        <div class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700 text-xs font-mono text-slate-600 dark:text-slate-300" id="token-box-${brand.id}">
                            <span class="token-hidden tracking-widest text-slate-500 dark:text-slate-400 font-bold select-none">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                            <span class="token-raw hidden text-slate-800 dark:text-slate-100">${escapeHtml(brand.token)}</span>
                            <button
                                type="button"
                                onclick="toggleTokenDisplay(${brand.id})"
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
                    ` : `
                        <span class="inline-flex items-center text-xs text-amber-600 dark:text-amber-400 font-medium">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Missing Token
                        </span>
                    `}
                </td>

                <!-- 7. Repository Link & Owner -->
                <td class="py-4 px-3 whitespace-nowrap">
                    <div class="flex flex-col gap-1 items-start">
                        ${brand.repo_link ? `
                            <a href="${escapeHtml(brand.repo_link)}"
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
                        ` : `
                            <span class="inline-flex items-center text-xs text-rose-600 dark:text-rose-400 font-medium">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Missing Repo
                            </span>
                        `}
                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-medium bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700" title="Repository Owner">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>${escapeHtml(brand.repo_owner || 'ahmedzafar-devTeam')}</span>
                        </div>
                    </div>
                </td>

                <!-- 8. Actions -->
                <td class="py-4 px-4 sm:px-6 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end space-x-2">
                        <button
                            type="button"
                            onclick='openEditBrandModal(${brandJson})'
                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 rounded-lg shadow-2xs transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                        <button
                            type="button"
                            onclick="openDeleteBrandModal(${brand.id}, '${escapeHtml(brand.brand_name.replace(/'/g, "\\'"))}')"
                            class="inline-flex items-center px-2 py-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-white dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-lg shadow-2xs transition focus:outline-none focus:ring-2 focus:ring-rose-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span class="sr-only">Delete</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    if (countDisplay) countDisplay.textContent = filtered.length;
    if (catDisplay) {
        if (currentFilters.category === 'custom_manager') {
            catDisplay.textContent = 'Custom Status Manager (All Categories)';
        } else if (currentFilters.category === 'all') {
            catDisplay.textContent = 'All Categories';
        } else {
            catDisplay.textContent = currentFilters.category;
        }
    }

    // Sync master checkbox state
    if (masterCheckbox) {
        const visibleIds = filtered.map(b => Number(b.id));
        const allVisibleChecked = visibleIds.length > 0 && visibleIds.every(id => selectedBrandIds.has(id));
        const someVisibleChecked = visibleIds.some(id => selectedBrandIds.has(id));

        masterCheckbox.checked = allVisibleChecked;
        masterCheckbox.indeterminate = !allVisibleChecked && someVisibleChecked;
    }

    updateBulkActionBar();
}

/**
 * Handle individual brand row checkbox change.
 */
window.handleBrandCheckboxChange = function (brandId, checked) {
    const numId = Number(brandId);
    if (checked) {
        selectedBrandIds.add(numId);
    } else {
        selectedBrandIds.delete(numId);
    }

    // Update row highlighting directly
    const row = document.getElementById(`brand-row-${numId}`);
    if (row) {
        if (checked) {
            row.classList.add('bg-indigo-50/40', 'dark:bg-indigo-950/30');
        } else {
            row.classList.remove('bg-indigo-50/40', 'dark:bg-indigo-950/30');
        }
    }

    updateBulkActionBar();

    // Update master checkbox
    const filtered = getFilteredBrands();
    const masterCheckbox = document.getElementById('master-select-checkbox');
    if (masterCheckbox && filtered.length > 0) {
        const visibleIds = filtered.map(b => Number(b.id));
        const allVisibleChecked = visibleIds.every(id => selectedBrandIds.has(id));
        const someVisibleChecked = visibleIds.some(id => selectedBrandIds.has(id));
        masterCheckbox.checked = allVisibleChecked;
        masterCheckbox.indeterminate = !allVisibleChecked && someVisibleChecked;
    }
};

/**
 * Toggle select all brands in the current table view.
 */
window.toggleSelectAllBrands = function (checked) {
    const filtered = getFilteredBrands();
    filtered.forEach(brand => {
        const numId = Number(brand.id);
        if (checked) {
            selectedBrandIds.add(numId);
        } else {
            selectedBrandIds.delete(numId);
        }
    });

    renderTable();
};

/**
 * Select all brands in the current view.
 */
window.selectAllFilteredBrands = function () {
    const filtered = getFilteredBrands();
    filtered.forEach(b => selectedBrandIds.add(Number(b.id)));
    renderTable();
    showToast(`Selected all ${filtered.length} brand(s) in view.`, 'success');
};

/**
 * Select all brands belonging to a specific category (e.g. 'TM Brands').
 */
window.selectBrandsByCategory = function (categoryName) {
    let count = 0;
    allBrandsData.forEach(brand => {
        if (brand.category === categoryName) {
            selectedBrandIds.add(Number(brand.id));
            count++;
        }
    });
    renderTable();
    showToast(`Selected all ${count} brands from "${categoryName}".`, 'success');
};

/**
 * Invert brand selection among filtered brands.
 */
window.invertBrandSelection = function () {
    const filtered = getFilteredBrands();
    filtered.forEach(b => {
        const numId = Number(b.id);
        if (selectedBrandIds.has(numId)) {
            selectedBrandIds.delete(numId);
        } else {
            selectedBrandIds.add(numId);
        }
    });
    renderTable();
};

/**
 * Clear all brand selections.
 */
window.clearBrandSelection = function () {
    selectedBrandIds.clear();
    const masterCheckbox = document.getElementById('master-select-checkbox');
    if (masterCheckbox) {
        masterCheckbox.checked = false;
        masterCheckbox.indeterminate = false;
    }
    renderTable();
};

/**
 * Update the floating bulk actions bar state & counters.
 */
function updateBulkActionBar() {
    const bar = document.getElementById('bulk-action-bar');
    const countEl = document.getElementById('bulk-selected-count');
    const namesEl = document.getElementById('bulk-selected-names');

    if (!bar) return;

    const count = selectedBrandIds.size;
    if (count > 0) {
        bar.classList.remove('hidden');
        if (countEl) countEl.textContent = count;

        if (namesEl) {
            const selectedNames = allBrandsData
                .filter(b => selectedBrandIds.has(Number(b.id)))
                .map(b => b.brand_name);
            if (selectedNames.length <= 3) {
                namesEl.textContent = `(${selectedNames.join(', ')})`;
            } else {
                namesEl.textContent = `(${selectedNames.slice(0, 3).join(', ')} + ${selectedNames.length - 3} more)`;
            }
        }
    } else {
        bar.classList.add('hidden');
    }
}

/**
 * Quick batch set custom status for all selected brands (e.g. Number Update -> Updated / Not Updated).
 */
window.quickBatchSetCustomStatus = async function (fieldName, statusValue) {
    if (selectedBrandIds.size === 0) {
        showToast('Please check at least one brand first.', 'error');
        return;
    }

    const ids = Array.from(selectedBrandIds);
    const count = ids.length;

    try {
        const res = await fetch('/api/brands/bulk', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ids,
                custom_field_name: fieldName,
                custom_status: statusValue,
            }),
        });

        if (res.ok) {
            const result = await res.json();
            // Update local memory data
            allBrandsData.forEach(brand => {
                if (ids.includes(Number(brand.id))) {
                    brand.custom_field_name = fieldName;
                    brand.custom_status = statusValue;
                }
            });

            updateDashboard();
            showToast(`Batch updated ${count} brand(s): ${fieldName} is now "${statusValue}".`, 'success');
        } else {
            showToast('Failed to apply bulk update.', 'error');
        }
    } catch (err) {
        showToast('Error during bulk update: ' + err.message, 'error');
    }
};

/**
 * 1-click toggle single brand custom status in table row.
 */
window.toggleSingleBrandCustomStatus = async function (brandId) {
    const numId = Number(brandId);
    const brand = allBrandsData.find(b => Number(b.id) === numId);
    if (!brand) return;

    const currentStatus = brand.custom_status || 'Not Set';
    const newStatus = currentStatus === 'Updated' ? 'Not Updated' : 'Updated';
    const fieldName = brand.custom_field_name || 'Number Update';

    try {
        const res = await fetch('/api/brands', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                ...brand,
                id: numId,
                custom_field_name: fieldName,
                custom_status: newStatus,
            }),
        });

        if (res.ok) {
            brand.custom_status = newStatus;
            brand.custom_field_name = fieldName;
            updateDashboard();
            showToast(`"${brand.brand_name}": ${fieldName} marked as "${newStatus}".`, 'success');
        } else {
            showToast('Failed to update brand status.', 'error');
        }
    } catch (err) {
        showToast('Error updating brand: ' + err.message, 'error');
    }
};

/**
 * Filter table by custom status pills in Hero panel.
 */
window.filterByCustomStatus = function (status) {
    currentFilters.customStatus = status;

    // Highlight button pills
    const btnAll = document.getElementById('btn-filter-cs-all');
    const btnUpdated = document.getElementById('btn-filter-cs-updated');
    const btnNotUpdated = document.getElementById('btn-filter-cs-not-updated');

    const activeClass = 'px-2 py-0.5 rounded-md font-semibold bg-violet-600 text-white text-xs transition shadow-2xs';
    const inactiveClass = 'px-2 py-0.5 rounded-md font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 text-xs transition';

    if (btnAll) btnAll.className = status === 'all' ? activeClass : inactiveClass;
    if (btnUpdated) btnUpdated.className = status === 'Updated' ? activeClass : inactiveClass;
    if (btnNotUpdated) btnNotUpdated.className = status === 'Not Updated' ? activeClass : inactiveClass;

    renderTable();
};

/**
 * Open Bulk Status Modal.
 */
window.openBulkStatusModal = function () {
    if (selectedBrandIds.size === 0) {
        showToast('Please check at least one brand first.', 'error');
        return;
    }

    const modalCount = document.getElementById('bulk-modal-count');
    const brandPills = document.getElementById('bulk-modal-brand-pills');

    const selectedBrands = allBrandsData.filter(b => selectedBrandIds.has(Number(b.id)));

    if (modalCount) modalCount.textContent = `${selectedBrands.length} brands`;
    if (brandPills) {
        brandPills.innerHTML = selectedBrands.map(b => `
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200">
                <span class="w-1.5 h-1.5 rounded-full bg-violet-500"></span>
                <span>${escapeHtml(b.brand_name)}</span>
            </span>
        `).join('');
    }

    const modalEl = document.getElementById('bulkEditModal');
    if (modalEl) {
        const modalInstance = Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    }
};

/**
 * Re-render full dashboard interface.
 */
export function updateDashboard() {
    const stats = calculateStats(allBrandsData);
    renderStatsCards(stats);
    renderCategoryTabs(stats);
    renderOwnerDropdown(allBrandsData);
    renderTable();
}

/**
 * Set active category tab.
 */
window.setCategoryFilter = function (category) {
    currentFilters.category = category;
    updateDashboard();

    // Close mobile sidebar if open
    if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && sidebar.classList.contains('translate-x-0')) {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }
    }
};

/**
 * Set and apply search text.
 */
window.handleSearchInput = function (e) {
    currentFilters.search = e.target.value.trim();
    renderTable();
};

/**
 * Set and apply status filter.
 */
window.handleStatusFilter = function (e) {
    currentFilters.status = e.target.value;
    renderTable();
};

/**
 * Set and apply owner filter.
 */
window.handleOwnerFilter = function (e) {
    currentFilters.owner = e.target.value;
    renderTable();
};

/**
 * Reset all filters.
 */
window.resetFilters = function () {
    currentFilters = {
        category: 'all',
        search: '',
        status: '',
        owner: '',
        laravelOnly: false,
        customStatus: 'all',
    };
    const sInput = document.getElementById('filter-search');
    const stSelect = document.getElementById('filter-status');
    const ownSelect = document.getElementById('filter-owner');
    if (sInput) sInput.value = '';
    if (stSelect) stSelect.value = '';
    if (ownSelect) ownSelect.value = '';
    updateDashboard();
};

/**
 * Fetch initial brand data from storage/app/brands.json via API bridge.
 */
export async function loadBrands() {
    try {
        const res = await fetch('/api/brands');
        if (res.ok) {
            const data = await res.json();
            allBrandsData = data.brands || [];
            updateDashboard();
        }
    } catch (err) {
        console.warn('Could not load brands via /api/brands, using fallback embedded data:', err);
    }
}

/**
 * Open the Edit Brand Modal and prefill data.
 */
window.openEditBrandModal = function (brand) {
    const idInput = document.getElementById('edit_brand_id');
    const nameInput = document.getElementById('edit_brand_name');
    const categorySelect = document.getElementById('edit_category');
    const statusSelect = document.getElementById('edit_status');
    const ownerInput = document.getElementById('edit_owner');
    const tokenInput = document.getElementById('edit_token');
    const repoInput = document.getElementById('edit_repo_link');
    const repoOwnerSelect = document.getElementById('edit_repo_owner');
    const ftpInput = document.getElementById('edit_ftp_details');
    const notesInput = document.getElementById('edit_notes');
    const techSelect = document.getElementById('edit_technology');
    const laravelCheck = document.getElementById('edit_laravel');
    const customFieldInput = document.getElementById('edit_custom_field_name');
    const customStatusSelect = document.getElementById('edit_custom_status');

    if (idInput) idInput.value = brand.id;
    if (nameInput) nameInput.value = brand.brand_name || '';
    if (categorySelect) categorySelect.value = brand.category || 'TM Brands';
    if (statusSelect) statusSelect.value = brand.status || 'Pending';
    if (ownerInput) ownerInput.value = brand.owner || '';
    if (tokenInput) tokenInput.value = brand.token || '';
    if (repoInput) repoInput.value = brand.repo_link || '';
    if (repoOwnerSelect) repoOwnerSelect.value = brand.repo_owner || 'ahmedzafar-devTeam';
    if (ftpInput) ftpInput.value = brand.ftp_details || '';
    if (notesInput) notesInput.value = brand.notes || '';
    if (techSelect) techSelect.value = brand.technology || (brand.laravel ? 'Laravel' : 'PHP');
    if (laravelCheck) laravelCheck.checked = Boolean(brand.laravel);
    if (customFieldInput) customFieldInput.value = brand.custom_field_name || 'Number Update';
    if (customStatusSelect) customStatusSelect.value = brand.custom_status || '';

    const modalEl = document.getElementById('editBrandModal');
    if (modalEl) {
        const modalInstance = Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    }
};

/**
 * Open the Delete Confirmation Modal with brand ID and name.
 */
window.openDeleteBrandModal = function (id, name) {
    const idInput = document.getElementById('delete_brand_id');
    const nameEl = document.getElementById('deleteBrandName');

    if (idInput) idInput.value = id;
    if (nameEl) nameEl.textContent = name;

    const modalEl = document.getElementById('deleteBrandModal');
    if (modalEl) {
        const modalInstance = Modal.getOrCreateInstance(modalEl);
        modalInstance.show();
    }
};

/**
 * Toggle the visibility of a masked token in the table.
 */
window.toggleTokenDisplay = function (brandId) {
    const box = document.getElementById('token-box-' + brandId);
    if (!box) return;

    const hidden = box.querySelector('.token-hidden');
    const raw = box.querySelector('.token-raw');
    const eyeShow = box.querySelector('.eye-show');
    const eyeHide = box.querySelector('.eye-hide');

    if (!hidden || !raw || !eyeShow || !eyeHide) return;

    if (raw.classList.contains('hidden')) {
        raw.classList.remove('hidden');
        hidden.classList.add('hidden');
        eyeShow.classList.add('hidden');
        eyeHide.classList.remove('hidden');
    } else {
        raw.classList.add('hidden');
        hidden.classList.remove('hidden');
        eyeShow.classList.remove('hidden');
        eyeHide.classList.add('hidden');
    }
};

/**
 * Handle form submissions (Add, Edit, Delete, Bulk).
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial Load
    loadBrands();

    // 2. Add Brand Form Submission
    const addForm = document.getElementById('add-brand-form');
    if (addForm) {
        addForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = addForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData(addForm);
            const technologyVal = formData.get('technology') || 'Laravel';
            const payload = {
                brand_name: formData.get('brand_name'),
                category: formData.get('category'),
                status: formData.get('status'),
                owner: formData.get('owner'),
                token: formData.get('token'),
                repo_link: formData.get('repo_link'),
                repo_owner: formData.get('repo_owner') || 'ahmedzafar-devTeam',
                technology: technologyVal,
                laravel: technologyVal === 'Laravel',
                ftp_details: formData.get('ftp_details'),
                notes: formData.get('notes'),
                custom_field_name: formData.get('custom_field_name') || 'Number Update',
                custom_status: formData.get('custom_status') || null,
            };

            try {
                const res = await fetch('/api/brands', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const result = await res.json();

                if (res.ok) {
                    if (result.brand) {
                        allBrandsData.push(result.brand);
                    }
                    updateDashboard();
                    addForm.reset();

                    // Close modal
                    const modalEl = document.getElementById('addBrandModal');
                    if (modalEl) {
                        const modalInstance = Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    showToast('Brand added successfully.', 'success');
                } else {
                    showToast(result.error || 'Failed to save brand', 'error');
                }
            } catch (err) {
                showToast('Error saving brand: ' + err.message, 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

    // 3. Edit Brand Form Submission
    const editForm = document.getElementById('editBrandForm');
    if (editForm) {
        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = editForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData(editForm);
            const brandId = Number(document.getElementById('edit_brand_id')?.value);
            const technologyVal = formData.get('technology') || 'Laravel';
            const payload = {
                id: brandId,
                brand_name: formData.get('brand_name'),
                category: formData.get('category'),
                status: formData.get('status'),
                owner: formData.get('owner'),
                token: formData.get('token'),
                repo_link: formData.get('repo_link'),
                repo_owner: formData.get('repo_owner') || 'ahmedzafar-devTeam',
                technology: technologyVal,
                laravel: technologyVal === 'Laravel',
                ftp_details: formData.get('ftp_details'),
                notes: formData.get('notes'),
                custom_field_name: formData.get('custom_field_name'),
                custom_status: formData.get('custom_status'),
            };

            try {
                const res = await fetch('/api/brands', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const result = await res.json();

                if (res.ok) {
                    const idx = allBrandsData.findIndex(b => Number(b.id) === brandId);
                    if (idx !== -1 && result.brand) {
                        allBrandsData[idx] = result.brand;
                    }
                    updateDashboard();

                    // Close modal
                    const modalEl = document.getElementById('editBrandModal');
                    if (modalEl) {
                        const modalInstance = Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    showToast('Brand updated successfully.', 'success');
                } else {
                    showToast(result.error || 'Failed to update brand', 'error');
                }
            } catch (err) {
                showToast('Error updating brand: ' + err.message, 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

    // 4. Delete Brand Form Submission
    const deleteForm = document.getElementById('deleteBrandForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const brandId = Number(document.getElementById('delete_brand_id')?.value);

            try {
                const res = await fetch('/api/brands', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: brandId }),
                });

                if (res.ok) {
                    allBrandsData = allBrandsData.filter(b => Number(b.id) !== brandId);
                    selectedBrandIds.delete(brandId);
                    updateDashboard();

                    // Close modal
                    const modalEl = document.getElementById('deleteBrandModal');
                    if (modalEl) {
                        const modalInstance = Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    showToast('Brand deleted successfully.', 'success');
                } else {
                    showToast('Failed to delete brand', 'error');
                }
            } catch (err) {
                showToast('Error deleting brand: ' + err.message, 'error');
            }
        });
    }

    // 5. Bulk Edit Form Submission
    const bulkForm = document.getElementById('bulkEditForm');
    if (bulkForm) {
        bulkForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = bulkForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const ids = Array.from(selectedBrandIds);
            const customFieldName = document.getElementById('bulk_custom_field_name')?.value?.trim() || 'Number Update';
            const customStatus = document.getElementById('bulk_custom_status')?.value;
            const standardStatus = document.getElementById('bulk_status')?.value;
            const owner = document.getElementById('bulk_owner')?.value?.trim();

            const payload = {
                ids,
                custom_field_name: customFieldName,
                custom_status: customStatus,
            };
            if (standardStatus) payload.status = standardStatus;
            if (owner) payload.owner = owner;

            try {
                const res = await fetch('/api/brands/bulk', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });

                if (res.ok) {
                    const result = await res.json();
                    // Update in-memory brands
                    allBrandsData.forEach(brand => {
                        if (ids.includes(Number(brand.id))) {
                            brand.custom_field_name = customFieldName;
                            brand.custom_status = customStatus;
                            if (standardStatus) brand.status = standardStatus;
                            if (owner) brand.owner = owner;
                        }
                    });

                    updateDashboard();

                    // Close modal
                    const modalEl = document.getElementById('bulkEditModal');
                    if (modalEl) {
                        const modalInstance = Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    showToast(`Successfully updated ${ids.length} brands in bulk!`, 'success');
                } else {
                    showToast('Failed to apply bulk update', 'error');
                }
            } catch (err) {
                showToast('Error applying bulk update: ' + err.message, 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

    // Search and Filter Listeners
    const searchInput = document.getElementById('filter-search');
    if (searchInput) {
        searchInput.addEventListener('input', window.handleSearchInput);
    }

    const statusFilter = document.getElementById('filter-status');
    if (statusFilter) {
        statusFilter.addEventListener('change', window.handleStatusFilter);
    }

    const ownerFilter = document.getElementById('filter-owner');
    if (ownerFilter) {
        ownerFilter.addEventListener('change', window.handleOwnerFilter);
    }
});
