{{-- Flash Alerts & Notifications (Beginner-friendly Blade partial) --}}

@if(session('success'))
<div class="mb-6 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs" role="alert" id="successToast">
    <div class="flex items-center space-x-3">
        <div class="p-1 rounded-full bg-emerald-100 text-emerald-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    <button type="button" onclick="this.closest('#successToast').remove()" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg hover:bg-emerald-100/50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

@if(session('error'))
<div class="mb-6 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs" role="alert" id="errorToast">
    <div class="flex items-center space-x-3">
        <div class="p-1 rounded-full bg-rose-100 text-rose-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    </div>
    <button type="button" onclick="this.closest('#errorToast').remove()" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-100/50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
@endif

@if(isset($errors) && $errors->any())
<div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl shadow-xs" role="alert">
    <div class="flex items-start space-x-3">
        <div class="p-1 rounded-full bg-amber-100 text-amber-600 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold">Please check the form inputs:</h4>
            <ul class="mt-1 text-xs space-y-1 text-amber-800 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
