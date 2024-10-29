<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::group(['middleware' => ['api']], function () {
    Route::post('oauth/token', [AccessTokenController::class, 'issueToken']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::get('user/', [AuthController::class, 'getUser']);
});
