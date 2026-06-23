<?php

namespace App\Http\Controllers\Api\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;

/**
 * The job seeker's dashboard snapshot — recent applications + headline counts.
 * Mirrors the old SeekerDashboardController (5 most recent applications, saved
 * + applications + pending counts), returned as JSON for the React dashboard.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $recentApplications = $user->applications()
            ->with('jobListing.user')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'recent_applications' => ApplicationResource::collection($recentApplications),
            'applications_count' => $user->applications()->count(),
            'saved_jobs_count' => $user->savedJobs()->count(),
            'pending_count' => $user->applications()->where('status', 'pending')->count(),
        ]);
    }
}
