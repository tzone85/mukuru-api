<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\OrderController;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\GetForeignCurrencyAmountRequest;
use App\Http\Requests\GetTotalAmountRequest;
use App\Http\Requests\ShowCurrencyRequest;
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

Route::get('/currencies', function () {
    return app(CurrencyController::class)->index();
});

Route::get('/currencies/{id}', function (int $id) {
    return app(CurrencyController::class)->show($id);
})->where('id','[0-9]+');

Route::post('/get-total-amount', function (GetTotalAmountRequest $request) {
    return app(CurrencyController::class)->getTotalAmount($request);
});

Route::post('/get-foreign-currency-amount', function (GetForeignCurrencyAmountRequest $request) {
    return app(CurrencyController::class)->getForeignCurrencyAmount($request);
});

Route::post('/orders', function (CreateOrderRequest $request) {
    return app(OrderController::class)->store($request);
});
