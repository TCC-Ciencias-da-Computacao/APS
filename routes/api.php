<?php

use App\Http\Controllers\BeachController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('beaches')->group(function () {
        Route::get('/', [BeachController::class, 'index']);
        Route::get('/{beach}', [BeachController::class, 'show']);
    });
});
