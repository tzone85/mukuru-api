<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\CurrencySeeder;

/**
 * CurrencyApiGetTest
 *
 * Test suite for currency API GET operations.
 * Focuses on testing currency retrieval endpoints including:
 * - Listing all currencies (GET /api/v1/currencies)
 * - Getting individual currency details (GET /api/v1/currencies/{id})
 * - Validating response structures and data integrity
 * - Error handling for non-existent resources (404 responses)
 *
 * Uses RefreshDatabase trait and CurrencySeeder for consistent test data.
 * Each test method will have isolated database state with fresh seed data.
 *
 * @package Tests\Feature
 */
class CurrencyApiGetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * Seeds the database with currency test data using CurrencySeeder
     * to ensure consistent test conditions across all test methods.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed currencies using the CurrencySeeder
        $this->seed(CurrencySeeder::class);
    }
}