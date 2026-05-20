<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
<<<<<<< HEAD
use App\Http\Controllers\Admin\CategoryTemplateController;
use App\Http\Controllers\Admin\BudgetTemplateController;
use App\Http\Controllers\Admin\ScheduleTemplateController;
use App\Http\Controllers\Admin\TaskGroupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\EventGuestController;
use App\Http\Controllers\Organizer\GuestController;
use App\Http\Controllers\Organizer\EventTaskController;
use App\Http\Controllers\Organizer\EventScheduleController;
use App\Http\Controllers\Organizer\EventBudgetController;
use App\Http\Controllers\Organizer\ContributionController;
use App\Http\Controllers\Organizer\AttendanceController;
use App\Http\Controllers\Organizer\InviteCardController;
use App\Http\Controllers\Organizer\EventCompletionController;
use App\Http\Controllers\Public\GuestRegistrationController;
use App\Http\Controllers\Public\CheckinController;
=======
use App\Http\Controllers\Admin\CategoryTamplateController;
use App\Http\Controllers\Admin\BudgetTamplateController;
use App\Http\Controllers\Admin\ScheduleTamplateController;
use App\Http\Controllers\Admin\TaskGroupController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminEventController;
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
use Illuminate\Support\Facades\Route;

// ─── Public / Welcome ─────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

<<<<<<< HEAD
// ─── Public Guest Registration & Check-in (No login required) ─────────────────
Route::get('/register/{inviteToken}', [GuestRegistrationController::class, 'show'])->name('public.register');
Route::post('/register/{inviteToken}', [GuestRegistrationController::class, 'store'])->name('public.register.store');

Route::get('/checkin/{attendanceToken}', [CheckinController::class, 'show'])->name('public.checkin');
Route::post('/checkin/{attendanceToken}', [CheckinController::class, 'store'])->name('public.checkin.store');
=======
// ─── User Dashboard (Breeze / Organizer) ─────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
});

// ─── Admin Protected Routes ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
<<<<<<< HEAD
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
=======
    // Logout
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users — list, view, promote, demote, delete
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/promote', [UserController::class, 'promoteToAdmin'])->name('users.promote');
    Route::patch('users/{user}/demote', [UserController::class, 'demoteToUser'])->name('users.demote');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

<<<<<<< HEAD
    // Categories & Templates
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::get('category-templates', [CategoryTemplateController::class, 'index'])->name('category-templates.index');
    Route::get('category-templates/{category}', [CategoryTemplateController::class, 'show'])->name('category-templates.show');
    Route::post('category-templates/{category}', [CategoryTemplateController::class, 'store'])->name('category-templates.store');
    Route::delete('category-templates/{category}/{template}', [CategoryTemplateController::class, 'destroy'])->name('category-templates.destroy');
    Route::get('budget-templates', [BudgetTemplateController::class, 'index'])->name('budget-templates.index');
    Route::get('budget-templates/{category}', [BudgetTemplateController::class, 'show'])->name('budget-templates.show');
    Route::post('budget-templates/{category}', [BudgetTemplateController::class, 'store'])->name('budget-templates.store');
    Route::delete('budget-templates/{category}/{template}', [BudgetTemplateController::class, 'destroy'])->name('budget-templates.destroy');

    Route::get('schedule-templates', [ScheduleTemplateController::class, 'index'])->name('schedule-templates.index');
    Route::get('schedule-templates/{category}', [ScheduleTemplateController::class, 'show'])->name('schedule-templates.show');
    Route::post('schedule-templates/{category}', [ScheduleTemplateController::class, 'store'])->name('schedule-templates.store');
    Route::delete('schedule-templates/{category}/{template}', [ScheduleTemplateController::class, 'destroy'])->name('schedule-templates.destroy');
    Route::resource('task-groups', TaskGroupController::class)->except(['show']);

    // Events (read only)
=======
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
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    Route::get('events', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('events/{event}', [AdminEventController::class, 'show'])->name('events.show');
});

