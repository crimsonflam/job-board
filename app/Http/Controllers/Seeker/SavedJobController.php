<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Models\SavedJob;
use Illuminate\Http\Request;

/**
 * ============================================================
 * WHAT: Manages a seeker's saved (bookmarked) jobs — the list page and the
 *       save/unsave toggle behind the heart icon on each job card.
 * WHY:  Seekers want to shortlist jobs to revisit later. Bookmarks are stored
 *       as SavedJob rows (user ↔ job), so they persist across sessions.
 * HOW:  `index()` shows saved jobs (reusing the Browse filters); `toggle()`
 *       adds or removes one bookmark. All methods are behind the seeker role
 *       middleware, so `auth()->id()` is always the acting seeker.
 * ============================================================
 */
class SavedJobController extends Controller
{
    /**
     * MOD 7: Saved Jobs page. Lists the seeker's bookmarked jobs using the
     * SAME filters as Browse Jobs. We query JobListing (not SavedJob) so the
     * shared `filter` scope and the job-card component work unchanged, scoping
     * to jobs the user has saved via a whereHas on savedBy.
     */
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

        return view('seeker.saved-jobs.index', [
            'jobs' => $jobs,
            'educationLevels' => JobListing::EDUCATION_LABELS,
            'jobTypes' => JobListing::TYPE_LABELS,
            'experienceLevels' => JobListing::EXPERIENCE_LABELS,
            'cities' => config('morocco.cities'),
        ]);
    }

    /**
     * WHAT: Toggle a single job's saved state for the current seeker.
     * WHY:  One endpoint serves both "save" and "unsave" — the heart icon flips
     *       between filled/outline, so a single toggle is simpler than separate
     *       save/delete routes and naturally prevents duplicate bookmarks.
     * HOW:  Look for an existing bookmark; if present, delete it (unsave),
     *       otherwise create it (save). Either way redirect back with a flash.
     * @param JobListing $jobListing  Resolved from the route by its id.
     */
    public function toggle(JobListing $jobListing)
    {
        // Is this job already saved by the current seeker?
        $existing = SavedJob::where('user_id', auth()->id())
            ->where('job_listing_id', $jobListing->id)
            ->first();

        // Already saved → this click means "unsave": remove the bookmark.
        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Job removed from saved list.');
        }

        // Not saved yet → create the bookmark. (The DB unique index on
        // user_id+job_listing_id is a backstop against accidental duplicates.)
        SavedJob::create([
            'user_id' => auth()->id(),
            'job_listing_id' => $jobListing->id,
        ]);

        return back()->with('success', 'Job saved successfully!');
    }
}
