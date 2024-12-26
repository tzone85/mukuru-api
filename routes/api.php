<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\GetForeignCurrencyAmountRequest;
use App\Http\Requests\GetTotalAmountRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Currency routes
    Route::get('/currencies', [CurrencyController::class, 'index']);
    Route::get('/currencies/{id}', [CurrencyController::class, 'show']);
    Route::post('/currencies/get-foreign-currency-amount', [CurrencyController::class, 'getForeignCurrencyAmount'])
        ->middleware('validate.request:' . GetForeignCurrencyAmountRequest::class);
    Route::post('/currencies/get-total-amount', [CurrencyController::class, 'getTotalAmount'])
        ->middleware('validate.request:' . GetTotalAmountRequest::class);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('validate.request:' . CreateOrderRequest::class);
});
