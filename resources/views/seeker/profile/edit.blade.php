@extends('layouts.app')

@section('title', 'Edit Profile')

{{--
    ============================================================
    WHAT: Job-seeker profile edit (simplified).
    MOD 9:  Fields kept = Full Name, Email, Phone, Website, Bio (max 250, with
            counter), and the CV. NO avatar or any image upload — removed to
            reduce complexity.
    MOD 10: CV is PDF ONLY, max 5MB. Once uploaded we show the file name,
            upload date, and Download / Replace / Delete controls.
    ============================================================
--}}

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ bio: @js(old('bio', $user->bio ?? '')) }">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
        <p class="mt-1 text-sm text-gray-500">Keep your details up to date. Your CV is required before you can apply to jobs.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ---------- Personal info form (MOD 9). No file upload here. ---------- --}}
    <form action="{{ route('seeker.profile.update') }}" method="POST"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required maxlength="100"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required maxlength="20"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                placeholder="Phone Number">
        </div>

        <div>
            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="url" name="website" id="website" value="{{ old('website', $user->website) }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                placeholder="Website URL">
        </div>

        {{-- Bio with a live counter capped at 250 (MOD 9). --}}
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea name="bio" id="bio" rows="4" maxlength="250" x-model="bio"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                placeholder="Bio"></textarea>
            <p class="mt-1 text-right text-xs text-gray-400"><span x-text="bio.length"></span>/250</p>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('seeker.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Save</button>
        </div>
    </form>

    {{-- ---------- CV section (MOD 10). Separate form so its file upload and
         the delete action don't interfere with the text form above. ---------- --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">CV / Resume</h2>
        <p class="text-sm text-gray-500 mb-4">PDF only, max 5MB. Required before applying to jobs.</p>

        @if($user->hasDefaultResume())
            {{-- Current CV: confirmation + file details + Download / Delete. --}}
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                <div class="flex items-center min-w-0">
                    <svg class="h-9 w-9 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div class="ml-3 min-w-0">
                        {{-- MOD 10: green success confirmation with a check icon (no emoji). --}}
                        <p class="text-sm font-medium text-green-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            CV uploaded successfully
                        </p>
                        <p class="text-sm text-gray-700 truncate">{{ $user->resume_file_name ?? 'Resume.pdf' }}</p>
                        @if($user->resume_uploaded_at)
                            <p class="text-xs text-gray-400">Uploaded on {{ $user->resume_uploaded_at->format('M d, Y') }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Download (forced download via controller). --}}
                    <a href="{{ route('seeker.cv.download') }}"
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Download
                    </a>
                    {{-- Delete with confirmation (MOD 10). --}}
                    <form action="{{ route('seeker.cv.delete') }}" method="POST"
                          onsubmit="return confirm('Delete your CV? You will need to upload one again before applying.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Upload / Replace form (PDF only). When a CV already exists this acts
             as "Replace"; the controller deletes the old file first. We resubmit
             the existing text fields (hidden) so the shared update() validation —
             which requires name/email/phone — passes without blanking them. --}}
        <form action="{{ route('seeker.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            <input type="hidden" name="phone" value="{{ $user->phone }}">
            <input type="hidden" name="website" value="{{ $user->website }}">
            <input type="hidden" name="bio" value="{{ $user->bio }}">

            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $user->hasDefaultResume() ? 'Replace CV' : 'Upload CV' }}</label>
            {{-- MOD 10: accept PDF only. Server also validates mimes:pdf. --}}
            <input type="file" name="resume" accept="application/pdf" required
                class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
            @error('resume') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

            <button type="submit" class="mt-3 px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                {{ $user->hasDefaultResume() ? 'Replace CV' : 'Upload CV' }}
            </button>
        </form>
    </div>
</div>
@endsection
