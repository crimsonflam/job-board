<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Seeker\SeekerDashboardController;
use App\Http\Controllers\Seeker\ApplicationController;
use App\Http\Controllers\Seeker\SavedJobController;
use App\Http\Controllers\Seeker\ProfileController;
use App\Http\Controllers\Seeker\JobAlertController;
use App\Http\Controllers\Employer\EmployerDashboardController;
use App\Http\Controllers\Employer\JobListingController;
use App\Http\Controllers\Employer\CompanyProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminJobController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCompanyController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{slug}', [CompanyController::class, 'show'])->name('companies.show');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Messages (all authenticated users)
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/start/{application}', [MessageController::class, 'startConversation'])->name('messages.start');

    // Seeker routes
    Route::middleware('role:seeker')->prefix('seeker')->name('seeker.')->group(function () {
        Route::get('/dashboard', [SeekerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/apply/{jobListing}', [ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/apply/{jobListing}', [ApplicationController::class, 'store'])->name('applications.store');
        Route::get('/saved-jobs', [SavedJobController::class, 'index'])->name('saved-jobs.index');
        Route::post('/saved-jobs/{jobListing}', [SavedJobController::class, 'toggle'])->name('saved-jobs.toggle');
        Route::get('/alerts', [JobAlertController::class, 'index'])->name('alerts.index');
        Route::post('/alerts', [JobAlertController::class, 'store'])->name('alerts.store');
        Route::delete('/alerts/{jobAlert}', [JobAlertController::class, 'destroy'])->name('alerts.destroy');
    });

    // Employer routes
    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/company', [CompanyProfileController::class, 'edit'])->name('company.edit');
        Route::put('/company', [CompanyProfileController::class, 'update'])->name('company.update');
        Route::resource('jobs', JobListingController::class)->except(['show']);
        Route::get('/jobs/{jobListing}/applications', [JobListingController::class, 'applications'])->name('jobs.applications');
        Route::put('/applications/{application}/status', [JobListingController::class, 'updateApplicationStatus'])->name('applications.update-status');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
        Route::put('/jobs/{jobListing}/status', [AdminJobController::class, 'updateStatus'])->name('jobs.update-status');
        Route::delete('/jobs/{jobListing}', [AdminJobController::class, 'destroy'])->name('jobs.destroy');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/companies', [AdminCompanyController::class, 'index'])->name('companies.index');
        Route::put('/companies/{company}/verify', [AdminCompanyController::class, 'toggleVerification'])->name('companies.toggle-verification');
    });
});
