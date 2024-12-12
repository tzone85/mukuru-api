<?php

namespace Tests\Feature;

use App\Model\Currency;
use App\Model\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test currency
        Currency::create([
            'currency' => 'ZAR',
            'symbol' => 'R',
            'exchange_rate' => 0.0751574,
            'surcharge_rate' => 0.075,
            'discountRate' => 0.03,
        ]);
    }

    public function test_can_create_order()
    {
        $response = $this->postJson('/api/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 75.1574
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'currency',
                'symbol',
                'exchange_rate',
                'surcharge_rate',
                'foreign_currency_amount',
                'total_amount',
                'surcharge_amount',
                'discount_amount',
                'discount_rate',
                'created_at',
                'updated_at'
            ]);

        $this->assertDatabaseHas('orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000
        ]);
    }

    public function test_cannot_create_order_with_invalid_currency()
    {
        $response = $this->postJson('/api/orders', [
            'currency' => 'INVALID',
            'foreign_currency_amount' => 1000,
            'total_amount' => 75.1574
        ]);

        $response->assertStatus(404);
    }

    public function test_order_calculations_are_correct()
    {
        $response = $this->postJson('/api/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 75.1574
        ]);

        $response->assertStatus(200);
        
        $order = $response->json();
        
        // Test surcharge calculation
        $expectedSurcharge = $order['total_amount'] * 0.075;
        $this->assertEquals($expectedSurcharge, $order['surcharge_amount']);
        
        // Test discount calculation
        $amountAfterSurcharge = $order['total_amount'] - $order['surcharge_amount'];
        $expectedDiscount = $amountAfterSurcharge * 0.03;
        $this->assertEquals($expectedDiscount, $order['discount_amount']);
        
        // Test final amount
        $expectedFinalAmount = $amountAfterSurcharge - $expectedDiscount;
        $this->assertEquals($expectedFinalAmount, $order['total_amount']);
    }

    public function test_validation_rules()
    {
        $response = $this->postJson('/api/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => -1000, // negative amount
            'total_amount' => 75.1574
        ]);

        $response->assertStatus(422);

        $response = $this->postJson('/api/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 'not-a-number',
            'total_amount' => 75.1574
        ]);

        $response->assertStatus(422);
    }

    public function test_can_get_all_orders()
    {
        // Create a test order first
        $this->postJson('/api/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 75.1574
        ]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'currency',
                    'symbol',
                    'exchange_rate',
                    'surcharge_rate',
                    'foreign_currency_amount',
                    'total_amount',
                    'surcharge_amount',
                    'discount_amount',
                    'discount_rate',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJsonCount(1);
    }
}
