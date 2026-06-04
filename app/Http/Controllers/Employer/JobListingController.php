<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobListingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Require a company profile (company name) before managing jobs.
        if (!$user->hasCompanyProfile()) {
            return redirect()->route('employer.company.edit');
        }

        // An employer's jobs are their own job listings (user_id).
        // (View expects `$jobListings`.)
        $jobListings = $user->jobListings()
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return view('employer.jobs.index', compact('jobListings'));
    }

    /**
     * MOD 16: Toggle a job between Active and Inactive — the only two states now
     * (no draft). Active = visible to seekers; Inactive = hidden but kept in DB.
     */
    public function toggleStatus(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);

        if ($jobListing->status === 'active') {
            $jobListing->update(['status' => 'inactive']);
            $message = 'Job set to inactive — it is no longer visible to seekers.';
        } else {
            $jobListing->update([
                'status' => 'active',
                'published_at' => $jobListing->published_at ?? now(),
            ]);
            $message = 'Job set to active — it is now visible to seekers.';
        }

        return back()->with('success', $message);
    }

    public function create()
    {
        $categories = Category::all();
        // MOD 2/14: the location dropdown needs the Moroccan cities list.
        $cities = config('morocco.cities');
        return view('employer.jobs.create', compact('categories', 'cities'));
    }

    /**
     * MOD 16: Jobs auto-publish on creation (status='active', published now) —
     * there is no draft state. The employer can later deactivate or delete.
     */
    public function store(Request $request)
    {
        $validated = $this->validateJob($request);

        auth()->user()->jobListings()->create([
            ...$validated,
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            // `skills` is optional; use null-safe access (?? null) so an omitted
            // field doesn't trigger an "undefined array key" error.
            'skills' => !empty($validated['skills']) ? array_map('trim', explode(',', $validated['skills'])) : null,
            'status' => 'active',          // MOD 16: published immediately
            'published_at' => now(),
        ]);

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job published successfully!');
    }

    public function edit(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $categories = Category::all();
        $cities = config('morocco.cities');
        return view('employer.jobs.edit', compact('jobListing', 'categories', 'cities'));
    }

    public function update(Request $request, JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $validated = $this->validateJob($request);

        // Status is managed via the Active/Inactive toggle, not this form,
        // so we don't touch it here.
        $jobListing->update([
            ...$validated,
            'skills' => !empty($validated['skills']) ? array_map('trim', explode(',', $validated['skills'])) : null,
        ]);

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job listing updated successfully!');
    }

    public function destroy(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $jobListing->delete();

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job listing deleted.');
    }

    /**
     * Applicants for ONE specific job (reached from "View Applicants" on a job
     * row). Delegates to the shared applicants view, pre-filtered to this job.
     */
    public function applications(Request $request, JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        // Re-use the unified applicants screen, locked to this job.
        $request->merge(['job' => $jobListing->id]);
        return $this->applicants($request);
    }

    /**
     * WHAT: Unified "Applicants" screen — every applicant across ALL of this
     *       employer's jobs, with search (by name), filter (by job + status)
     *       and sort.
     * WHY:  Employers manage candidates in one place rather than hopping
     *       between per-job lists. All filtering is done in the query.
     */
    public function applicants(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasCompanyProfile()) {
            return redirect()->route('employer.company.edit');
        }

        $jobFilter = $request->input('job');     // a job id, or null for "all jobs"
        $statusFilter = $request->input('status', 'all');
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $applications = \App\Models\Application::query()
            // Only applications to jobs owned by this employer.
            ->whereHas('jobListing', fn ($q) => $q->where('user_id', $user->id))
            ->with(['user', 'jobListing'])
            ->when($jobFilter, fn ($q, $jobId) => $q->where('job_listing_id', $jobId))
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            // Search by applicant name.
            ->when($search, fn ($q, $s) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")))
            ->when($sort === 'oldest', fn ($q) => $q->oldest())
            ->when($sort === 'status', fn ($q) => $q->orderBy('status')->latest())
            ->when(!in_array($sort, ['oldest', 'status']), fn ($q) => $q->latest())
            ->paginate(20)
            ->withQueryString();

        // For the "filter by job" dropdown.
        $jobs = $user->jobListings()->orderBy('title')->get(['id', 'title']);

        return view('employer.applicants', compact('applications', 'jobs', 'jobFilter', 'statusFilter', 'search', 'sort'));
    }

    /**
     * WHAT: Employer responds to an applicant — accept or reject — with a
     *       message (sent from the Reply modal on the Applicants page).
     * WHY:  The seeker-facing status model is pending → accepted | rejected.
     *       A decision REQUIRES a message (10–500 chars) so the applicant
     *       always gets context. We stamp `responded_at` for "Replied on …".
     */
    public function updateApplicationStatus(Request $request, \App\Models\Application $application)
    {
        $jobListing = $application->jobListing;
        $this->authorizeJob($jobListing);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
            // Message is mandatory on a decision, 10–500 chars (matches the UI counter).
            'response_message' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'response_message.required' => 'Please write a message to the applicant.',
            'response_message.min' => 'Your message should be at least 10 characters.',
        ]);

        $application->update([
            'status' => $validated['status'],
            'response_message' => $validated['response_message'],
            'responded_at' => now(),
        ]);

        $name = $application->user->name;
        return back()->with('success', "Response sent to {$name}.");
    }

    /**
     * ============================================================
     * MOD 2/3/13/14/15: Job validation for the new Moroccan model.
     * WHY:
     *  - Job type is one of four (full-time/part-time/remote/internship).
     *  - Location must be a Moroccan city from the predefined list, EXCEPT
     *    for remote jobs which have NO location. Backend enforces:
     *      remote      → location must be empty (set to NULL)
     *      non-remote  → location is required and must be a known city
     *  - Education uses the Bac/Bac+N scale; experience is required.
     *  - Salary is in MAD (no currency field).
     * ============================================================
     */
    private function validateJob(Request $request): array
    {
        $cities = config('morocco.cities');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            // MOD 2/14: four job types only.
            'type' => ['required', 'in:full-time,part-time,remote,internship'],
            // MOD 13: experience level is now required.
            'experience_level' => ['required', 'in:entry_level,mid_level,senior,lead'],
            // MOD 3: Moroccan education scale.
            'education_level' => ['required', 'in:none,bac,bac+2,bac+3,bac+5'],
            // MOD 2/14: location required for non-remote, must be a known city;
            // for remote it must be absent. The closure enforces both directions.
            'location' => [
                'nullable',
                'required_unless:type,remote',
                Rule::in($cities),
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') === 'remote' && filled($value)) {
                        $fail('Remote jobs cannot have a location.');
                    }
                },
            ],
            // MOD 15: salary in MAD, no currency.
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'skills' => ['nullable', 'string'],
        ], [
            'location.required_unless' => 'Location is required for non-remote positions.',
            'location.in' => 'Please choose a valid Moroccan city.',
        ]);

        // MOD 14: remote jobs store NULL location regardless of what was posted.
        if ($validated['type'] === 'remote') {
            $validated['location'] = null;
        }

        return $validated;
    }

    /**
     * MOD 11: Download an applicant's CV — forced download (no in-app preview).
     * Authorization: the application must belong to one of this employer's jobs.
     */
    public function downloadCv(\App\Models\Application $application)
    {
        $this->authorizeJob($application->jobListing);

        abort_unless(
            $application->resume_path && Storage::disk('public')->exists($application->resume_path),
            404
        );

        // Content-Disposition: attachment forces the browser to download.
        return Storage::disk('public')->download(
            $application->resume_path,
            $application->resume_file_name ?? 'cv.pdf'
        );
    }

    private function authorizeJob(JobListing $job): void
    {
        // A job belongs to the employer via user_id now.
        if ($job->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
