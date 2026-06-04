@extends('layouts.app')

@section('title', 'Application - ' . $application->jobListing->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('seeker.applications.index') }}" class="text-sm text-primary-600 hover:text-primary-700">&larr; Back to Applications</a>
    </div>

    {{-- Job Info & Status (3-state model: No Response / Accepted / Rejected). --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $application->jobListing->title }}</h1>
                <p class="mt-1 text-gray-600">{{ $application->jobListing->user->company_name }}</p>
                <p class="mt-1 text-sm text-gray-400">Applied on {{ $application->created_at->format('F d, Y') }}</p>
            </div>
            @php
                $statusMeta = match($application->status) {
                    'accepted' => ['bg-green-100 text-green-800', 'Accepted ✓'],
                    'rejected' => ['bg-red-100 text-red-800', 'Rejected ✗'],
                    default     => ['bg-gray-100 text-gray-700', 'No Response Yet'],
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusMeta[0] }}">
                {{ $statusMeta[1] }}
            </span>
        </div>

        {{-- Employer's response message, if they've replied. --}}
        @if($application->hasResponse() && $application->response_message)
            <div class="mt-4 bg-gray-50 border border-gray-100 rounded-lg p-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Employer's Response</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">@if($application->status === 'accepted')🎉 @endif{{ $application->response_message }}</p>
                @if($application->responded_at)
                    <p class="mt-2 text-xs text-gray-400">Replied on {{ $application->responded_at->format('M d, Y') }}</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Cover letters are not part of this application's workflow (CV-only). --}}

    {{-- CV submitted with THIS application (default or custom — frozen at apply time). --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">CV Submitted</h2>
        @if ($application->resume_path)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center min-w-0">
                    <svg class="h-8 w-8 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <div class="ml-3 min-w-0">
                        <span class="block text-sm text-gray-700 truncate">{{ $application->resume_file_name ?? 'Resume' }}</span>
                        <span class="block text-xs text-gray-400">{{ $application->cv_is_default ? 'Default CV' : 'Custom CV for this job' }}</span>
                    </div>
                </div>
                <a href="{{ Storage::url($application->resume_path) }}" target="_blank" class="flex-shrink-0 inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download
                </a>
            </div>
        @else
            <p class="text-gray-500 text-sm italic">No CV was attached to this application.</p>
        @endif
    </div>

    {{-- Actions. MOD 19: "Start Conversation" removed — no messaging feature.
         The employer's accept/reject message (shown above) is the only
         communication channel. --}}
    <div class="flex items-center space-x-4">
        <a href="{{ route('seeker.applications.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Back to Applications
        </a>
    </div>
</div>
@endsection
