<?php

namespace Tests\Feature;

use App\Model\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test currencies
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
                        'exchange_rate',
                        'surcharge_rate',
                        'discount_rate'
                    ]
                ]
            ])
            ->assertJsonPath('data', function ($data) {
                return count($data) === 2;
            });
    }

    public function test_can_get_single_currency()
    {
        $currency = Currency::where('currency', 'ZAR')->first();

        $response = $this->getJson("/api/v1/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => 'ZAR',
                    'symbol' => 'R',
                    'exchange_rate' => 0.0751574,
                    'surcharge_rate' => 0.075
                ]
            ]);
    }

    public function test_returns_404_for_non_existent_currency()
    {
        $response = $this->getJson('/api/v1/currencies/999');

        $response->assertStatus(404);
    }

    public function test_can_convert_usd_to_foreign_currency()
    {
        $response = $this->postJson('/api/v1/currencies/get-foreign-currency-amount', [
            'currency' => 'ZAR',
            'total_amount' => 100
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'foreign_currency_amount',
                    'total_amount',
                    'exchange_rate',
                    'surcharge_rate',
                    'currency'
                ]
            ]);
    }

    public function test_can_convert_foreign_currency_to_usd()
    {
        $response = $this->postJson('/api/v1/currencies/get-total-amount', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'foreign_currency_amount',
                    'total_amount',
                    'exchange_rate',
                    'surcharge_rate',
                    'currency'
                ]
            ]);
    }

    public function test_returns_error_for_invalid_currency_conversion()
    {
        $response = $this->postJson('/api/v1/currencies/get-foreign-currency-amount', [
            'currency' => 'INVALID',
            'total_amount' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);
    }

    public function test_get_total_amount_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/currencies/get-total-amount', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency', 'foreign_currency_amount']);
    }

    public function test_get_foreign_currency_amount_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/currencies/get-foreign-currency-amount', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency', 'total_amount']);
    }

    public function test_get_total_amount_validates_numeric_amount()
    {
        $response = $this->postJson('/api/v1/currencies/get-total-amount', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 'not-a-number'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);
    }

    public function test_get_foreign_currency_amount_validates_numeric_amount()
    {
        $response = $this->postJson('/api/v1/currencies/get-foreign-currency-amount', [
            'currency' => 'ZAR',
            'total_amount' => 'not-a-number'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);
    }

    public function test_get_total_amount_validates_positive_amount()
    {
        $response = $this->postJson('/api/v1/currencies/get-total-amount', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => -100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);
    }

    public function test_get_foreign_currency_amount_validates_positive_amount()
    {
        $response = $this->postJson('/api/v1/currencies/get-foreign-currency-amount', [
            'currency' => 'ZAR',
            'total_amount' => -100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);
    }
}
