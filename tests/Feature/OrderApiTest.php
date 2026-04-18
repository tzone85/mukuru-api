<?php

namespace Tests\Feature;

use App\Model\Currency;
use App\Model\Order;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
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
            'exchange_rate' => 1.25,
            'surcharge_rate' => 0.05,
            'discount_rate' => 0.02,
        ]);

        Currency::create([
            'currency' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1.0,
            'surcharge_rate' => 0.05,
            'discount_rate' => 0.02,
        ]);
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

    public function test_successful_order_creation_with_valid_data()
    {
        $orderData = [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'currency',
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

        // Verify database persistence
        $this->assertDatabaseHas('orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
        ]);

        // Verify calculations are performed
        $order = $response->json();
        $this->assertNotNull($order['surcharge_amount']);
        $this->assertNotNull($order['discount_amount']);
        $this->assertTrue($order['total_amount'] > 0);
    }

    public function test_order_creation_calculates_surcharge_correctly()
    {
        $orderData = [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);
        $order = $response->json();

        // Business logic: surcharge = original_total * surcharge_rate
        $expectedSurcharge = 100.00 * 0.075;
        $this->assertEquals($expectedSurcharge, $order['surcharge_amount']);

        // After surcharge: adjusted_total = original_total - surcharge
        $adjustedTotal = 100.00 - $expectedSurcharge;

        // Discount = adjusted_total * discount_rate
        $expectedDiscount = $adjustedTotal * 0.03;
        $this->assertEquals($expectedDiscount, $order['discount_amount']);

        // Final total = adjusted_total - discount
        $expectedFinalTotal = $adjustedTotal - $expectedDiscount;
        $this->assertEquals($expectedFinalTotal, $order['total_amount']);
    }

    public function test_order_creation_validation()
    {
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422);
    }

    public function test_required_field_validation()
    {
        // Test missing currency
        $response = $this->postJson('/api/v1/orders', [
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);

        // Test missing foreign_currency_amount
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'total_amount' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);

        // Test missing total_amount
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);

        // Test missing all required fields
        $response = $this->postJson('/api/v1/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency', 'foreign_currency_amount', 'total_amount']);
    }

    public function test_data_type_validation_for_currency_amounts()
    {
        // Test non-numeric foreign_currency_amount
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 'not-a-number',
            'total_amount' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);

        // Test non-numeric total_amount
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 'invalid-amount'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);

        // Test non-numeric values for both amount fields
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 'invalid',
            'total_amount' => 'also-invalid'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount', 'total_amount']);
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

    public function test_invalid_currency_handling()
    {
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'INVALID',
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);

        // Test non-existent but valid format currency
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'XXX',
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency']);
    }

    public function test_order_creation_with_negative_amounts()
    {
        $orderData = [
            'currency' => 'USD',
            'foreign_currency_amount' => -100.00,
            'total_amount' => -100.00
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(422);
    }

    public function test_negative_amount_validation()
    {
        // Test negative foreign_currency_amount
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => -1000,
            'total_amount' => 100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount']);

        // Test negative total_amount
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => -100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['total_amount']);

        // Test both amounts negative
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => -1000,
            'total_amount' => -100.00
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['foreign_currency_amount', 'total_amount']);

        // Test zero amounts (should be valid as min:0 allows zero)
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 0,
            'total_amount' => 0
        ]);

        $response->assertStatus(200);
    }

    public function test_proper_response_structure_matches_expected_format()
    {
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'GBP',
            'foreign_currency_amount' => 500,
            'total_amount' => 625.00
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'currency',
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

        $order = $response->json();

        // Verify types
        $this->assertIsInt($order['id']);
        $this->assertIsString($order['currency']);
        $this->assertIsNumeric($order['exchange_rate']);
        $this->assertIsNumeric($order['surcharge_rate']);
        $this->assertIsNumeric($order['foreign_currency_amount']);
        $this->assertIsNumeric($order['total_amount']);
        $this->assertIsNumeric($order['surcharge_amount']);
        $this->assertIsNumeric($order['discount_amount']);
        $this->assertIsNumeric($order['discount_rate']);
        $this->assertIsString($order['created_at']);
        $this->assertIsString($order['updated_at']);

        // Verify currency data is populated from currency table
        $this->assertEquals('GBP', $order['currency']);
        $this->assertEquals(1.25, $order['exchange_rate']);
        $this->assertEquals(0.05, $order['surcharge_rate']);
        $this->assertEquals(0.02, $order['discount_rate']);
    }

    public function test_database_persistence_verification()
    {
        $orderData = [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 2500,
            'total_amount' => 187.89
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);
        $response->assertStatus(200);

        $order = $response->json();

        // Verify complete order data is persisted correctly
        $this->assertDatabaseHas('orders', [
            'id' => $order['id'],
            'currency' => 'ZAR',
            'foreign_currency_amount' => 2500,
            'exchange_rate' => 0.0751574,
            'surcharge_rate' => 0.075,
            'discount_rate' => 0.03
        ]);

        // Verify calculated amounts are persisted
        $this->assertDatabaseHas('orders', [
            'id' => $order['id'],
            'surcharge_amount' => $order['surcharge_amount'],
            'discount_amount' => $order['discount_amount'],
            'total_amount' => $order['total_amount']
        ]);

        // Verify order can be retrieved from database
        $savedOrder = Order::find($order['id']);
        $this->assertNotNull($savedOrder);
        $this->assertEquals('ZAR', $savedOrder->currency);
        $this->assertEquals(2500, $savedOrder->foreign_currency_amount);
    }

    public function test_business_logic_validation_with_different_currencies()
    {
        // Test with ZAR (7.5% surcharge, 3% discount)
        $zarResponse = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ]);

        $zarOrder = $zarResponse->json();
        $this->assertEquals(100.00 * 0.075, $zarOrder['surcharge_amount']);

        // Test with GBP (5% surcharge, 2% discount)
        $gbpResponse = $this->postJson('/api/v1/orders', [
            'currency' => 'GBP',
            'foreign_currency_amount' => 1000,
            'total_amount' => 100.00
        ]);

        $gbpOrder = $gbpResponse->json();
        $this->assertEquals(100.00 * 0.05, $gbpOrder['surcharge_amount']);

        // Verify different currencies produce different calculations
        $this->assertNotEquals($zarOrder['surcharge_amount'], $gbpOrder['surcharge_amount']);
        $this->assertNotEquals($zarOrder['discount_amount'], $gbpOrder['discount_amount']);
        $this->assertNotEquals($zarOrder['total_amount'], $gbpOrder['total_amount']);
    }

    public function test_edge_case_with_very_small_amounts()
    {
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'ZAR',
            'foreign_currency_amount' => 0.01,
            'total_amount' => 0.01
        ]);

        $response->assertStatus(200);

        $order = $response->json();
        $this->assertTrue($order['surcharge_amount'] >= 0);
        $this->assertTrue($order['discount_amount'] >= 0);
        $this->assertTrue($order['total_amount'] >= 0);
    }

    public function test_edge_case_with_large_amounts()
    {
        $response = $this->postJson('/api/v1/orders', [
            'currency' => 'GBP',
            'foreign_currency_amount' => 999999999.99,
            'total_amount' => 799999999.99
        ]);

        $response->assertStatus(200);

        $order = $response->json();
        $this->assertTrue($order['surcharge_amount'] > 0);
        $this->assertTrue($order['discount_amount'] > 0);
        $this->assertTrue($order['total_amount'] > 0);
    }
}