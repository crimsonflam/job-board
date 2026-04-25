<?php

namespace App\Http\Controllers;

use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['jobListings' => fn ($q) => $q->published()])
            ->orderByDesc('is_verified')
            ->paginate(20);

        return view('companies.index', compact('companies'));
    }

    public function show(string $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $jobs = $company->jobListings()->published()->with('category')->latest()->paginate(10);

        return view('companies.show', compact('company', 'jobs'));
    }
}
