<?php

namespace Tests\Feature;

use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed currencies using CurrencySeeder
        $this->seed(CurrencySeeder::class);
    }

    public function test_can_list_orders()
    {
        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200);
    }

    public function test_can_create_order()
    {
        $orderData = [
            'currency' => 'USD',
            'foreign_currency_amount' => 100.00,
            'total_amount' => 100.00
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'currency',
                'foreign_currency_amount',
                'total_amount',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_order_creation_validation()
    {
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency', 'foreign_currency_amount', 'total_amount']);
    }

    public function test_order_creation_with_invalid_currency()
    {
        $orderData = [
            'currency' => 'INVALID',
            'foreign_currency_amount' => 100.00,
            'total_amount' => 100.00
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422);
    }

    public function test_order_creation_with_negative_amounts()
    {
        $orderData = [
            'currency' => 'USD',
            'foreign_currency_amount' => -100.00,
            'total_amount' => -100.00
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount', 'total_amount']);
    }
}