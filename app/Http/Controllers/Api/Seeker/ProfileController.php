<?php

namespace App\Http\Controllers\Api\Seeker;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return new UserResource(auth()->user());
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone' => ['required', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'bio' => ['nullable', 'string', 'max:250'],
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'resume.mimes' => 'Only PDF files are allowed.',
            'resume.max' => 'The CV must not be larger than 5MB.',
        ]);

        $user = auth()->user();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'website' => $validated['website'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        if ($request->hasFile('resume')) {
            if ($user->resume_path) {
                Storage::disk('public')->delete($user->resume_path);
            }
            $user->resume_path = $request->file('resume')->store('resumes', 'public');
            $user->resume_file_name = $request->file('resume')->getClientOriginalName();
            $user->resume_uploaded_at = now();
        }

        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Profile updated successfully!',
        ]);
    }

    public function downloadCv()
    {
        $user = auth()->user();
        abort_unless($user->resume_path && Storage::disk('public')->exists($user->resume_path), 404);

        return Storage::disk('public')->download(
            $user->resume_path,
            $user->resume_file_name ?? 'cv.pdf'
        );
    }

    public function deleteCv()
    {
        $user = auth()->user();

        if ($user->resume_path) {
            Storage::disk('public')->delete($user->resume_path);
        }

        $user->update([
            'resume_path' => null,
            'resume_file_name' => null,
            'resume_uploaded_at' => null,
        ]);

        return (new UserResource($user->fresh()))->additional([
            'message' => 'CV deleted.',
        ]);
    }
}
