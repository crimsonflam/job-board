<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with('user')
            ->withCount('jobListings')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20);

        return view('admin.companies.index', compact('companies'));
    }

    public function toggleVerification(Company $company)
    {
        $company->update(['is_verified' => !$company->is_verified]);
        $status = $company->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "Company {$status} successfully.");
    }
}
