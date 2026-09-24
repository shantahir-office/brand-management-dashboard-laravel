{{-- Delete Brand Confirmation Modal using TW Elements --}}

<div
    data-twe-modal-init
    class="fixed left-0 top-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300"
    id="deleteBrandModal"
    tabindex="-1"
    aria-labelledby="deleteBrandModalLabel"
    aria-hidden="true">
    
    <div
        data-twe-modal-dialog-ref
        class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-20 min-[576px]:max-w-[460px] px-4">
        
        <div class="pointer-events-auto relative flex w-full flex-col rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl outline-none">
            {{-- Modal Body --}}
            <div class="p-6 text-center">
                <div class="mx-auto w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1" id="deleteBrandModalLabel">
                    Delete Brand
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-2">
                    Are you sure you want to permanently delete <strong id="deleteBrandName" class="text-slate-800 dark:text-slate-200">this brand</strong>?
                </p>
                <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium">
                    This action will remove the record from <code class="font-mono bg-rose-50 dark:bg-rose-950/60 px-1 py-0.5 rounded">storage/app/brands.json</code>.
                </p>
            </div>

            {{-- Modal Footer Actions --}}
            <form id="deleteBrandForm" action="" method="POST" class="flex items-center justify-center space-x-3 rounded-b-2xl border-t border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90 px-6 py-4">
                @csrf
                @method('DELETE')

                <button
                    type="button"
                    data-twe-modal-dismiss
                    class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800 rounded-lg transition">
                    Cancel
                </button>
                <button
                    type="submit"
                    data-twe-ripple-init
                    class="px-5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 active:bg-rose-800 rounded-lg shadow-sm shadow-rose-200 dark:shadow-none transition">
                    Confirm Delete
                </button>
            </form>
        </div>
    </div>
</div>
