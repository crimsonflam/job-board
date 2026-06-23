<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $jobs = JobListing::with(['user', 'category'])
            ->when($request->search, function ($q, $s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($u) => $u->where('company_name', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return JobListingResource::collection($jobs);
    }

    public function destroy(JobListing $jobListing)
    {
        $jobListing->delete();

        return response()->json(['message' => 'Job deleted successfully.']);
    }
}
