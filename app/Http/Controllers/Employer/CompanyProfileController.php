<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('employer.company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'in:1-10,11-50,51-200,201-500,500+'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('companies/banners', 'public');
        } else {
            unset($validated['banner']);
        }

        $company = auth()->user()->company;

        if ($company) {
            $company->update($validated);
        } else {
            $validated['user_id'] = auth()->id();
            $validated['slug'] = Str::slug($validated['name']) . '-' . auth()->id();
            Company::create($validated);
        }

        return back()->with('success', 'Company profile updated!');
    }
}
