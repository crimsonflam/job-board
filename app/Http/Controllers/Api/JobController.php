<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\JobListingResource;
use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Http\Request;

class JobController extends Controller
{
    // show all jobs 
    public function index(Request $request)
    {
        $jobs = JobListing::published()
            ->filter($request->only([
                'search', 'category', 'type', 'location',
                'experience', 'education', 'salary_min', 'salary_max',
            ]))
            ->with(['user', 'category'])
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::withCount(['jobListings' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return JobListingResource::collection($jobs)->additional([
            'categories' => CategoryResource::collection($categories),
        ]);
    }
// show a detail job
    public function show(Request $request, string $slug)
    {
        $job = JobListing::where('slug', $slug)
            ->with(['user', 'category'])
            ->firstOrFail();

        $myApplication = null;
        $user = $request->user();
        if ($user && $user->isSeeker()) {
            $myApplication = $user->applications()
                ->where('job_listing_id', $job->id)
                ->first();
        }

        return (new JobListingResource($job))->additional([
            'meta' => [ 
                //check if applied or not
                'my_application' => $myApplication
                    ? new ApplicationResource($myApplication)
                    : null,
            ],
        ]);
    }
}
