<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UpvoteController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\SeasonalAlertController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/cities', [CityController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/companies', [CompanyController::class, 'index']);

Route::get('/reports/track/{token}', [ReportController::class, 'track']);
Route::get('/leaderboard', [LeaderboardController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Reports (Citizen)
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/my-reports', [ReportController::class, 'myReports']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::post('/reports/{report}/upvote', [UpvoteController::class, 'toggle']);
    Route::post('/reports/{report}/rate', [RatingController::class, 'store']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Company Routes
    Route::middleware('role:company')->prefix('company')->group(function () {
        Route::get('/reports', [CompanyController::class, 'myReports']);
        Route::get('/stats', [CompanyController::class, 'stats']);
        Route::get('/route', [CompanyController::class, 'route']);
        Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus']);
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/reports', [AdminController::class, 'reports']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::patch('/users/{user}/toggle-ban', [AdminController::class, 'toggleBan']);
        Route::get('/companies', [AdminController::class, 'companies']);
        Route::post('/companies', [AdminController::class, 'storeCompany']);
        Route::get('/analytics', [AdminController::class, 'analytics']);
        Route::get('/seasonal-alerts', [SeasonalAlertController::class, 'index']);
        Route::post('/seasonal-alerts', [SeasonalAlertController::class, 'store']);
        Route::post('/auto-assign-all', [AssignmentController::class, 'autoAssignAll']);
    });
});
