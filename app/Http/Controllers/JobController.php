<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobListing::published()
            ->filter($request->only(['search', 'category', 'type', 'location', 'remote', 'experience', 'salary_min', 'salary_max']))
            ->with(['company', 'category'])
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::withCount(['jobListings' => fn ($q) => $q->published()])->get();

        return view('jobs.index', compact('jobs', 'categories'));
    }

    public function show(string $slug)
    {
        $job = JobListing::where('slug', $slug)
            ->with(['company', 'category'])
            ->firstOrFail();

        $job->increment('views_count');

        $relatedJobs = JobListing::published()
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('category_id', $job->category_id)
                    ->orWhere('company_id', $job->company_id);
            })
            ->with('company')
            ->limit(5)
            ->get();

        return view('jobs.show', compact('job', 'relatedJobs'));
    }
}
