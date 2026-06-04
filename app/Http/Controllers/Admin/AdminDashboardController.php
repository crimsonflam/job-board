<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\User;

/**
 * ============================================================
 * WHAT: The admin dashboard — platform-wide stats plus recent jobs and users.
 * WHY:  Gives admins/super-admins a health snapshot of the whole platform
 *       (user counts by status/role, job + application totals) and quick links
 *       into the management screens. Reachable by both admin tiers.
 * HOW:  Compute a handful of COUNT aggregates into a $stats array and load the
 *       10 most recent jobs and users for the activity tables.
 * ============================================================
 */
class AdminDashboardController extends Controller
{
    public function index()
    {
        // MOD 6: "Total Companies" stat removed. The dashboard now focuses on
        // user + job metrics, including the new active/deactivated user counts
        // (MOD 4). Companies aren't a separate entity, so a company count is
        // not meaningful here.
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

        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentUsers'));
    }
}
