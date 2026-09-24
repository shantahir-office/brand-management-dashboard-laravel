<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;

/*
|--------------------------------------------------------------------------
| Web Routes for Brand Management Dashboard
|--------------------------------------------------------------------------
|
| Simple, clean, beginner-friendly route declarations.
|
*/

// Main Dashboard view (supports GET query parameters: ?search=...&category=...&status=...&owner=...)
Route::get('/', [BrandController::class, 'index'])->name('dashboard');

// Store a new brand into storage/app/brands.json
Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');

// Update an existing brand in storage/app/brands.json
Route::put('/brands/{id}', [BrandController::class, 'update'])->name('brands.update');

// Bulk update multiple brands (custom status field, status, owner)
Route::post('/brands/bulk-update', [BrandController::class, 'bulkUpdate'])->name('brands.bulkUpdate');

// Delete an existing brand from storage/app/brands.json
Route::delete('/brands/{id}', [BrandController::class, 'destroy'])->name('brands.destroy');
