<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\Seeker\DashboardController as SeekerDashboardController;
use App\Http\Controllers\Api\Seeker\ProfileController as SeekerProfileController;
use App\Http\Controllers\Api\Seeker\ApplicationController as SeekerApplicationController;
use App\Http\Controllers\Api\Seeker\SavedJobController;
use App\Http\Controllers\Api\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Api\Employer\CompanyProfileController;
use App\Http\Controllers\Api\Employer\JobListingController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\JobController as AdminJobController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;

Route::get('/meta', [MetaController::class, 'index']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{slug}', [JobController::class, 'show']);

Route::get('/me', [AuthController::class, 'me']);

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {

// middleware protects routes following les instructions donner 
// prefix /prefix/where ur at
// name bash iwli route dynamique f frontend we can use the name instead of hard coded route
// role so u can know which user type can access this bloc/ defined in app.php 
    Route::middleware('role:seeker')->prefix('seeker')->name('seeker.')->group(function () {
        Route::get('/dashboard', [SeekerDashboardController::class, 'index']);

        Route::get('/profile', [SeekerProfileController::class, 'show']);
        Route::put('/profile', [SeekerProfileController::class, 'update']);
        Route::post('/profile', [SeekerProfileController::class, 'update']);
        Route::get('/cv/download', [SeekerProfileController::class, 'downloadCv']);
        Route::delete('/cv', [SeekerProfileController::class, 'deleteCv']);

        Route::get('/applications', [SeekerApplicationController::class, 'index']);
        Route::get('/applications/{application}', [SeekerApplicationController::class, 'show']);
        Route::post('/apply/{jobListing}', [SeekerApplicationController::class, 'store']);

        Route::get('/saved-jobs', [SavedJobController::class, 'index']);
        Route::post('/saved-jobs/{jobListing}', [SavedJobController::class, 'toggle']);
    });


    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'index']);

        Route::get('/company', [CompanyProfileController::class, 'show']);
        Route::put('/company', [CompanyProfileController::class, 'update']);

        Route::get('/jobs', [JobListingController::class, 'index']);
        Route::post('/jobs', [JobListingController::class, 'store']);
        Route::get('/jobs/{jobListing}', [JobListingController::class, 'show']);
        Route::put('/jobs/{jobListing}', [JobListingController::class, 'update']);
        Route::delete('/jobs/{jobListing}', [JobListingController::class, 'destroy']);
        Route::put('/jobs/{jobListing}/toggle-status', [JobListingController::class, 'toggleStatus']);

        Route::get('/applicants', [JobListingController::class, 'applicants']);
        Route::get('/applications/{application}/cv', [JobListingController::class, 'downloadCv']);
        Route::put('/applications/{application}/status', [JobListingController::class, 'updateApplicationStatus']);
    });

    Route::middleware('role:admin,super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/jobs', [AdminJobController::class, 'index']);
        Route::delete('/jobs/{jobListing}', [AdminJobController::class, 'destroy']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::put('/users/{user}/deactivate', [AdminUserController::class, 'deactivate']);
        Route::put('/users/{user}/activate', [AdminUserController::class, 'activate']);
    });
});
