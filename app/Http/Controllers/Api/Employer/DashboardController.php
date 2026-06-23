<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $activeJobs = $user->jobListings()->where('status', 'active')->count();

        $totalApplications = Application::whereHas(
            'jobListing',
            fn ($q) => $q->where('user_id', $user->id)
        )->count();

        $recentApplications = Application::whereHas(
            'jobListing',
            fn ($q) => $q->where('user_id', $user->id)
        )
            ->with(['user', 'jobListing'])
            ->latest()
            ->limit(10)
            ->get();
//pure sql query to count the number of applications for each job status 
        $jobStats = $user->jobListings()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'active_jobs' => $activeJobs,
            'total_applications' => $totalApplications,
            'job_stats' => $jobStats,
            'recent_applications' => ApplicationResource::collection($recentApplications),
        ]);
    }
}
