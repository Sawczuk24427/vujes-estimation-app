<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\EstimationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/users', [UserController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('users', UserController::class)->only([ 'store', 'update']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('estimations', EstimationController::class);

});
