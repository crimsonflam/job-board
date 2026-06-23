<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Http\Resources\UserResource;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;
class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'deactivated_users' => User::where('status', 'deactivated')->count(),
            'seekers' => User::where('role', 'seeker')->count(),
            'employers' => User::where('role', 'employer')->count(),
            'total_jobs' => JobListing::count(),
            'active_jobs' => JobListing::where('status', 'active')->count(),
            'total_applications' => Application::count(),
        ];

        $recentJobs = JobListing::with('user')->latest()->limit(10)->get();
        $recentUsers = User::latest()->limit(10)->get();

        return response()->json([
            'stats' => $stats,
            'recent_jobs' => JobListingResource::collection($recentJobs),
            'recent_users' => UserResource::collection($recentUsers),
        ]);
    }
}
