<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::get('/users/{user}', [AuthController::class, 'showUserProfile'])->name('user.profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/admin/users/{user}/toggle-block', [AuthController::class, 'toggleUserBlock'])->name('admin.users.toggle-block');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('recipes', RecipeController::class)->except(['index', 'show']);
});

Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
