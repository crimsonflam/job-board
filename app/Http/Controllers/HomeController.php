<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\JobListing;

class HomeController extends Controller
{
    public function index()
    {
        $featuredJobs = JobListing::published()
            ->featured()
            ->with('company')
            ->latest('published_at')
            ->limit(6)
            ->get();

        $latestJobs = JobListing::published()
            ->with(['company', 'category'])
            ->latest('published_at')
            ->limit(10)
            ->get();

        $categories = Category::withCount(['jobListings' => fn ($q) => $q->published()])->get();

        $topCompanies = Company::where('is_verified', true)
            ->withCount(['jobListings' => fn ($q) => $q->published()])
            ->orderByDesc('job_listings_count')
            ->limit(6)
            ->get();

        $stats = [
            'jobs' => JobListing::published()->count(),
            'companies' => Company::count(),
            'categories' => Category::count(),
        ];

        return view('home', compact('featuredJobs', 'latestJobs', 'categories', 'topCompanies', 'stats'));
    }
}
