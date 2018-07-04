<?php

use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            'symbol' => 'R',
            'currency' => 'ZAR',
            'description' => 'South Africa Rand',
            'exchange_rate' => '0.0751574',
            'surcharge_rate' => '0.075',
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ]);

        DB::table('currencies')->insert([
            'symbol' => '',
            'currency' => 'GBP',
            'description' => 'British Pound',
            'exchange_rate' => '1.5356784',
            'surcharge_rate' => '0.05',
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ]);

        DB::table('currencies')->insert([
            'symbol' => '',
            'currency' => 'EUR',
            'description' => 'Euro',
            'exchange_rate' => '1.1301069',
            'surcharge_rate' => '0.05',
            'discount_rate' => '0.02',
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ]);

        DB::table('currencies')->insert([
            'symbol' => '',
            'currency' => 'KES',
            'description' => 'Kenyan Shilling',
            'exchange_rate' => '0.0096283',
            'surcharge_rate' => '0.025',
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ]);
    }
}
