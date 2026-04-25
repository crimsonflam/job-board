<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use App\Models\SavedJob;

class SavedJobController extends Controller
{
    public function index()
    {
        $savedJobs = auth()->user()->savedJobs()
            ->with('jobListing.company')
            ->latest()
            ->paginate(15);

        return view('seeker.saved-jobs.index', compact('savedJobs'));
    }

    public function toggle(JobListing $jobListing)
    {
        $existing = SavedJob::where('user_id', auth()->id())
            ->where('job_listing_id', $jobListing->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Job removed from saved list.');
        }

        SavedJob::create([
            'user_id' => auth()->id(),
            'job_listing_id' => $jobListing->id,
        ]);

        return back()->with('success', 'Job saved successfully!');
    }
}
