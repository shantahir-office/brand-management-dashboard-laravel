{{-- Edit Brand Modal using TW Elements --}}

<div
    data-twe-modal-init
    class="fixed left-0 top-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300"
    id="editBrandModal"
    tabindex="-1"
    aria-labelledby="editBrandModalLabel"
    aria-hidden="true">
    
    <div
        data-twe-modal-dialog-ref
        class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 min-[576px]:max-w-[620px] px-4">
        
        <div class="pointer-events-auto relative flex w-full flex-col rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl outline-none">
            {{-- Modal Header --}}
            <div class="flex flex-shrink-0 items-center justify-between rounded-t-2xl border-b border-slate-100 dark:border-slate-800 px-6 py-4">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white" id="editBrandModalLabel">
                            Edit Brand
                        </h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Update brand records in storage/app/brands.json</p>
                    </div>
                </div>

                <button
                    type="button"
                    data-twe-modal-dismiss
                    aria-label="Close"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form id="editBrandForm" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    {{-- Row 1: Brand Name & Category --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_brand_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Brand Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="brand_name"
                                id="edit_brand_name"
                                required
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        </div>

                        <div>
                            <label for="edit_category" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Category <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="category"
                                id="edit_category"
                                required
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Status & Owner --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_status" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Status
                            </label>
                            <select
                                name="status"
                                id="edit_status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}">{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="edit_owner" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Owner / Lead Developer
                            </label>
                            <input
                                type="text"
                                name="owner"
                                id="edit_owner"
                                placeholder="e.g. Sarah Jenkins"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        </div>
                    </div>

                    {{-- Row 3: Token --}}
                    <div>
                        <label for="edit_token" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Access / API Token
                        </label>
                        <input
                            type="text"
                            name="token"
                            id="edit_token"
                            placeholder="Leave blank if no token assigned yet"
                            class="w-full px-3 py-2 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                    </div>

                    {{-- Row 4: Repository Link & Owner --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_repo_link" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Repository URL
                            </label>
                            <input
                                type="url"
                                name="repo_link"
                                id="edit_repo_link"
                                placeholder="https://github.com/organization/repo-name"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        </div>

                        <div>
                            <label for="edit_repo_owner" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Repository Owner <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="repo_owner"
                                id="edit_repo_owner"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                                <option value="ahmedzafar-devTeam" selected>ahmedzafar-devTeam</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 5: Technology Dropdown --}}
                    <div>
                        <label for="edit_technology" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Technology <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="technology"
                            id="edit_technology"
                            required
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                            <option value="Laravel">Laravel</option>
                            <option value="PHP">PHP</option>
                            <option value="React">React</option>
                            <option value="HTML">HTML</option>
                            <option value="Next.js">Next.js</option>
                        </select>
                    </div>

                    {{-- Row 6: FTP Details --}}
                    <div>
                        <label for="edit_ftp_details" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            FTP / SFTP Details
                        </label>
                        <input
                            type="text"
                            name="ftp_details"
                            id="edit_ftp_details"
                            placeholder="ftp.server.internal:21 | user: my_user"
                            class="w-full px-3 py-2 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                    </div>

                    {{-- Row 7: Notes --}}
                    <div>
                        <label for="edit_notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Internal Notes & Description
                        </label>
                        <textarea
                            name="notes"
                            id="edit_notes"
                            rows="2"
                            placeholder="Optional notes or deployment instructions..."
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition"></textarea>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end space-x-3 rounded-b-2xl border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/90 px-6 py-4">
                    <button
                        type="button"
                        data-twe-modal-dismiss
                        class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-200/70 dark:hover:bg-slate-800 rounded-lg transition">
                        Cancel
                    </button>
                    <button
                        type="submit"
                        data-twe-ripple-init
                        class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm shadow-indigo-200 dark:shadow-none transition">
                        Update Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
