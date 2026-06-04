<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;

/**
 * ============================================================
 * WHAT: The job seeker's home dashboard — a snapshot of their activity.
 * WHY:  Gives the seeker an at-a-glance summary (recent applications + counts)
 *       and a jumping-off point to Browse Jobs / My Applications / Saved Jobs.
 * HOW:  Pull a few cheap aggregates and the 5 most recent applications for the
 *       logged-in user, then hand them to the dashboard view.
 * ============================================================
 */
class SeekerDashboardController extends Controller
{
    public function index()
    {
        // The logged-in seeker (route is behind seeker-role middleware).
        $user = auth()->user();

        // The 5 most recent applications, eager-loading each job AND its
        // employer (jobListing.user) so the view can show the company name
        // without firing an extra query per row (avoids the N+1 problem).
        $recentApplications = $user->applications()->with('jobListing.user')->latest()->limit(5)->get();

        // Headline counts for the stat cards. Run as COUNT queries (not loading
        // the rows) so they stay fast regardless of how much history exists.
        $savedJobsCount = $user->savedJobs()->count();
        $applicationsCount = $user->applications()->count();
        // MOD 8: job-alerts removed — no active-alerts count anymore.

        return view('seeker.dashboard', compact('user', 'recentApplications', 'savedJobsCount', 'applicationsCount'));
    }
}
