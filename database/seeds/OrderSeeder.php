<?php

use Illuminate\Database\Seeder;
use App\Model\Order;
use App\Model\Currency;
use App\User;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            // Create 2 orders for each currency
            Order::create([
                'currency' => $currency->currency,
                'exchange_rate' => $currency->exchange_rate,
                'surcharge_rate' => $currency->surcharge_rate,
                'foreign_currency_amount' => 1000.00,
                'total_amount' => 1200.00,
                'surcharge_amount' => 100.00,
                'discount_amount' => 0.00,
                'discount_rate' => null
            ]);

            Order::create([
                'currency' => $currency->currency,
                'exchange_rate' => $currency->exchange_rate,
                'surcharge_rate' => $currency->surcharge_rate,
                'foreign_currency_amount' => 500.00,
                'total_amount' => 600.00,
                'surcharge_amount' => 50.00,
                'discount_amount' => 10.00,
                'discount_rate' => 0.02
            ]);
        }
    }
}
