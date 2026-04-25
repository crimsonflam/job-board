<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\JobListing;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_seekers' => User::where('role', 'seeker')->count(),
            'total_employers' => User::where('role', 'employer')->count(),
            'total_jobs' => JobListing::count(),
            'active_jobs' => JobListing::where('status', 'published')->count(),
            'total_applications' => Application::count(),
            'total_companies' => Company::count(),
            'verified_companies' => Company::where('is_verified', true)->count(),
        ];

        $recentJobs = JobListing::with('company')->latest()->limit(10)->get();
        $recentUsers = User::latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentUsers'));
    }
}
