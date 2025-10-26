<?php

use App\Http\Controllers\Api\v1\Balance\BalanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'v1'], function () {
    Route::post('/deposit', [BalanceController::class, 'deposit']);
    Route::post('/withdraw', [BalanceController::class, 'withdraw']);
    Route::post('/transfer', [BalanceController::class, 'transfer']);
    Route::get('/balance/{user}', [BalanceController::class, 'getBalance']);
});
