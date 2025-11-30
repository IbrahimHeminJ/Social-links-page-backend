<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::middleware(['auth:sanctum', 'expaired_date'])->group(function () {
        Route::get('/user', 'user')->name('auth.user');
        Route::post('/user/update', 'update')->name('auth.user.update');
        Route::post('/logout', 'logout');
    });
});
Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::get('/users/{id}', 'show');
    Route::get('/tags', 'getTags');
    Route::get('/user/{id}/button-links', 'getButtonLinks');
});

require __DIR__.'/roles/admin/admin.php';
require __DIR__.'/roles/user/user.php';