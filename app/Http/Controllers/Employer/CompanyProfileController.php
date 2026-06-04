<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| WHAT: Lets an employer view and edit their company profile.
| WHY:  Company fields live on the EMPLOYER's user record (company_name,
|       company_description, company_location, company_website, industry).
| MOD 18: Company branding/images removed — there is no logo/banner upload.
|       The profile is text-only. Location must be a Moroccan city.
|--------------------------------------------------------------------------
*/
class CompanyProfileController extends Controller
{
    public function edit()
    {
        // The employer's own user record carries the company fields.
        return view('employer.company.edit', [
            'company' => auth()->user(),
            'cities' => config('morocco.cities'),
        ]);
    }

    public function update(Request $request)
    {
        // MOD 18: text fields only — no image/logo handling.
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:5000'],
            'company_website' => ['nullable', 'url', 'max:255'],
            // MOD 18/2: company location is a Moroccan city from the list.
            'company_location' => ['nullable', \Illuminate\Validation\Rule::in(config('morocco.cities'))],
            'industry' => ['nullable', 'string', 'max:255'],
        ], [
            'company_location.in' => 'Please choose a valid Moroccan city.',
        ]);

        // Persist the company fields straight onto the employer's user row.
        auth()->user()->update($validated);

        return back()->with('success', 'Company profile updated!');
    }
}
