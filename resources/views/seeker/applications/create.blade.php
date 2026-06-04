@extends('layouts.app')

@section('title', 'Apply - ' . $jobListing->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Back</a>
    </div>

    {{-- Job Info Header --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h1 class="text-xl font-bold text-gray-900">Apply for this Position</h1>
        <div class="mt-3">
            <h2 class="text-lg font-semibold text-gray-800">{{ $jobListing->title }}</h2>
            <p class="text-gray-600">{{ $jobListing->user->company_name }}</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Application Form --}}
    <form action="{{ route('seeker.applications.store', $jobListing) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow">
        @csrf

        <div class="p-6 space-y-6">
            {{-- Cover letters are not part of this application's workflow: the
                 apply step is CV/resume only (see Phase 2 for the full apply modal). --}}

            {{-- Resume Upload --}}
            <div>
                <label for="resume" class="block text-sm font-medium text-gray-700">Resume</label>
                <p class="mt-1 text-xs text-gray-500">Upload your resume (PDF, DOC, or DOCX, max 5MB).</p>
                <div class="mt-2">
                    <input
                        type="file"
                        id="resume"
                        name="resume"
                        accept=".pdf,.doc,.docx"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('resume') border-red-300 @enderror"
                    />
                </div>
                @error('resume')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex items-center justify-end space-x-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Submit Application
            </button>
        </div>
    </form>
</div>
@endsection
