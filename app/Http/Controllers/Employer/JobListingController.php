<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobListingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            return redirect()->route('employer.company.edit');
        }

        $jobs = $company->jobListings()
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return view('employer.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('employer.jobs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateJob($request);
        $company = auth()->user()->company;

        $job = $company->jobListings()->create([
            ...$validated,
            'user_id' => auth()->id(),
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'skills' => $validated['skills'] ? array_map('trim', explode(',', $validated['skills'])) : null,
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'expires_at' => $validated['status'] === 'published' ? now()->addDays(30) : null,
        ]);

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job listing created successfully!');
    }

    public function edit(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $categories = Category::all();
        return view('employer.jobs.edit', compact('jobListing', 'categories'));
    }

    public function update(Request $request, JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $validated = $this->validateJob($request);

        $wasPublished = $jobListing->status === 'published';

        $jobListing->update([
            ...$validated,
            'skills' => $validated['skills'] ? array_map('trim', explode(',', $validated['skills'])) : null,
            'published_at' => (!$wasPublished && $validated['status'] === 'published') ? now() : $jobListing->published_at,
            'expires_at' => (!$wasPublished && $validated['status'] === 'published') ? now()->addDays(30) : $jobListing->expires_at,
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

    public function applications(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $applications = $jobListing->applications()->with('user')->latest()->paginate(20);
        return view('employer.jobs.applications', compact('jobListing', 'applications'));
    }

    public function updateApplicationStatus(Request $request, \App\Models\Application $application)
    {
        $jobListing = $application->jobListing;
        $this->authorizeJob($jobListing);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,shortlisted,rejected,hired'],
            'employer_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $application->update($validated);

        return back()->with('success', 'Application status updated.');
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'type' => ['required', 'in:full-time,part-time,contract,freelance,internship'],
            'experience_level' => ['required', 'in:entry,mid,senior,lead'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_remote' => ['boolean'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'max:3'],
            'skills' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,closed'],
        ]);
    }

    private function authorizeJob(JobListing $job): void
    {
        if ($job->company_id !== auth()->user()->company?->id) {
            abort(403);
        }
    }
}
