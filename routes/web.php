<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryTamplateController;
use App\Http\Controllers\Admin\BudgetTamplateController;
use App\Http\Controllers\Admin\ScheduleTamplateController;
use App\Http\Controllers\Admin\TaskGroupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminEventController;
use Illuminate\Support\Facades\Route;

// ─── Public / Welcome ─────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

// ─── User Dashboard (Breeze / Organizer) ─────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
});

// ─── Admin Protected Routes ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Logout
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users — list, view, promote, demote, delete
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/promote', [UserController::class, 'promoteToAdmin'])->name('users.promote');
    Route::patch('users/{user}/demote', [UserController::class, 'demoteToUser'])->name('users.demote');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Event Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Category Templates
    Route::resource('category-templates', CategoryTamplateController::class)->except(['show']);

    // Budget Templates
    Route::resource('budget-templates', BudgetTamplateController::class)->except(['show']);

    // Schedule Templates
    Route::resource('schedule-templates', ScheduleTamplateController::class)->except(['show']);

    // Task Groups
    Route::resource('task-groups', TaskGroupController::class)->except(['show']);

    // Events (admin view — read only / manage)
    Route::get('events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('events/{event}', [AdminEventController::class, 'show'])->name('events.show');
});

// ─── Breeze Auth Routes (user login, register, password reset…) ───────────────
require __DIR__.'/auth.php';