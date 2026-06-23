<?php

namespace App\Http\Controllers\Api\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
//afficahe show all application for seeker
public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', 'newest');

        $query = auth()->user()->applications()
            ->with('jobListing.user')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status));

        match ($sort) {
            'oldest' => $query->oldest(),
            'status' => $query->orderBy('status')->latest(),
            default => $query->latest(),
        };

        $applications = $query->paginate(15)->withQueryString();

        return ApplicationResource::collection($applications);
    }
//show a single application for seeker
    public function show(Application $application)
    {
        $this->authorizeOwner($application);
        $application->load('jobListing.user');

        return new ApplicationResource($application);
    }
//apply
    public function store(Request $request, JobListing $jobListing)
    {
        $user = auth()->user();

        if ($user->hasApplied($jobListing->id)) {
            return response()->json([
                'message' => 'You have already applied to this job.',
            ], 409);
        }

        if (!$user->hasDefaultResume()) {
            return response()->json([
                'message' => 'Please upload your resume in your profile before applying.',
                'redirect' => '/seeker/profile',
            ], 422);
        }

        $request->validate([
            'cv_choice' => ['required', 'in:default,upload'],
            'resume' => ['required_if:cv_choice,upload', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'resume.required_if' => 'Please choose a PDF file to upload, or use your default CV.',
        ]);

        if ($request->input('cv_choice') === 'upload' && $request->hasFile('resume')) {
            // Custom CV stored just for this application.
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $resumeFileName = $request->file('resume')->getClientOriginalName();
            $cvIsDefault = false;
        } else {
            // Use the user's default CV.
            $resumePath = $user->resume_path;
            $resumeFileName = $user->resume_file_name;
            $cvIsDefault = true;
        }

        $application = Application::create([
            'user_id' => $user->id,
            'job_listing_id' => $jobListing->id,
            'resume_path' => $resumePath,
            'resume_file_name' => $resumeFileName,
            'cv_is_default' => $cvIsDefault,
        ]);

        return (new ApplicationResource($application))
            ->additional(['message' => 'Application submitted successfully!'])
            ->response()
            ->setStatusCode(201);
    }
//check if the application belongs to the authenticated user
    private function authorizeOwner(Application $application): void
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
