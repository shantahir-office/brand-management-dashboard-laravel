<?php

namespace App\Services;

/**
 * BrandService handles reading, filtering, and writing brand data in storage/app/brands.json.
 * 
 * Simple, beginner-friendly service without complex database abstractions.
 */
class BrandService
{
    /**
     * The predefined list of categories supported by the dashboard.
     * To add a new category in the future, simply add it to this array!
     */
    protected array $categories = [
        'TM Brands',
        'FTP Details',
        'Design Brands',
        'Books Brands / POS',
        'OC Brands',
        'DBA Brands',
        'HR Brand',
        'PPC Brands',
    ];

    /**
     * Standard status choices for brands.
     */
    protected array $statuses = [
        'Pending',
        'Development',
        'Active',
        'Completed',
        'On Hold',
    ];

    /**
     * Get the absolute path to the brands.json file.
     */
    public function getFilePath(): string
    {
        // Check if running inside Laravel with storage_path() available
        if (function_exists('storage_path')) {
            return storage_path('app/brands.json');
        }

        // Fallback for standalone scripts
        return dirname(__DIR__, 2) . '/storage/app/brands.json';
    }

    /**
     * Read all brands directly from storage/app/brands.json.
     *
     * @return array
     */
    public function readRawBrands(): array
    {
        $filePath = $this->getFilePath();

        if (!file_exists($filePath)) {
            // If the file doesn't exist yet, initialize it with an empty array
            $initialData = ['brands' => []];
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            file_put_contents($filePath, json_encode($initialData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return [];
        }

        $jsonContent = file_get_contents($filePath);
        $decoded = json_decode($jsonContent, true);

        return $decoded['brands'] ?? [];
    }

    /**
     * Save the brands array back to storage/app/brands.json.
     *
     * @param array $brands
     * @return bool
     */
    public function writeBrands(array $brands): bool
    {
        $filePath = $this->getFilePath();

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $payload = [
            'brands' => array_values($brands) // Re-index array keys to 0, 1, 2...
        ];

        $jsonString = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return file_put_contents($filePath, $jsonString) !== false;
    }

    /**
     * Get all brands with optional search and filters applied.
     *
     * @param array $filters
     * @return array
     */
    public function getAll(array $filters = []): array
    {
        $brands = $this->readRawBrands();

        $search = isset($filters['search']) ? trim(strtolower((string)$filters['search'])) : '';
        $category = isset($filters['category']) ? trim((string)$filters['category']) : '';
        $status = isset($filters['status']) ? trim((string)$filters['status']) : '';
        $owner = isset($filters['owner']) ? trim((string)$filters['owner']) : '';

        // Filter the brands array
        if ($search !== '' || $category !== '' || $status !== '' || $owner !== '') {
            $brands = array_filter($brands, function ($brand) use ($search, $category, $status, $owner) {
                // Category filter
                if ($category !== '' && ($brand['category'] ?? '') !== $category) {
                    return false;
                }

                // Status filter
                if ($status !== '' && ($brand['status'] ?? '') !== $status) {
                    return false;
                }

                // Owner filter
                if ($owner !== '' && ($brand['owner'] ?? '') !== $owner) {
                    return false;
                }

                // Search query: checks brand name, owner, repo_owner, and repo_link
                if ($search !== '') {
                    $brandName = strtolower($brand['brand_name'] ?? '');
                    $brandOwner = strtolower($brand['owner'] ?? '');
                    $brandRepoOwner = strtolower($brand['repo_owner'] ?? '');
                    $brandRepo = strtolower($brand['repo_link'] ?? '');

                    $matches = (strpos($brandName, $search) !== false) ||
                               (strpos($brandOwner, $search) !== false) ||
                               (strpos($brandRepoOwner, $search) !== false) ||
                               (strpos($brandRepo, $search) !== false);

                    if (!$matches) {
                        return false;
                    }
                }

                return true;
            });
        }

        return array_values($brands);
    }

    /**
     * Find a single brand by its integer ID.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        $brands = $this->readRawBrands();

        foreach ($brands as $brand) {
            if (isset($brand['id']) && (int)$brand['id'] === $id) {
                return $brand;
            }
        }

        return null;
    }

    /**
     * Create a new brand and save to brands.json.
     *
     * @param array $data
     * @return array The newly created brand
     */
    public function create(array $data): array
    {
        $brands = $this->readRawBrands();

        // Calculate next unique ID
        $maxId = 0;
        foreach ($brands as $brand) {
            if (isset($brand['id']) && (int)$brand['id'] > $maxId) {
                $maxId = (int)$brand['id'];
            }
        }
        $newId = $maxId + 1;

        $tech = !empty($data['technology']) ? trim($data['technology']) : (!empty($data['laravel']) ? 'Laravel' : 'PHP');

        // Clean values: treat empty strings as null so users never need to type "NULL"
        $newBrand = [
            'id' => $newId,
            'category' => !empty($data['category']) ? trim($data['category']) : 'TM Brands',
            'brand_name' => !empty($data['brand_name']) ? trim($data['brand_name']) : 'Untitled Brand',
            'technology' => $tech,
            'repo_owner' => !empty($data['repo_owner']) ? trim($data['repo_owner']) : 'ahmedzafar-devTeam',
            'token' => !empty($data['token']) ? trim($data['token']) : null,
            'status' => !empty($data['status']) ? trim($data['status']) : 'Pending',
            'owner' => !empty($data['owner']) ? trim($data['owner']) : null,
            'repo_link' => !empty($data['repo_link']) ? trim($data['repo_link']) : null,
            'laravel' => $tech === 'Laravel',
            'ftp_details' => !empty($data['ftp_details']) ? trim($data['ftp_details']) : null,
            'notes' => !empty($data['notes']) ? trim($data['notes']) : null,
            'custom_field_name' => !empty($data['custom_field_name']) ? trim($data['custom_field_name']) : 'Number Update',
            'custom_status' => !empty($data['custom_status']) ? trim($data['custom_status']) : null,
        ];

        $brands[] = $newBrand;
        $this->writeBrands($brands);

        return $newBrand;
    }

    /**
     * Update an existing brand by ID.
     *
     * @param int $id
     * @param array $data
     * @return array|null The updated brand, or null if not found
     */
    public function update(int $id, array $data): ?array
    {
        $brands = $this->readRawBrands();
        $updatedBrand = null;

        foreach ($brands as $index => $brand) {
            if (isset($brand['id']) && (int)$brand['id'] === $id) {
                // Keep the same ID
                $brand['brand_name'] = !empty($data['brand_name']) ? trim($data['brand_name']) : $brand['brand_name'];
                $brand['category'] = !empty($data['category']) ? trim($data['category']) : $brand['category'];
                $brand['token'] = !empty($data['token']) ? trim($data['token']) : null;
                $brand['status'] = !empty($data['status']) ? trim($data['status']) : 'Pending';
                $brand['owner'] = !empty($data['owner']) ? trim($data['owner']) : null;
                $brand['repo_link'] = !empty($data['repo_link']) ? trim($data['repo_link']) : null;
                if (array_key_exists('repo_owner', $data)) {
                    $brand['repo_owner'] = !empty($data['repo_owner']) ? trim($data['repo_owner']) : 'ahmedzafar-devTeam';
                } elseif (!isset($brand['repo_owner'])) {
                    $brand['repo_owner'] = 'ahmedzafar-devTeam';
                }
                $brand['laravel'] = isset($data['laravel']) ? (bool)$data['laravel'] : false;
                $brand['ftp_details'] = !empty($data['ftp_details']) ? trim($data['ftp_details']) : null;
                $brand['notes'] = !empty($data['notes']) ? trim($data['notes']) : null;
                if (array_key_exists('technology', $data)) {
                    $brand['technology'] = !empty($data['technology']) ? trim($data['technology']) : 'Laravel';
                    $brand['laravel'] = ($brand['technology'] === 'Laravel');
                } elseif (isset($data['laravel'])) {
                    $brand['laravel'] = (bool)$data['laravel'];
                    $brand['technology'] = $brand['laravel'] ? 'Laravel' : ($brand['technology'] ?? 'PHP');
                }
                if (array_key_exists('custom_field_name', $data)) {
                    $brand['custom_field_name'] = !empty($data['custom_field_name']) ? trim($data['custom_field_name']) : null;
                }
                if (array_key_exists('custom_status', $data)) {
                    $brand['custom_status'] = !empty($data['custom_status']) ? trim($data['custom_status']) : null;
                }

                $brands[$index] = $brand;
                $updatedBrand = $brand;
                break;
            }
        }

        if ($updatedBrand !== null) {
            $this->writeBrands($brands);
        }

        return $updatedBrand;
    }

    /**
     * Bulk update multiple brands at once (e.g. setting custom status field, standard status, owner, etc.)
     *
     * @param array $ids List of brand IDs to update
     * @param array $data Fields to update on each brand
     * @return int Number of updated brands
     */
    public function bulkUpdate(array $ids, array $data): int
    {
        $brands = $this->readRawBrands();
        $targetIds = array_map('intval', $ids);
        $updatedCount = 0;

        foreach ($brands as $index => $brand) {
            if (isset($brand['id']) && in_array((int)$brand['id'], $targetIds, true)) {
                $updatedCount++;

                if (array_key_exists('custom_field_name', $data)) {
                    $brand['custom_field_name'] = !empty($data['custom_field_name']) ? trim($data['custom_field_name']) : null;
                }
                if (array_key_exists('custom_status', $data)) {
                    $brand['custom_status'] = !empty($data['custom_status']) ? trim($data['custom_status']) : null;
                }
                if (!empty($data['status'])) {
                    $brand['status'] = trim($data['status']);
                }
                if (array_key_exists('owner', $data) && $data['owner'] !== '') {
                    $brand['owner'] = trim($data['owner']);
                }
                if (!empty($data['category'])) {
                    $brand['category'] = trim($data['category']);
                }

                $brands[$index] = $brand;
            }
        }

        if ($updatedCount > 0) {
            $this->writeBrands($brands);
        }

        return $updatedCount;
    }

    /**
     * Delete a brand by ID.
     *
     * @param int $id
     * @return bool True if deleted, false if not found
     */
    public function delete(int $id): bool
    {
        $brands = $this->readRawBrands();
        $found = false;

        $filtered = [];
        foreach ($brands as $brand) {
            if (isset($brand['id']) && (int)$brand['id'] === $id) {
                $found = true;
                continue; // Skip this brand to delete it
            }
            $filtered[] = $brand;
        }

        if ($found) {
            $this->writeBrands($filtered);
            return true;
        }

        return false;
    }

    /**
     * Compute statistics and data quality metrics dynamically from brands.json.
     *
     * @return array
     */
    public function getStats(): array
    {
        $brands = $this->readRawBrands();

        $stats = [
            'total' => count($brands),
            'active' => 0,
            'development' => 0,
            'completed' => 0,
            'pending' => 0,
            'on_hold' => 0,
            'laravel' => 0,
            'missing_token' => 0,
            'missing_repo' => 0,
            'missing_owner' => 0,
            'categories_count' => [],
        ];

        // Initialize category count dictionary
        foreach ($this->categories as $cat) {
            $stats['categories_count'][$cat] = 0;
        }

        foreach ($brands as $b) {
            $status = strtolower($b['status'] ?? '');
            if ($status === 'active') {
                $stats['active']++;
            } elseif ($status === 'development') {
                $stats['development']++;
            } elseif ($status === 'completed') {
                $stats['completed']++;
            } elseif ($status === 'pending') {
                $stats['pending']++;
            } elseif ($status === 'on hold') {
                $stats['on_hold']++;
            }

            if (!empty($b['laravel'])) {
                $stats['laravel']++;
            }

            // Data quality checks: identify missing critical information
            if (empty($b['token'])) {
                $stats['missing_token']++;
            }
            if (empty($b['repo_link'])) {
                $stats['missing_repo']++;
            }
            if (empty($b['owner'])) {
                $stats['missing_owner']++;
            }

            // Count per category
            $cat = $b['category'] ?? '';
            if (isset($stats['categories_count'][$cat])) {
                $stats['categories_count'][$cat]++;
            } else {
                $stats['categories_count'][$cat] = 1;
            }
        }

        return $stats;
    }

    /**
     * Return the available category list.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Return the available statuses.
     *
     * @return array
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * Get unique owners for filtering dropdown.
     *
     * @return array
     */
    public function getOwners(): array
    {
        $brands = $this->readRawBrands();
        $owners = [];

        foreach ($brands as $b) {
            if (!empty($b['owner'])) {
                $owners[trim($b['owner'])] = true;
            }
        }

        $list = array_keys($owners);
        sort($list);
        return $list;
    }
}
