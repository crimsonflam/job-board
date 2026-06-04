<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Http\Request;

/**
 * ============================================================
 * WHAT: The PUBLIC job browsing surface — the Browse Jobs list and a single
 *       job's details page. No login required (guests can browse).
 * WHY:  Discovery is the core of the product, so it lives on open routes.
 *       Both methods only ever expose PUBLISHED/active jobs, never drafts or
 *       inactive listings.
 * HOW:  `index()` builds a filtered, paginated list via the model's `filter`
 *       scope; `show()` looks one job up by its slug.
 * ============================================================
 */
class JobController extends Controller
{
    /**
     * WHAT: Browse Jobs — search + filter + sort over published jobs.
     * WHY:  All filtering/sorting happens in the DB query (via the JobListing
     *       `filter` scope) so the page stays fast and pagination reflects the
     *       filtered set. Each filter is independent and optional.
     */
    public function index(Request $request)
    {
        // MOD 4: time-based ("posted date") sorting/filtering removed.
        // Sort is "newest" only; jobs show regardless of posting date.
        $jobs = JobListing::published()
            ->filter($request->only([
                'search', 'category', 'type', 'location',
                'experience', 'education', 'salary_min', 'salary_max',
            ]))
            ->with(['user', 'category']) // company info comes from the employer (user)
            ->latest('published_at')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::withCount(['jobListings' => fn ($q) => $q->published()])->get();

        return view('jobs.index', [
            'jobs' => $jobs,
            'categories' => $categories,
            // Filter option lists (value => label), defined on the model.
            'educationLevels' => JobListing::EDUCATION_LABELS,
            'jobTypes' => JobListing::TYPE_LABELS,
            'experienceLevels' => JobListing::EXPERIENCE_LABELS,
            'cities' => config('morocco.cities'),
        ]);
    }

    /**
     * WHAT: Show one job's full details page.
     * WHY:  Jobs are addressed by a human-readable `slug` (e.g. /jobs/senior-
     *       laravel-developer-1) rather than a numeric id — nicer URLs and
     *       harder to enumerate. Eager-loading the employer + category avoids
     *       extra queries in the view.
     * HOW:  Look the job up by slug; 404 if not found.
     * @param string $slug  The job's unique URL slug (from the route).
     */
    public function show(string $slug)
    {
        // MOD 17: no view tracking (views_count removed).
        // MOD 6: no "related jobs" — the page shows only this single job.
        // firstOrFail() throws a 404 if the slug matches no job, so a bad/old
        // link shows "not found" rather than a blank or broken page.
        $job = JobListing::where('slug', $slug)
            ->with(['user', 'category'])
            ->firstOrFail();

        return view('jobs.show', compact('job'));
    }
}
