<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (versioned: /api/v1)
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider with the "api/v1"
| prefix and the "api" middleware group. All endpoints (except login)
| require a Sanctum token and respect tenant isolation.
|
*/

// Public authentication
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    // Auth / account
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // Organizations
    Route::get('organizations', [OrganizationController::class, 'index']);
    Route::post('organizations', [OrganizationController::class, 'store']);
    Route::get('organizations/{organization}', [OrganizationController::class, 'show']);
    Route::put('organizations/{organization}', [OrganizationController::class, 'update']);
    Route::delete('organizations/{organization}', [OrganizationController::class, 'destroy']);

    // Organization members (user management)
    Route::get('organizations/{organization}/members', [MemberController::class, 'index']);
    Route::post('organizations/{organization}/members', [MemberController::class, 'store']);
    Route::put('organizations/{organization}/members/{member}', [MemberController::class, 'update']);
    Route::delete('organizations/{organization}/members/{member}', [MemberController::class, 'destroy']);

    // Projects
    Route::get('organizations/{organization}/projects', [ProjectController::class, 'index']);
    Route::post('organizations/{organization}/projects', [ProjectController::class, 'store']);
    Route::get('organizations/{organization}/projects/{project}', [ProjectController::class, 'show']);
    Route::put('organizations/{organization}/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('organizations/{organization}/projects/{project}', [ProjectController::class, 'destroy']);

    // Tasks
    Route::get('organizations/{organization}/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('organizations/{organization}/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::get('organizations/{organization}/projects/{project}/tasks/{task}', [TaskController::class, 'show']);
    Route::put('organizations/{organization}/projects/{project}/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('organizations/{organization}/projects/{project}/tasks/{task}', [TaskController::class, 'destroy']);
});
