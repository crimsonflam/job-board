<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;

class SeekerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recentApplications = $user->applications()->with('jobListing.company')->latest()->limit(5)->get();
        $savedJobsCount = $user->savedJobs()->count();
        $applicationsCount = $user->applications()->count();
        $activeAlertsCount = $user->jobAlerts()->where('is_active', true)->count();

        return view('seeker.dashboard', compact('user', 'recentApplications', 'savedJobsCount', 'applicationsCount', 'activeAlertsCount'));
    }
}
