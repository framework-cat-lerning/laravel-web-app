<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'loggedIn'])->name('logged-in');
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('logout', LogoutController::class)->name('logout');

    // 商品管理
    Route::get('/products', [ProductController::class, "index"])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, "show"])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, "edit"])->name('products.edit');
    Route::put('/products/{product}/edit', [ProductController::class, "update"])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, "delete"])->name('products.delete');
    Route::patch('/products/{product}', [ProductController::class, "approval"])->name('products.approval');

    // ユーザ管理
    Route::get('/users', [UserController::class, "index"])->name('users.index');
    Route::get('/users/new', [UserController::class, "new"])->name('users.new');
    Route::post('/users/new', [UserController::class, "store"])->name('users.store');
    Route::get('/users/{user}', [UserController::class, "show"])->name('users.show');
    Route::get('/users/{product}/edit', [UserController::class, "edit"])->name('users.edit');
    Route::put('/users/{product}/edit', [UserController::class, "update"])->name('users.update');
    Route::delete('/users/{product}', [UserController::class, "delete"])->name('users.delete');

});
