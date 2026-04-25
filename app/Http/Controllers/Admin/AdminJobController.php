<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobListing::with(['company', 'category'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20);

        return view('admin.jobs.index', compact('jobs'));
    }

    public function updateStatus(Request $request, JobListing $jobListing)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,published,closed,expired'],
        ]);

        $jobListing->update($validated);

        return back()->with('success', 'Job status updated.');
    }

    public function destroy(JobListing $jobListing)
    {
        $jobListing->delete();
        return back()->with('success', 'Job listing deleted.');
    }
}
