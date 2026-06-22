<?php

namespace Tests\Feature;

use App\Model\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyApiGetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed test currencies with comprehensive data
        Currency::create([
            'currency' => 'ZAR',
            'symbol' => 'R',
            'exchange_rate' => 0.0751574,
            'surcharge_rate' => 0.075,
            'discount_rate' => 0.03,
        ]);

        Currency::create([
            'currency' => 'GBP',
            'symbol' => '£',
            'exchange_rate' => 1.35,
            'surcharge_rate' => 0.05,
            'discount_rate' => 0.02,
        ]);

        Currency::create([
            'currency' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 1.18,
            'surcharge_rate' => 0.06,
            'discount_rate' => 0.025,
        ]);

        Currency::create([
            'currency' => 'KES',
            'symbol' => 'KSh',
            'exchange_rate' => 0.0088,
            'surcharge_rate' => 0.08,
            'discount_rate' => 0.035,
        ]);
    }

    public function test_get_all_currencies_returns_proper_structure()
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
                        'discount_percentage'
                    ]
                ]
            ]);
    }

    public function test_get_all_currencies_returns_correct_count()
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(200)
            ->assertJsonPath('data', function ($data) {
                return count($data) === 4; // We seeded 4 currencies
            });
    }

    public function test_get_currencies_includes_all_seeded_currencies()
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(200);

        $currencies = $response->json('data');
        $codes = collect($currencies)->pluck('code')->toArray();

        $this->assertContains('ZAR', $codes);
        $this->assertContains('GBP', $codes);
        $this->assertContains('EUR', $codes);
        $this->assertContains('KES', $codes);
    }

    public function test_get_single_currency_zar()
    {
        $currency = Currency::where('currency', 'ZAR')->first();

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'ZAR',
                    'symbol' => 'R',
                    'rate' => 0.0751574,
                    'surcharge_percentage' => 7.5,
                    'discount_percentage' => 3.0
                ]
            ]);
    }

    public function test_get_single_currency_gbp()
    {
        $currency = Currency::where('currency', 'GBP')->first();

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'GBP',
                    'symbol' => '£',
                    'rate' => 1.35,
                    'surcharge_percentage' => 5.0,
                    'discount_percentage' => 2.0
                ]
            ]);
    }

    public function test_get_single_currency_eur()
    {
        $currency = Currency::where('currency', 'EUR')->first();

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'EUR',
                    'symbol' => '€',
                    'rate' => 1.18,
                    'surcharge_percentage' => 6.0,
                    'discount_percentage' => 2.5
                ]
            ]);
    }

    public function test_get_single_currency_kes()
    {
        $currency = Currency::where('currency', 'KES')->first();

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'KES',
                    'symbol' => 'KSh',
                    'rate' => 0.0088,
                    // 8.0 round-trips through JSON as int 8
                    'surcharge_percentage' => 8,
                    'discount_percentage' => 3.5
                ]
            ]);
    }

    public function test_get_single_currency_includes_all_fields()
    {
        $currency = Currency::first();

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'code',
                    'name',
                    'symbol',
                    'rate',
                    'surcharge_percentage',
                    'discount_percentage'
                ]
            ]);
    }

    public function test_get_currency_with_nonexistent_id_returns_404()
    {
        $response = $this->getJson('/api/v1/currencies/999');

        $response->assertStatus(404);
    }

    public function test_get_currency_with_invalid_id_format_returns_404()
    {
        $response = $this->getJson('/api/v1/currencies/invalid-id');

        $response->assertStatus(404);
    }

    public function test_get_currency_with_negative_id_returns_404()
    {
        $response = $this->getJson('/api/v1/currencies/-1');

        $response->assertStatus(404);
    }

    public function test_get_currencies_response_time_is_reasonable()
    {
        $startTime = microtime(true);

        $response = $this->getJson('/api/v1/currencies');

        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $responseTime, 'Response time should be under 2 seconds');
    }

    public function test_get_single_currency_response_time_is_reasonable()
    {
        $currency = Currency::first();
        $startTime = microtime(true);

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(1.0, $responseTime, 'Single currency response time should be under 1 second');
    }

    public function test_currencies_have_valid_exchange_rates()
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(200);

        $currencies = $response->json('data');

        foreach ($currencies as $currency) {
            // Whole-number values round-trip through JSON as int, so assert numeric.
            $this->assertIsNumeric($currency['rate']);
            $this->assertGreaterThan(0, $currency['rate']);
        }
    }

    public function test_currencies_have_valid_surcharge_rates()
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(200);

        $currencies = $response->json('data');

        foreach ($currencies as $currency) {
            $this->assertIsNumeric($currency['surcharge_percentage']);
            $this->assertGreaterThanOrEqual(0, $currency['surcharge_percentage']);
            $this->assertLessThanOrEqual(100, $currency['surcharge_percentage']);
        }
    }

    public function test_currencies_have_valid_discount_rates()
    {
        $response = $this->getJson('/api/v1/currencies');

        $response->assertStatus(200);

        $currencies = $response->json('data');

        foreach ($currencies as $currency) {
            $this->assertIsNumeric($currency['discount_percentage']);
            $this->assertGreaterThanOrEqual(0, $currency['discount_percentage']);
            $this->assertLessThanOrEqual(100, $currency['discount_percentage']);
        }
    }
}