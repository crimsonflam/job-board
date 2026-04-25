<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobListing;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = auth()->user()->applications()
            ->with('jobListing.company')
            ->latest()
            ->paginate(15);

        return view('seeker.applications.index', compact('applications'));
    }

    public function create(JobListing $jobListing)
    {
        if (auth()->user()->hasApplied($jobListing->id)) {
            return back()->with('error', 'You have already applied to this job.');
        }

        return view('seeker.applications.create', compact('jobListing'));
    }

    public function store(Request $request, JobListing $jobListing)
    {
        if (auth()->user()->hasApplied($jobListing->id)) {
            return back()->with('error', 'You have already applied to this job.');
        }

        $validated = $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        Application::create([
            'user_id' => auth()->id(),
            'job_listing_id' => $jobListing->id,
            'cover_letter' => $validated['cover_letter'],
            'resume_path' => $resumePath,
        ]);

        return redirect()->route('seeker.applications.index')
            ->with('success', 'Application submitted successfully!');
    }

    public function show(Application $application)
    {
        $this->authorize($application);
        $application->load('jobListing.company');
        return view('seeker.applications.show', compact('application'));
    }

    private function authorize(Application $application): void
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
