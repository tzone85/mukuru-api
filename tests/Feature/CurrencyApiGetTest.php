<?php

namespace Tests\Feature;

use App\Model\Currency;
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

        // Seed test currencies matching the CurrencySeeder
        Currency::create([
            'currency' => 'USD',
            'description' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1.0000,
            'surcharge_rate' => 0.0500,
            'discount_rate' => 0.0000,
        ]);

        Currency::create([
            'currency' => 'EUR',
            'description' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 0.8500,
            'surcharge_rate' => 0.0500,
            'discount_rate' => 0.0000,
        ]);

        Currency::create([
            'currency' => 'GBP',
            'description' => 'British Pound',
            'symbol' => '£',
            'exchange_rate' => 0.7500,
            'surcharge_rate' => 0.0500,
            'discount_rate' => 0.0000,
        ]);
    }

    public function test_can_list_all_currencies()
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'code',
                        'name',
                        'symbol',
                        'rate',
                        'surcharge_percentage',
                        'discount_percentage',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);

        // Verify response contains expected number of currencies from seeder
        $responseData = $response->json('data');
        $this->assertCount(3, $responseData, 'Response should contain 3 currencies from seeder');

        // Validate all required currency fields are present and have correct data types
        foreach ($responseData as $currency) {
            // Check required fields exist
            $this->assertArrayHasKey('id', $currency);
            $this->assertArrayHasKey('code', $currency);
            $this->assertArrayHasKey('name', $currency);
            $this->assertArrayHasKey('symbol', $currency);
            $this->assertArrayHasKey('rate', $currency);
            $this->assertArrayHasKey('surcharge_percentage', $currency);
            $this->assertArrayHasKey('discount_percentage', $currency);
            $this->assertArrayHasKey('created_at', $currency);
            $this->assertArrayHasKey('updated_at', $currency);

            // Validate data types
            $this->assertIsInt($currency['id']);
            $this->assertIsString($currency['code']);
            $this->assertIsString($currency['name']);
            $this->assertIsString($currency['symbol']);
            $this->assertIsNumeric($currency['rate']);
            $this->assertIsNumeric($currency['surcharge_percentage']);
            $this->assertIsNumeric($currency['discount_percentage']);
            $this->assertIsString($currency['created_at']);
            $this->assertIsString($currency['updated_at']);

            // Validate timestamp format (ISO 8601)
            $this->assertMatchesRegularExpression(
                '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z/',
                $currency['created_at'],
                'created_at should be in ISO 8601 format'
            );
            $this->assertMatchesRegularExpression(
                '/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z/',
                $currency['updated_at'],
                'updated_at should be in ISO 8601 format'
            );

            // Validate values are not empty
            $this->assertNotEmpty($currency['code']);
            $this->assertNotEmpty($currency['name']);
            $this->assertNotEmpty($currency['symbol']);
            $this->assertGreaterThanOrEqual(0, $currency['rate']);
            $this->assertGreaterThanOrEqual(0, $currency['surcharge_percentage']);
            $this->assertGreaterThanOrEqual(0, $currency['discount_percentage']);
        }

        // Check that specific seeded currencies are present
        $currencyCodes = collect($responseData)->pluck('code')->toArray();
        $this->assertContains('USD', $currencyCodes);
        $this->assertContains('EUR', $currencyCodes);
        $this->assertContains('GBP', $currencyCodes);
    }

    public function test_can_get_currency_by_valid_id()
    {
        // Retrieve currency ID from seeded data dynamically
        $currency = Currency::where('currency', 'USD')->first();

        // Make API request to GET /api/v1/currencies/{id}
        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        // Verify 200 status code response
        $response->assertStatus(200);

        // Assert correct JSON structure with 'data' object (not array)
        $response->assertJsonStructure([
            'data' => [
                'id',
                'code',
                'name',
                'symbol',
                'exchange_rate',
                'surcharge_rate',
                'discount_rate'
            ]
        ]);

        // Validate all currency fields match expected seeded values
        $response->assertJson([
            'data' => [
                'id' => $currency->id,
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0,
                'surcharge_rate' => 0.05,
                'discount_rate' => 0.0
            ]
        ]);

        // Tests specific currency data accuracy - assert exact values
        $responseData = $response->json('data');
        $this->assertEquals($currency->id, $responseData['id']);
        $this->assertEquals('USD', $responseData['code']);
        $this->assertEquals('US Dollar', $responseData['name']);
        $this->assertEquals('$', $responseData['symbol']);
        $this->assertEquals(1.0, $responseData['exchange_rate']);
        $this->assertEquals(0.05, $responseData['surcharge_rate']);
        $this->assertEquals(0.0, $responseData['discount_rate']);
    }
}