<?php

use App\Http\Controllers\API\Admin\ReportController;
use App\Http\Controllers\API\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'expaired_date', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });
    Route::controller(ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/show', 'getShowReports');
        Route::get('/resolved', 'getResolvedReports');
        Route::post('/{id}', 'store')->name('store');
    });
});