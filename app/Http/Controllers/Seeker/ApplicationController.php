<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * WHAT: "My Applications" — every job the seeker applied to, with the
     *       employer's response status and message.
     * WHY:  Seekers track outcomes here. We support filtering by status
     *       (all / pending / accepted / rejected) and sorting (newest /
     *       oldest / by status) entirely in the query.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', 'newest');

        $query = auth()->user()->applications()
            ->with('jobListing.user') // company info comes from the job's employer
            ->when($status !== 'all', fn ($q) => $q->where('status', $status));

        match ($sort) {
            'oldest' => $query->oldest(),
            // "By status" groups accepted → rejected → pending alphabetically,
            // then newest within each group.
            'status' => $query->orderBy('status')->latest(),
            default => $query->latest(),
        };

        $applications = $query->paginate(15)->withQueryString();

        return view('seeker.applications.index', compact('applications', 'status', 'sort'));
    }

    /**
     * Applying now happens via a modal on the job details page, so the old
     * standalone "create" page is no longer used — redirect any direct hits
     * to the job page where the Apply modal lives.
     */
    public function create(JobListing $jobListing)
    {
        return redirect()->route('jobs.show', $jobListing->slug);
    }

    /**
     * WHAT: Stores a new application, recording WHICH CV was used.
     * WHY:  The CV is stored on the application (resume_path / resume_file_name
     *       / cv_is_default) — never written back to the user's profile — so a
     *       later change to the seeker's default CV does NOT alter applications
     *       they've already submitted. The employer always sees the exact CV
     *       that was sent with each application.
     * GATE: A seeker must have a default CV on file before applying at all.
     */
    public function store(Request $request, JobListing $jobListing)
    {
        $user = auth()->user();

        if ($user->hasApplied($jobListing->id)) {
            return back()->with('error', 'You have already applied to this job.');
        }

        // Resume requirement: no default CV → cannot apply (send them to profile).
        if (!$user->hasDefaultResume()) {
            return redirect()->route('seeker.profile.edit')
                ->with('error', 'Please upload your resume in your profile before applying.');
        }

        // 'default' = reuse the profile CV; 'upload' = a new file for this job only.
        $request->validate([
            'cv_choice' => ['required', 'in:default,upload'],
            // The uploaded file is required only when the seeker chose "upload".
            'resume' => ['required_if:cv_choice,upload', 'nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'resume.required_if' => 'Please choose a PDF file to upload, or use your default CV.',
        ]);

        if ($request->input('cv_choice') === 'upload' && $request->hasFile('resume')) {
            // Custom CV: store the uploaded file just for this application.
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $resumeFileName = $request->file('resume')->getClientOriginalName();
            $cvIsDefault = false;
        } else {
            // Default CV: copy the profile CV's path + name onto the application.
            // We copy the values (not just reference the user) so the snapshot
            // is frozen at apply time.
            $resumePath = $user->resume_path;
            $resumeFileName = $user->resume_file_name;
            $cvIsDefault = true;
        }

        Application::create([
            'user_id' => $user->id,
            'job_listing_id' => $jobListing->id,
            'resume_path' => $resumePath,
            'resume_file_name' => $resumeFileName,
            'cv_is_default' => $cvIsDefault,
        ]);

        return redirect()->route('seeker.applications.index')
            ->with('success', 'Application submitted successfully!');
    }

    public function show(Application $application)
    {
        $this->authorize($application);
        $application->load('jobListing.user');
        return view('seeker.applications.show', compact('application'));
    }

    private function authorize(Application $application): void
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
