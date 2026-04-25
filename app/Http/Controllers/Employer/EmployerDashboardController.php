<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;

class EmployerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('employer.company.edit')
                ->with('info', 'Please set up your company profile first.');
        }

        $activeJobs = $company->jobListings()->where('status', 'published')->count();
        $totalApplications = \App\Models\Application::whereHas('jobListing', fn ($q) => $q->where('company_id', $company->id))->count();
        $recentApplications = \App\Models\Application::whereHas('jobListing', fn ($q) => $q->where('company_id', $company->id))
            ->with(['user', 'jobListing'])
            ->latest()
            ->limit(10)
            ->get();
        $jobStats = $company->jobListings()
            ->selectRaw("status, COUNT(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('employer.dashboard', compact('company', 'activeJobs', 'totalApplications', 'recentApplications', 'jobStats'));
    }
}
