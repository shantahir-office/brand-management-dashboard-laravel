<?php

namespace Tests\Unit;

use App\Services\BrandService;
use Tests\TestCase;

class BrandServiceTest extends TestCase
{
    /**
     * Test that BrandService can retrieve all brands.
     */
    public function test_can_get_all_brands(): void
    {
        $service = new BrandService();
        $brands = $service->getAll();

        $this->assertIsArray($brands);
    }

    /**
     * Test that stats calculation works.
     */
    public function test_can_calculate_stats(): void
    {
        $service = new BrandService();
        $stats = $service->getStats();

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('active', $stats);
        $this->assertArrayHasKey('development', $stats);
        $this->assertArrayHasKey('completed', $stats);
        $this->assertArrayHasKey('laravel', $stats);
        $this->assertArrayHasKey('missing_token', $stats);
        $this->assertArrayHasKey('missing_repo', $stats);
    }

    /**
     * Test categories list contains the required 8 categories.
     */
    public function test_has_eight_required_categories(): void
    {
        $service = new BrandService();
        $categories = $service->getCategories();

        $this->assertCount(8, $categories);
        $this->assertContains('TM Brands', $categories);
        $this->assertContains('FTP Details', $categories);
        $this->assertContains('Design Brands', $categories);
        $this->assertContains('Books Brands / POS', $categories);
        $this->assertContains('OC Brands', $categories);
        $this->assertContains('DBA Brands', $categories);
        $this->assertContains('HR Brand', $categories);
        $this->assertContains('PPC Brands', $categories);
    }
}
