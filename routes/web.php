<?php

use App\Http\Controllers\ExchangeRateController;
use Illuminate\Support\Facades\Route;


Route::any('/', [ExchangeRateController::class, 'getExchangeRate']);
Route::get('/exchange-rates', [ExchangeRateController::class, 'getExchangeRates']);
