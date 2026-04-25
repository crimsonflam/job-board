<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('seeker.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'string'],
            'expected_salary' => ['nullable', 'string', 'max:50'],
            'availability' => ['required', 'in:available,open,not_available'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $user = auth()->user();

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        if (isset($validated['skills'])) {
            $validated['skills'] = array_map('trim', explode(',', $validated['skills']));
        }

        unset($validated['resume'], $validated['avatar']);
        if (!$request->hasFile('avatar')) unset($validated['avatar']);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }
}
