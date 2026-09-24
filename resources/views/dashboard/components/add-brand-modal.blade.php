{{-- Add Brand Modal using TW Elements --}}

<div
    data-twe-modal-init
    class="fixed left-0 top-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300"
    id="addBrandModal"
    tabindex="-1"
    aria-labelledby="addBrandModalLabel"
    aria-hidden="true">
    
    <div
        data-twe-modal-dialog-ref
        class="pointer-events-none relative w-auto translate-y-[-50px] opacity-0 transition-all duration-300 ease-in-out min-[576px]:mx-auto min-[576px]:mt-7 min-[576px]:max-w-[620px] px-4">
        
        <div class="pointer-events-auto relative flex w-full flex-col rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl outline-none">
            {{-- Modal Header --}}
            <div class="flex flex-shrink-0 items-center justify-between rounded-t-2xl border-b border-slate-100 dark:border-slate-800 px-6 py-4">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-base font-bold text-slate-900 dark:text-white" id="addBrandModalLabel">
                            Add New Brand
                        </h5>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Record a new development brand into storage/app/brands.json</p>
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
            <form action="{{ route('brands.store') }}" method="POST" id="add-brand-form">
                @csrf

                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                    {{-- Row 1: Brand Name & Category (Required) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="add_brand_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Brand Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="brand_name"
                                id="add_brand_name"
                                required
                                placeholder="e.g. Apex Global Trademark"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        </div>

                        <div>
                            <label for="add_category" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Category <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="category"
                                id="add_category"
                                required
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ (isset($filters['category']) && $filters['category'] === $cat) ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Status & Owner --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="add_status" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Status
                            </label>
                            <select
                                name="status"
                                id="add_status"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
                                @foreach($statuses as $st)
                                    <option value="{{ $st }}" {{ $st === 'Development' ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="add_owner" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Owner / Lead Developer
                            </label>
                            <input
                                type="text"
                                name="owner"
                                id="add_owner"
                                placeholder="e.g. David Chen"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        </div>
                    </div>

                    {{-- Row 3: Token --}}
                    <div>
                        <label for="add_token" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Access / API Token
                        </label>
                        <input
                            type="text"
                            name="token"
                            id="add_token"
                            placeholder="e.g. ghp_91K3jd82Akq90Lz82NmK109A"
                            class="w-full px-3 py-2 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Tokens are securely stored in storage/app/brands.json and masked in the UI.</p>
                    </div>

                    {{-- Row 4: Repository Link & Owner --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="add_repo_link" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Repository URL
                            </label>
                            <input
                                type="url"
                                name="repo_link"
                                id="add_repo_link"
                                placeholder="https://github.com/organization/repo-name"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                        </div>

                        <div>
                            <label for="add_repo_owner" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Repository Owner <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="repo_owner"
                                id="add_repo_owner"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                                <option value="ahmedzafar-devTeam" selected>ahmedzafar-devTeam</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 5: Technology Dropdown --}}
                    <div>
                        <label for="add_technology" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Technology <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="technology"
                            id="add_technology"
                            required
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                            <option value="Laravel" selected>Laravel</option>
                            <option value="PHP">PHP</option>
                            <option value="React">React</option>
                            <option value="HTML">HTML</option>
                            <option value="Next.js">Next.js</option>
                        </select>
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-slate-500">Select the framework or stack: Laravel, PHP, React, HTML, or Next.js.</p>
                    </div>

                    {{-- Row 6: FTP Details --}}
                    <div>
                        <label for="add_ftp_details" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            FTP / SFTP Details
                        </label>
                        <input
                            type="text"
                            name="ftp_details"
                            id="add_ftp_details"
                            placeholder="e.g. sftp://ftp.example.com:22 | user: deploy_user"
                            class="w-full px-3 py-2 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-900 outline-none transition">
                    </div>

                    {{-- Row 7: Notes --}}
                    <div>
                        <label for="add_notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Internal Notes & Description
                        </label>
                        <textarea
                            name="notes"
                            id="add_notes"
                            rows="2"
                            placeholder="Deployment instructions, credentials reminder, or contact info..."
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
                        Save Brand
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
