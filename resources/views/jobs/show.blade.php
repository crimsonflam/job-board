@extends('layouts.app')

@section('title', $job->title . ' at ' . ($job->user->company_name ?? 'Unknown'))

@section('content')
{{-- `applyOpen` (Alpine) controls the apply modal at the bottom of this page.
     It opens automatically if the previous submit failed validation, so the
     seeker sees the error messages inside the modal instead of a closed dialog. --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ applyOpen: {{ $errors->any() ? 'true' : 'false' }} }">
    {{-- Back to list (preserves the seeker's filters via the browser's history). --}}
    <nav class="mb-6 text-sm text-gray-500 flex items-center">
        <a href="{{ route('jobs.index') }}" class="inline-flex items-center hover:text-primary-600">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Jobs
        </a>
        @if($job->category)
            <span class="mx-2">/</span>
            <a href="{{ route('jobs.index', ['category' => $job->category->id]) }}" class="hover:text-primary-600">{{ $job->category->name }}</a>
        @endif
    </nav>

    {{-- Company header. MOD 18: no company logo/branding — the avatar tile is
         just the company's initials in a neutral tile. --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-8">
        <div class="h-40 bg-primary-600"></div>
        <div class="p-6 -mt-10 relative">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-5">
                <div class="flex-shrink-0 w-20 h-20 bg-white rounded-xl border-4 border-white shadow-sm flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-400">{{ strtoupper(substr($job->user->company_name ?? 'J', 0, 2)) }}</span>
                </div>
                <div class="mt-4 sm:mt-0 sm:pb-1">
                    @if($job->user && $job->user->company_name)
                        <span class="text-sm text-primary-600 font-medium">{{ $job->user->company_name }}</span>
                    @endif
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $job->title }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Main content --}}
        <div class="flex-1 min-w-0 space-y-8">
            {{-- Meta info --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                {{-- MOD 5: deadline removed. MOD 17: views removed. Icons only. --}}
                <div class="flex flex-wrap gap-4 text-sm">
                    {{-- Job type (includes "Remote") --}}
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">
                        {{ $job->typeLabel() }}
                    </span>
                    {{-- Education requirement --}}
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                        {{ $job->educationLabel() }}
                    </span>
                    {{-- Experience level (MOD 13) --}}
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172"/></svg>
                        {{ $job->experienceLabel() }}
                    </span>
                    {{-- Location (remote jobs show "Remote") --}}
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        {{ $job->isRemote() ? 'Remote' : ($job->location ?? 'Not specified') }}
                    </span>
                    {{-- Salary (MAD) --}}
                    <span class="inline-flex items-center text-gray-600">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $job->salaryRange() }}
                    </span>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Description</h2>
                <div class="prose prose-sm max-w-none text-gray-600">
                    {!! $job->description !!}
                </div>
            </div>

            {{-- Requirements --}}
            @if($job->requirements)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Requirements</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! $job->requirements !!}
                    </div>
                </div>
            @endif

            {{-- Benefits --}}
            @if($job->benefits)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Benefits</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! $job->benefits !!}
                    </div>
                </div>
            @endif

            {{-- Skills --}}
            @if($job->skills && count($job->skills))
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Skills</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->skills as $skill)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-sm font-medium">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">
            {{-- ============================================================
                 Action panel.
                 - Guest:            "Sign in to Apply".
                 - Seeker (applied): status badge + applied date + employer
                                     response link (no apply button).
                 - Seeker (new):     "Apply for This Job" → opens the apply modal.
                 ============================================================ --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 space-y-3">
                @auth
                    @if(auth()->user()->isSeeker())
                        @php $myApplication = auth()->user()->applications()->where('job_listing_id', $job->id)->first(); @endphp

                        @if($myApplication)
                            {{-- Already applied: show the current response status. --}}
                            {{-- MOD 20: status uses text (no ✓/✗ emoji). --}}
                            @php
                                $badge = match($myApplication->status) {
                                    'accepted' => ['bg-green-100 text-green-800', 'Accepted'],
                                    'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
                                    default     => ['bg-gray-100 text-gray-700', 'Applied'],
                                };
                            @endphp
                            <div class="text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badge[0] }}">{{ $badge[1] }}</span>
                                <p class="mt-2 text-xs text-gray-500">Applied on {{ $myApplication->created_at->format('M d, Y') }}</p>
                            </div>
                            @if($myApplication->hasResponse())
                                <a href="{{ route('seeker.applications.index') }}" class="block w-full py-2 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm text-center">
                                    View Response
                                </a>
                            @else
                                <div class="w-full py-2 px-4 bg-gray-50 text-gray-500 font-medium rounded-lg text-xs text-center">
                                    No response from the employer yet
                                </div>
                            @endif
                        @else
                            {{-- Not applied yet: open the apply modal (defined below). --}}
                            <button type="button" @click="applyOpen = true"
                                class="block w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition text-sm text-center">
                                Apply for This Job
                            </button>
                        @endif

                        {{-- Save / unsave toggle. --}}
                        <form action="{{ route('seeker.saved-jobs.toggle', $job) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 px-4 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm flex items-center justify-center">
                                @if(auth()->user()->hasSavedJob($job->id))
                                    <svg class="w-4 h-4 mr-2 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
                                    Saved
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                    Save Job
                                @endif
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                        class="block w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition text-sm text-center">
                        Sign in to Apply
                    </a>
                @endauth
            </div>

            {{-- Company info card. MOD 18: text only — no logo image. --}}
            @if($job->user && $job->user->company_name)
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">About the Company</h3>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <span class="text-lg font-bold text-gray-400">{{ strtoupper(substr($job->user->company_name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $job->user->company_name }}</span>
                            @if($job->user->industry)
                                <p class="text-xs text-gray-500">{{ $job->user->industry }}</p>
                            @endif
                        </div>
                    </div>
                    @if($job->user->company_description)
                        <p class="text-sm text-gray-600 mb-3">{{ $job->user->company_description }}</p>
                    @endif
                    @if($job->user->company_location)
                        <p class="text-sm text-gray-500 mb-2">
                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            {{ $job->user->company_location }}
                        </p>
                    @endif
                    @if($job->user->company_website)
                        <a href="{{ $job->user->company_website }}" target="_blank" rel="noopener noreferrer"
                            class="block mt-4 text-center text-sm text-primary-600 font-medium hover:text-primary-700">
                            Visit Website
                        </a>
                    @endif
                </div>
            @endif

            {{-- MOD 6: "Related Jobs" section removed — the page shows only this job. --}}
        </aside>
    </div>

    {{-- ============================================================
         APPLICATION MODAL
         Only rendered for a logged-in seeker who hasn't applied yet.
         Two cases:
           1. No default CV on file  → block applying, prompt to go to profile.
           2. Has a default CV       → choose "Use my default CV" or
                                        "Upload a new CV for this job".

         // CRITICAL: When the employer reviews the applicant's CV, they see:
         //   - the default CV if the applicant chose "use default"
         //   - the custom CV if the applicant uploaded a new one for this job
         // The chosen CV is stored on the APPLICATION record (resume_path /
         // resume_file_name / cv_is_default), NOT on the user's profile, so
         // changing the default CV later never alters past applications.
         ============================================================ --}}
    @auth
        @if(auth()->user()->isSeeker() && !auth()->user()->hasApplied($job->id))
            @php $seeker = auth()->user(); @endphp

            {{-- Overlay --}}
            <div x-show="applyOpen" x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 @keydown.escape.window="applyOpen = false">

                {{-- Dialog --}}
                <div @click.away="applyOpen = false"
                     x-show="applyOpen"
                     x-transition
                     class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Submit Your Application</h3>
                        <button type="button" @click="applyOpen = false" class="text-gray-400 hover:text-primary-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    @if(!$seeker->hasDefaultResume())
                        {{-- ---------- Case 1: no default CV → must upload one first ---------- --}}
                        <div class="px-6 py-8 text-center">
                            <div class="mx-auto w-12 h-12 rounded-full bg-primary-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                            </div>
                            <h4 class="text-base font-semibold text-gray-900">Upload your resume first</h4>
                            <p class="mt-1 text-sm text-gray-500">
                                Please upload your resume in your profile before applying.
                            </p>
                            <a href="{{ route('seeker.profile.edit') }}"
                               class="inline-block mt-5 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                                Go to Profile
                            </a>
                        </div>
                    @else
                        {{-- ---------- Case 2: has a default CV → choose which CV to use ---------- --}}
                        <form action="{{ route('seeker.applications.store', $job) }}" method="POST" enctype="multipart/form-data"
                              x-data="{ choice: 'default' }">
                            @csrf

                            <div class="px-6 py-5 space-y-4">

                                {{-- Option 1: use default CV --}}
                                <label class="block border rounded-lg p-4 cursor-pointer transition"
                                       :class="choice === 'default' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-start">
                                        <input type="radio" name="cv_choice" value="default" x-model="choice" checked
                                               class="mt-1 w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-900">Use My Default CV</span>
                                            <span class="block text-sm text-gray-600 mt-0.5">{{ $seeker->resume_file_name ?? 'My Resume' }}</span>
                                            @if($seeker->resume_uploaded_at)
                                                <span class="block text-xs text-gray-400 mt-0.5">Uploaded on {{ $seeker->resume_uploaded_at->format('M d, Y') }}</span>
                                            @endif
                                            <span class="block text-xs text-gray-400 mt-1">This CV will be used for this application.</span>
                                        </div>
                                    </div>
                                </label>

                                {{-- Option 2: upload a new CV for this job only --}}
                                <label class="block border rounded-lg p-4 cursor-pointer transition"
                                       :class="choice === 'upload' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                                    <div class="flex items-start">
                                        <input type="radio" name="cv_choice" value="upload" x-model="choice"
                                               class="mt-1 w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                                        <div class="ml-3 flex-1">
                                            <span class="block text-sm font-medium text-gray-900">Upload New CV for This Role</span>
                                            <span class="block text-xs text-gray-400 mt-1">
                                                This CV is used only for this job. Your default CV stays unchanged.
                                            </span>
                                            <div x-show="choice === 'upload'" x-cloak class="mt-3">
                                                <input type="file" name="resume" accept=".pdf"
                                                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                                <p class="mt-1 text-xs text-gray-400">PDF only, max 5MB.</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                {{-- Validation errors (e.g. bad file) bounce back here. --}}
                                @if($errors->any())
                                    <div class="text-sm text-red-600">
                                        @foreach($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                                <button type="button" @click="applyOpen = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-5 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    @endauth
</div>
@endsection
