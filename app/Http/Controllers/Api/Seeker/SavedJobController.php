<?php

namespace App\Http\Controllers\Api\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use App\Models\SavedJob;
use Illuminate\Http\Request;

/**
 * Saved (bookmarked) jobs — the list (reusing the Browse filters) and the
 * save/unsave toggle behind the heart icon. Same query/behaviour as the old
 * Blade SavedJobController.
 */
class SavedJobController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $jobs = JobListing::whereHas('savedBy', fn ($q) => $q->where('user_id', $userId))
            ->filter($request->only([
                'search', 'category', 'type', 'location',
                'experience', 'education', 'salary_min', 'salary_max',
            ]))
            ->with(['user', 'category'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return JobListingResource::collection($jobs);
    }

    /** Toggle one job's saved state for the current seeker (save ⇄ unsave). */
    public function toggle(JobListing $jobListing)
    {
        $existing = SavedJob::where('user_id', auth()->id())
            ->where('job_listing_id', $jobListing->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'saved' => false,
                'message' => 'Job removed from saved list.',
            ]);
        }

        SavedJob::create([
            'user_id' => auth()->id(),
            'job_listing_id' => $jobListing->id,
        ]);

        return response()->json([
            'saved' => true,
            'message' => 'Job saved successfully!',
        ]);
    }
}
