<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Http\Resources\JobListingResource;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobListingController extends Controller
{
    public function index()
    {
        $jobListings = auth()->user()->jobListings()
            ->withCount('applications')
            ->latest()
            ->paginate(15);

        return JobListingResource::collection($jobListings);
    }

    public function show(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);

        return new JobListingResource($jobListing);
    }

    public function store(Request $request)
    {
        $validated = $this->validateJob($request);

        $job = auth()->user()->jobListings()->create([
            ...$validated,
            //slug is unique id for url for each job listing
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'skills' => !empty($validated['skills'])
                ? array_map('trim', explode(',', $validated['skills']))
                : null,
            'status' => 'active',
            'published_at' => now(),
        ]);

        return (new JobListingResource($job))
            ->additional(['message' => 'Job published successfully!'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $validated = $this->validateJob($request);

        $jobListing->update([
            ...$validated,
            'skills' => !empty($validated['skills'])
                ? array_map('trim', explode(',', $validated['skills']))
                : null,
        ]);

        return (new JobListingResource($jobListing->fresh()))->additional([
            'message' => 'Job listing updated successfully!',
        ]);
    }

    public function destroy(JobListing $jobListing)
    {
        $this->authorizeJob($jobListing);
        $jobListing->delete();

        return response()->json(['message' => 'Job listing deleted.']);
    }

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

        return (new JobListingResource($jobListing->fresh()))->additional([
            'message' => $message,
        ]);
    }

    public function applicants(Request $request)
    {
        $user = auth()->user();

        $jobFilter = $request->input('job');
        $statusFilter = $request->input('status', 'all');
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $applications = Application::query()
            ->whereHas('jobListing', fn ($q) => $q->where('user_id', $user->id))
            ->with(['user', 'jobListing'])
            ->when($jobFilter, fn ($q, $jobId) => $q->where('job_listing_id', $jobId))
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->when($search, fn ($q, $s) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")))
            ->when($sort === 'oldest', fn ($q) => $q->oldest())
            ->when($sort === 'status', fn ($q) => $q->orderBy('status')->latest())
            ->when(!in_array($sort, ['oldest', 'status']), fn ($q) => $q->latest())
            ->paginate(20)
            ->withQueryString();

        // For the filter by job dropdown.
        $jobs = $user->jobListings()->orderBy('title')->get(['id', 'title']);

        return ApplicationResource::collection($applications)->additional([
            'jobs' => $jobs,
        ]);
    }

    // Accept/reject 
    public function updateApplicationStatus(Request $request, Application $application)
    {
        $this->authorizeJob($application->jobListing);

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
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

        return (new ApplicationResource($application->fresh()->load(['user', 'jobListing'])))
            ->additional(['message' => "Response sent to {$name}."]);
    }

    public function downloadCv(Application $application)
    {
        $this->authorizeJob($application->jobListing);

        abort_unless(
            $application->resume_path && Storage::disk('public')->exists($application->resume_path),
            404
        );

        return Storage::disk('public')->download(
            $application->resume_path,
            $application->resume_file_name ?? 'cv.pdf'
        );
    }
//this is checking if the data entered 
// follows the valid types given or not before running the query right
    private function validateJob(Request $request): array
    {
        $cities = config('morocco.cities');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'type' => ['required', 'in:full-time,part-time,remote,internship'],
            'experience_level' => ['required', 'in:entry_level,mid_level,senior,lead'],
            'education_level' => ['required', 'in:none,bac,bac+2,bac+3,bac+5'],
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
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'skills' => ['nullable', 'string'],
        ], [
            'location.required_unless' => 'Location is required for non-remote positions.',
            'location.in' => 'Please choose a valid Moroccan city.',
        ]);

        if ($validated['type'] === 'remote') {
            $validated['location'] = null;
        }

        return $validated;
    }

    private function authorizeJob(JobListing $job): void
    {
        if ($job->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
