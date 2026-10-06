<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationMemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authentication (guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authentication (authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('email/verify', [VerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/resend', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

/*
|--------------------------------------------------------------------------
| Application (auth + verified)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('users/{user}', [ProfileController::class, 'show'])->name('users.show');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Activity logs
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');

    // Roles & permissions overview
    Route::get('roles-permissions', [RoleController::class, 'index'])->name('roles.index');

    // Organizations
    Route::get('organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::get('organizations/{organization}/switch', [OrganizationController::class, 'switchOrganization'])
        ->name('organizations.switch');

    /*
    |----------------------------------------------------------------------
    | Organization-scoped routes (tenant isolation enforced via the
    | "organization" middleware + policies + scoped parent lookups)
    |----------------------------------------------------------------------
    */
    Route::prefix('organizations/{organization}')->middleware('organization')->group(function () {
        Route::get('/', [OrganizationController::class, 'show'])->name('organizations.show');
        Route::get('settings', [OrganizationController::class, 'edit'])->name('organizations.edit');
        Route::put('settings', [OrganizationController::class, 'update'])->name('organizations.update');
        Route::post('logo', [OrganizationController::class, 'updateLogo'])->name('organizations.logo');
        Route::delete('/', [OrganizationController::class, 'destroy'])->name('organizations.destroy');

        // Members / user management
        Route::get('users', [OrganizationMemberController::class, 'index'])->name('organizations.users.index');
        Route::get('users/invite', [OrganizationMemberController::class, 'create'])->name('organizations.users.invite');
        Route::post('users/invite', [OrganizationMemberController::class, 'invite'])->name('organizations.users.store');
        Route::get('users/{member}/edit', [OrganizationMemberController::class, 'edit'])->name('organizations.users.edit');
        Route::put('users/{member}', [OrganizationMemberController::class, 'update'])->name('organizations.users.update');
        Route::delete('users/{member}', [OrganizationMemberController::class, 'destroy'])->name('organizations.users.destroy');

        // Projects
        Route::get('projects', [ProjectController::class, 'index'])->name('organizations.projects.index');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('organizations.projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('organizations.projects.store');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('organizations.projects.show');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('organizations.projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('organizations.projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('organizations.projects.destroy');

        // Tasks (nested under project)
        Route::get('projects/{project}/tasks', [TaskController::class, 'index'])->name('organizations.projects.tasks.index');
        Route::get('projects/{project}/tasks/create', [TaskController::class, 'create'])->name('organizations.projects.tasks.create');
        Route::post('projects/{project}/tasks', [TaskController::class, 'store'])->name('organizations.projects.tasks.store');
        Route::get('projects/{project}/tasks/{task}', [TaskController::class, 'show'])->name('organizations.projects.tasks.show');
        Route::get('projects/{project}/tasks/{task}/edit', [TaskController::class, 'edit'])->name('organizations.projects.tasks.edit');
        Route::put('projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('organizations.projects.tasks.update');
        Route::delete('projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('organizations.projects.tasks.destroy');
    });
});
