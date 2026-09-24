<?php

namespace Tests\Feature;

use App\Services\BrandService;
use Tests\TestCase;

class BrandTest extends TestCase
{
    /**
     * Test brand creation and cleanup in storage/app/brands.json.
     */
    public function test_can_create_and_delete_brand(): void
    {
        $service = new BrandService();

        $brand = $service->create([
            'brand_name' => 'Unit Test Brand',
            'category' => 'TM Brands',
            'status' => 'Development',
            'owner' => 'Tester',
            'token' => 'sample_token',
            'repo_link' => 'https://github.com/test/repo',
            'laravel' => true,
            'ftp_details' => null,
            'notes' => 'Test notes',
        ]);

        $this->assertNotNull($brand['id']);
        $this->assertEquals('Unit Test Brand', $brand['brand_name']);

        // Cleanup
        $deleted = $service->delete($brand['id']);
        $this->assertTrue($deleted);
    }
}
