<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CarController;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::group(['middleware' => ['api']], function () {
    Route::post('oauth/token', [AccessTokenController::class, 'issueToken']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
});

//Route::post('/login', [LoginController::class, 'login'])->name('login');


Route::middleware('auth:api')->group(function () {
    Route::get('cars', [CarController::class, 'getAllCars']);
    Route::post('cars', [CarController::class, 'createCar']);
    Route::get('cars/{id}', [CarController::class, 'getCar']);
    Route::put('cars/{id}', [CarController::class, 'updateCar']);
    Route::delete('cars/{id}', [CarController::class, 'deleteCar']);
});
