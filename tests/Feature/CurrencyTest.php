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
            'discountRate' => 0.03,
        ]);

        Currency::create([
            'currency' => 'GBP',
            'symbol' => '£',
            'exchange_rate' => 1.35,
            'surcharge_rate' => 0.05,
            'discountRate' => 0.02,
        ]);
    }

    public function test_can_list_all_currencies()
    {
        $response = $this->getJson('/api/currencies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'currency',
                    'symbol',
                    'exchange_rate',
                    'surcharge_rate',
                    'discountRate'
                ]
            ])
            ->assertJsonCount(2);
    }

    public function test_can_get_single_currency()
    {
        $currency = Currency::where('currency', 'ZAR')->first();

        $response = $this->getJson("/api/currencies/{$currency->id}");

        $response->assertStatus(200)
            ->assertJson([
                'currency' => 'ZAR',
                'symbol' => 'R',
                'exchange_rate' => '0.0751574',
                'surcharge_rate' => '0.0750000'
            ]);
    }

    public function test_returns_404_for_non_existent_currency()
    {
        $response = $this->getJson('/api/currencies/999');

        $response->assertStatus(404);
    }

    public function test_can_convert_usd_to_foreign_currency()
    {
        $response = $this->postJson('/api/get-foreign-currency-amount', [
            'currency' => 'ZAR',
            'total_amount' => 100
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'foreign_currency_amount',
                'total_amount',
                'exchange_rate',
                'surcharge_rate',
                'currency'
            ]);
    }

    public function test_can_convert_foreign_currency_to_usd()
    {
        $response = $this->postJson('/api/get-total-amount', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_amount',
                'exchange_rate',
                'surcharge_rate'
            ]);
    }

    public function test_returns_error_for_invalid_currency_conversion()
    {
        $response = $this->postJson('/api/get-foreign-currency-amount', [
            'currency' => 'INVALID',
            'total_amount' => 100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);
    }

    public function test_get_total_amount_validates_required_fields()
    {
        $response = $this->postJson('/api/get-total-amount', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency', 'foreign_currency_amount']);
    }

    public function test_get_foreign_currency_amount_validates_required_fields()
    {
        $response = $this->postJson('/api/get-foreign-currency-amount', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency', 'total_amount']);
    }

    public function test_get_total_amount_validates_numeric_amount()
    {
        $response = $this->postJson('/api/get-total-amount', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 'not-a-number'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);
    }

    public function test_get_foreign_currency_amount_validates_numeric_amount()
    {
        $response = $this->postJson('/api/get-foreign-currency-amount', [
            'currency' => 'ZAR',
            'total_amount' => 'not-a-number'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);
    }

    public function test_get_total_amount_validates_positive_amount()
    {
        $response = $this->postJson('/api/get-total-amount', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => -100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);
    }

    public function test_get_foreign_currency_amount_validates_positive_amount()
    {
        $response = $this->postJson('/api/get-foreign-currency-amount', [
            'currency' => 'ZAR',
            'total_amount' => -100
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);
    }
}
