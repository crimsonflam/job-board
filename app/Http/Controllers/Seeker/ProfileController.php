<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('seeker.profile.edit', ['user' => auth()->user()]);
    }

    /**
     * ============================================================
     * MOD 9: Employee profile simplified to essential info only.
     * WHY:  Profile pictures were removed to cut file-handling complexity;
     *       a seeker's only uploaded file is their CV. We keep name, email,
     *       phone, website, bio (max 250) and the CV.
     * MOD 10: CV is PDF-only, max 5MB. We also record the original filename
     *       and upload timestamp so the profile can show "MyResume.pdf —
     *       uploaded on …" with download/replace/delete controls.
     * ============================================================
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Email is required + unique (ignoring the user's own row).
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'phone' => ['required', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            // MOD 9: bio capped at 250 characters (a counter enforces this in the UI too).
            'bio' => ['nullable', 'string', 'max:250'],
            // MOD 10: PDF ONLY — reject doc/docx/etc. with a clear message.
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'resume.mimes' => 'Only PDF files are allowed.',
            'resume.max' => 'The CV must not be larger than 5MB.',
        ]);

        $user = auth()->user();

        // Only the editable text fields are mass-assigned here.
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'website' => $validated['website'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        // CV upload: replace any previous file, and store name + timestamp.
        if ($request->hasFile('resume')) {
            // Delete the old CV file (if any) to avoid orphaned storage.
            if ($user->resume_path) {
                Storage::disk('public')->delete($user->resume_path);
            }
            $user->resume_path = $request->file('resume')->store('resumes', 'public');
            $user->resume_file_name = $request->file('resume')->getClientOriginalName();
            $user->resume_uploaded_at = now();
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * MOD 10: Download the seeker's own CV (forced download, not preview).
     */
    public function downloadCv()
    {
        $user = auth()->user();
        abort_unless($user->resume_path && Storage::disk('public')->exists($user->resume_path), 404);

        // Content-Disposition: attachment forces a download in the browser.
        return Storage::disk('public')->download(
            $user->resume_path,
            $user->resume_file_name ?? 'cv.pdf'
        );
    }

    /**
     * MOD 10: Delete the seeker's CV (with a confirmation dialog in the UI).
     * Clears both the file and its metadata.
     */
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

        return back()->with('success', 'CV deleted.');
    }
}
