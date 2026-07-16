<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Route API untuk Dashboard Utama & Visualisasi
Route::get('/countries', [ApiController::class, 'getCountries']);
Route::get('/ports', [ApiController::class, 'getPorts']);
Route::get('/news', [ApiController::class, 'getNewsData']);
Route::get('/currency', [ApiController::class, 'getCurrencyData']);
Route::get('/risk', [ApiController::class, 'getRiskData']);