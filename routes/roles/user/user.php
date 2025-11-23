<?php

use App\Http\Controllers\API\User\LinkController;
use App\Http\Controllers\API\User\ThemeController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'expaired_date'])->prefix('user')->name('user.')->group(function () {
    Route::controller(LinkController::class)->prefix('links')->name('links.')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{id}', 'show');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
    Route::controller(ThemeController::class)->prefix('themes')->name('themes.')->group(function () {
        Route::get('/', 'index');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});