<<<<<<< HEAD
// ─── Organizer Protected Routes ───────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Events ────────────────────────────────────────────────────────────────
    Route::resource('events', EventController::class);
    Route::patch('events/{event}/status', [EventController::class, 'updateStatus'])->name('events.status');

    // ── Event Guests ──────────────────────────────────────────────────────────
    Route::get('events/{event}/guests', [EventGuestController::class, 'index'])->name('events.guests.index');
    Route::post('events/{event}/guests', [EventGuestController::class, 'store'])->name('events.guests.store');
    Route::patch('events/{event}/guests/{eventGuest}', [EventGuestController::class, 'update'])->name('events.guests.update');
    Route::delete('events/{event}/guests/{eventGuest}', [EventGuestController::class, 'destroy'])->name('events.guests.destroy');

    // ── Tasks ─────────────────────────────────────────────────────────────────
    Route::get('events/{event}/tasks', [EventTaskController::class, 'index'])->name('events.tasks.index');
    Route::post('events/{event}/tasks', [EventTaskController::class, 'store'])->name('events.tasks.store');
    Route::patch('events/{event}/tasks/{task}', [EventTaskController::class, 'update'])->name('events.tasks.update');
    Route::patch('events/{event}/tasks/{task}/complete', [EventTaskController::class, 'complete'])->name('events.tasks.complete');
    Route::patch('events/{event}/tasks/{task}/reopen', [EventTaskController::class, 'reopen'])->name('events.tasks.reopen');
    Route::delete('events/{event}/tasks/{task}', [EventTaskController::class, 'destroy'])->name('events.tasks.destroy');
    Route::post('events/{event}/tasks/reorder', [EventTaskController::class, 'reorder'])->name('events.tasks.reorder');

    // ── Schedule ──────────────────────────────────────────────────────────────
    Route::get('events/{event}/schedule', [EventScheduleController::class, 'index'])->name('events.schedule.index');
    Route::post('events/{event}/schedule', [EventScheduleController::class, 'store'])->name('events.schedule.store');
    Route::patch('events/{event}/schedule/{schedule}', [EventScheduleController::class, 'update'])->name('events.schedule.update');
    Route::delete('events/{event}/schedule/{schedule}', [EventScheduleController::class, 'destroy'])->name('events.schedule.destroy');

    // ── Budget ────────────────────────────────────────────────────────────────
    Route::get('events/{event}/budget', [EventBudgetController::class, 'index'])->name('events.budget.index');
    Route::patch('events/{event}/budget', [EventBudgetController::class, 'updateTotal'])->name('events.budget.update');
    Route::post('events/{event}/budget/items', [EventBudgetController::class, 'storeItem'])->name('events.budget.items.store');
    Route::patch('events/{event}/budget/items/{item}', [EventBudgetController::class, 'updateItem'])->name('events.budget.items.update');
    Route::delete('events/{event}/budget/items/{item}', [EventBudgetController::class, 'destroyItem'])->name('events.budget.items.destroy');

    // ── Contributions ─────────────────────────────────────────────────────────
    Route::get('events/{event}/contributions', [ContributionController::class, 'index'])->name('events.contributions.index');
    Route::post('events/{event}/contributions', [ContributionController::class, 'store'])->name('events.contributions.store');
    Route::patch('events/{event}/contributions/{contribution}', [ContributionController::class, 'update'])->name('events.contributions.update');
    Route::delete('events/{event}/contributions/{contribution}', [ContributionController::class, 'destroy'])->name('events.contributions.destroy');

    // ── Attendance ────────────────────────────────────────────────────────────
    Route::get('events/{event}/attendance', [AttendanceController::class, 'index'])->name('events.attendance.index');
    Route::post('events/{event}/attendance/start', [AttendanceController::class, 'startCheckin'])->name('events.attendance.start');
    Route::post('events/{event}/attendance/stop', [AttendanceController::class, 'stopCheckin'])->name('events.attendance.stop');
    Route::post('events/{event}/attendance/manual', [AttendanceController::class, 'manualCheckin'])->name('events.attendance.manual');

    // ── Invite Card ───────────────────────────────────────────────────────────
    Route::get('events/{event}/invite', [InviteCardController::class, 'show'])->name('events.invite.show');
    Route::patch('events/{event}/invite', [InviteCardController::class, 'update'])->name('events.invite.update');
    Route::get('events/{event}/invite/link', [InviteCardController::class, 'getInviteLink'])->name('events.invite.link');

    // ── Event Completion ──────────────────────────────────────────────────────
    Route::get('events/{event}/complete', [EventCompletionController::class, 'show'])->name('events.completion.show');
    Route::post('events/{event}/complete', [EventCompletionController::class, 'store'])->name('events.completion.store');

    // ── Global Guest Book ─────────────────────────────────────────────────────
    Route::resource('guests', GuestController::class)->except(['show']);
});

// ─── Breeze Auth Routes ───────────────────────────────────────────────────────
require __DIR__.'/auth.php';
=======
// ─── Breeze Auth Routes (user login, register, password reset…) ───────────────
require __DIR__.'/auth.php';
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
