<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Seeker\SeekerDashboardController;
use App\Http\Controllers\Seeker\ApplicationController;
use App\Http\Controllers\Seeker\SavedJobController;
use App\Http\Controllers\Seeker\ProfileController;
use App\Http\Controllers\Employer\EmployerDashboardController;
use App\Http\Controllers\Employer\JobListingController;
use App\Http\Controllers\Employer\CompanyProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminJobController;
use App\Http\Controllers\Admin\AdminUserController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{slug}', [JobController::class, 'show'])->name('jobs.show');
// The standalone Companies feature was removed; company info is shown
// inline on jobs (via the employer) instead of on dedicated company pages.

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

    // MOD 19: Messaging routes removed entirely. The only employer→seeker
    // communication is the accept/reject response on an application.

    // Seeker routes
    Route::middleware('role:seeker')->prefix('seeker')->name('seeker.')->group(function () {
        Route::get('/dashboard', [SeekerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        // MOD 10: CV download + delete (PDF only; managed from the profile).
        Route::get('/cv/download', [ProfileController::class, 'downloadCv'])->name('cv.download');
        Route::delete('/cv', [ProfileController::class, 'deleteCv'])->name('cv.delete');
        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
        Route::get('/apply/{jobListing}', [ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/apply/{jobListing}', [ApplicationController::class, 'store'])->name('applications.store');
        // MOD 7: Saved jobs (bookmark/unbookmark).
        Route::get('/saved-jobs', [SavedJobController::class, 'index'])->name('saved-jobs.index');
        Route::post('/saved-jobs/{jobListing}', [SavedJobController::class, 'toggle'])->name('saved-jobs.toggle');
        // MOD 8: Job-alert routes removed (alert feature deleted).
    });

    // Employer routes
    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function () {
        Route::get('/dashboard', [EmployerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/company', [CompanyProfileController::class, 'edit'])->name('company.edit');
        Route::put('/company', [CompanyProfileController::class, 'update'])->name('company.update');
        // Use {jobListing} as the route param so it matches the controller's
        // `JobListing $jobListing` type-hints (otherwise implicit model binding
        // silently injects an empty model and authorization fails with 403).
        Route::resource('jobs', JobListingController::class)
            ->except(['show'])
            ->parameters(['jobs' => 'jobListing']);
        // Toggle a job between active (published) and inactive (closed).
        Route::put('/jobs/{jobListing}/toggle-status', [JobListingController::class, 'toggleStatus'])->name('jobs.toggle-status');
        // Per-job applicants ("View Applicants" on a job row).
        Route::get('/jobs/{jobListing}/applications', [JobListingController::class, 'applications'])->name('jobs.applications');
        // Unified applicants screen across all of the employer's jobs.
        Route::get('/applicants', [JobListingController::class, 'applicants'])->name('applicants.index');
        // MOD 11: Download an applicant's CV (forced download, no in-app preview).
        Route::get('/applications/{application}/cv', [JobListingController::class, 'downloadCv'])->name('applications.cv');
        // Accept / reject + message to an applicant.
        Route::put('/applications/{application}/status', [JobListingController::class, 'updateApplicationStatus'])->name('applications.update-status');
    });

    // Admin routes
    // MOD 5: the admin area is open to BOTH admin tiers. The super_admin is a
    // higher tier than admin, so the route group accepts both roles;
    // finer-grained powers (e.g. managing other admins) are enforced per-action
    // via User::canManage().
    Route::middleware('role:admin,super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
        // MOD 2: admin can ONLY delete jobs (no status change route).
        Route::delete('/jobs/{jobListing}', [AdminJobController::class, 'destroy'])->name('jobs.destroy');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        // MOD 4: deactivate/activate instead of delete (no users.destroy route).
        Route::put('/users/{user}/deactivate', [AdminUserController::class, 'deactivate'])->name('users.deactivate');
        Route::put('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        // Admin company moderation removed along with the companies table.
    });
});
