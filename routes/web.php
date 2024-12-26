<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// API Documentation Routes
Route::get('docs/api', fn () => view('scramble::docs'))->name('scramble.docs.index');
Route::get('docs/api.json', fn () => app(\Dedoc\Scramble\Support\Generator\OpenApi::class)->generate())->name('scramble.docs.json');
