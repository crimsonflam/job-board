<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Employer company profile (text-only — no logo/branding). Company fields live
 * on the employer's own user record. Same rules as the old Blade
 * CompanyProfileController.
 */
class CompanyProfileController extends Controller
{
    public function show()
    {
        return new UserResource(auth()->user());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:5000'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'company_location' => ['nullable', Rule::in(config('morocco.cities'))],
            'industry' => ['nullable', 'string', 'max:255'],
        ], [
            'company_location.in' => 'Please choose a valid Moroccan city.',
        ]);

        auth()->user()->update($validated);

        return (new UserResource(auth()->user()->fresh()))->additional([
            'message' => 'Company profile updated!',
        ]);
    }
}
