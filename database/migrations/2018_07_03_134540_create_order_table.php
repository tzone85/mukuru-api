<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency',3);
            $table->decimal('exchange_rate',8,7);
            $table->decimal('surcharge_rate',8,7);
            $table->decimal('foreign_currency_amount',13,4);
            $table->decimal('total_amount',13,4);
            $table->decimal('surcharge_amount',13,4);
            $table->decimal('discount_amount',13,4);
            $table->decimal('discount_rate',13,4)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
