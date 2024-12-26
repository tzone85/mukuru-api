<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'currency' => 'USD',
                'description' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.0000,
                'surcharge_rate' => 0.0500,
                'discount_rate' => 0.0000,
            ],
            [
                'currency' => 'EUR',
                'description' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.8500,
                'surcharge_rate' => 0.0500,
                'discount_rate' => 0.0000,
            ],
            [
                'currency' => 'GBP',
                'description' => 'British Pound',
                'symbol' => '£',
                'exchange_rate' => 0.7500,
                'surcharge_rate' => 0.0500,
                'discount_rate' => 0.0000,
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->insert($currency);
        }
    }
}
