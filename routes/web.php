<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StokMasukController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/stok-masuk', [StokMasukController::class, 'store']);
Route::get('/stok-masuk/approve/{id}', [StokMasukController::class, 'approve']);
Route::get('/stok-masuk/reject/{id}', [StokMasukController::class, 'reject']);
