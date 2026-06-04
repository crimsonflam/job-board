<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;

/**
 * ============================================================
 * WHAT: The employer's home dashboard — active-job count, total applications,
 *       a recent-applications feed, and a per-status job breakdown.
 * WHY:  Gives employers an overview of their hiring pipeline in one place.
 * HOW:  All metrics are scoped to jobs the employer owns (job_listings.user_id).
 *       EDGE CASE: an employer with no company profile yet is redirected to set
 *       one up first — you can't meaningfully have jobs/stats without it.
 * ============================================================
 */
class EmployerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // An employer must set up their company profile (at minimum a
        // company name) before they can post jobs or see dashboard stats.
        // Company data now lives on the user, so we check the user directly.
        if (!$user->hasCompanyProfile()) {
            return redirect()->route('employer.company.edit')
                ->with('info', 'Please set up your company profile first.');
        }

        // All of this employer's jobs are simply their own job listings now
        // (job_listings.user_id), instead of being scoped through a company.
        $activeJobs = $user->jobListings()->where('status', 'active')->count();

        $totalApplications = \App\Models\Application::whereHas(
            'jobListing',
            fn ($q) => $q->where('user_id', $user->id)
        )->count();

        $recentApplications = \App\Models\Application::whereHas(
            'jobListing',
            fn ($q) => $q->where('user_id', $user->id)
        )
            ->with(['user', 'jobListing'])
            ->latest()
            ->limit(10)
            ->get();

        $jobStats = $user->jobListings()
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        // The view still receives `$company` for its header/labels — it is
        // now the employer's own user record (which carries company_name, etc.).
        return view('employer.dashboard', [
            'company' => $user,
            'activeJobs' => $activeJobs,
            'totalApplications' => $totalApplications,
            'recentApplications' => $recentApplications,
            'jobStats' => $jobStats,
        ]);
    }
}
