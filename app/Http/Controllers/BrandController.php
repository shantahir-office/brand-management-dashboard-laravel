<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BrandService;

/**
 * BrandController handles web requests for the Brand Management Dashboard.
 * 
 * Beginner-friendly controller:
 * index()   -> Displays dashboard with dynamic stats, tabs, search, and filtered brands
 * store()   -> Adds a new brand to storage/app/brands.json
 * update()  -> Updates an existing brand
 * destroy() -> Deletes a brand
 */
class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Display the main brand management dashboard.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Collect filters from query string (GET parameters)
        $filters = [
            'search'   => $request->query('search', ''),
            'category' => $request->query('category', ''),
            'status'   => $request->query('status', ''),
            'owner'    => $request->query('owner', ''),
        ];

        // Fetch brands and statistics
        $brands     = $this->brandService->getAll($filters);
        $allBrands  = $this->brandService->getAll(); // Unfiltered for full stats & category tabs
        $stats      = $this->brandService->getStats();
        $categories = $this->brandService->getCategories();
        $statuses   = $this->brandService->getStatuses();
        $owners     = $this->brandService->getOwners();

        // Active category for the tabs (defaults to first category or 'all')
        $activeCategory = $filters['category'] ?: 'all';

        // If client requested JSON (API / AJAX)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'brands'         => $brands,
                'stats'          => $stats,
                'categories'     => $categories,
                'statuses'       => $statuses,
                'activeCategory' => $activeCategory,
            ]);
        }

        return view('dashboard.index', compact(
            'brands',
            'allBrands',
            'stats',
            'categories',
            'statuses',
            'owners',
            'filters',
            'activeCategory'
        ));
    }

    /**
     * Store a newly created brand in brands.json.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Simple Laravel validation
        $validated = $request->validate([
            'brand_name'         => 'required|string|max:255',
            'category'           => 'required|string',
            'token'              => 'nullable|string|max:255',
            'status'             => 'nullable|string|max:50',
            'owner'              => 'nullable|string|max:255',
            'repo_link'          => 'nullable|url|max:500',
            'laravel'            => 'nullable',
            'ftp_details'        => 'nullable|string|max:500',
            'notes'              => 'nullable|string|max:1000',
            'custom_field_name'  => 'nullable|string|max:255',
            'custom_status'      => 'nullable|string|max:255',
        ]);

        // Convert checkbox to boolean (HTML checkboxes send "1", "on", or are omitted)
        $validated['laravel'] = $request->has('laravel') && ($request->input('laravel') == '1' || $request->input('laravel') == 'true' || $request->input('laravel') === true || $request->input('laravel') == 'on');

        $newBrand = $this->brandService->create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand added successfully.',
                'brand'   => $newBrand,
            ], 201);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Brand added successfully.');
    }

    /**
     * Update the specified brand in brands.json.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $id = (int)$id;

        $validated = $request->validate([
            'brand_name'         => 'required|string|max:255',
            'category'           => 'required|string',
            'token'              => 'nullable|string|max:255',
            'status'             => 'nullable|string|max:50',
            'owner'              => 'nullable|string|max:255',
            'repo_link'          => 'nullable|url|max:500',
            'laravel'            => 'nullable',
            'ftp_details'        => 'nullable|string|max:500',
            'notes'              => 'nullable|string|max:1000',
            'custom_field_name'  => 'nullable|string|max:255',
            'custom_status'      => 'nullable|string|max:255',
        ]);

        $validated['laravel'] = $request->has('laravel') && ($request->input('laravel') == '1' || $request->input('laravel') == 'true' || $request->input('laravel') === true || $request->input('laravel') == 'on');

        $updatedBrand = $this->brandService->update($id, $validated);

        if (!$updatedBrand) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Brand not found.'], 404);
            }
            return redirect()->route('dashboard')
                ->with('error', 'Brand not found.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully.',
                'brand'   => $updatedBrand,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Bulk update multiple brands at once (e.g. setting custom status field).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'No brands selected.'], 422);
            }
            return redirect()->route('dashboard')->with('error', 'No brands selected.');
        }

        $data = [
            'custom_field_name' => $request->input('custom_field_name', 'Number Update'),
            'custom_status'     => $request->input('custom_status'),
            'status'            => $request->input('status'),
            'owner'             => $request->input('owner'),
            'category'          => $request->input('category'),
        ];

        $updatedCount = $this->brandService->bulkUpdate($ids, $data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => "Updated {$updatedCount} brand(s) successfully.",
                'updatedCount' => $updatedCount,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', "Updated {$updatedCount} brand(s) successfully.");
    }

    /**
     * Remove the specified brand from brands.json.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $id = (int)$id;
        $deleted = $this->brandService->delete($id);

        if (!$deleted) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Brand not found or could not be deleted.'], 404);
            }
            return redirect()->route('dashboard')
                ->with('error', 'Brand not found or could not be deleted.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.',
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Brand deleted successfully.');
    }
}
