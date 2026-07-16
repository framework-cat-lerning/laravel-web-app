<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'loggedIn'])->name('logged-in');
});

Route::middleware('auth')->group(function () {
    Route::inertia('/', 'welcome')->name('home');
    Route::post('logout', LogoutController::class)->name('logout');
});
