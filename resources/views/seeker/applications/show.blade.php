@extends('layouts.app')

@section('title', 'Application - ' . $application->jobListing->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('seeker.applications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Applications</a>
    </div>

    {{-- Job Info & Status --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $application->jobListing->title }}</h1>
                <p class="mt-1 text-gray-600">{{ $application->jobListing->company->name }}</p>
                <p class="mt-1 text-sm text-gray-400">Applied on {{ $application->created_at->format('F d, Y') }}</p>
            </div>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'reviewed' => 'bg-blue-100 text-blue-800',
                    'shortlisted' => 'bg-indigo-100 text-indigo-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'hired' => 'bg-green-100 text-green-800',
                ];
                $color = $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                {{ ucfirst($application->status) }}
            </span>
        </div>
    </div>

    {{-- Cover Letter --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Cover Letter</h2>
        @if ($application->cover_letter)
            <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line">{{ $application->cover_letter }}</div>
        @else
            <p class="text-gray-500 text-sm italic">No cover letter was submitted with this application.</p>
        @endif
    </div>

    {{-- Resume --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Resume</h2>
        @if ($application->resume_path)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="ml-3 text-sm text-gray-700">Resume</span>
                </div>
                <a href="{{ Storage::url($application->resume_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100">
                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download
                </a>
            </div>
        @else
            <p class="text-gray-500 text-sm italic">No resume was uploaded with this application.</p>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center space-x-4">
        <a href="{{ route('messages.start', $application) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            Start Conversation
        </a>
        <a href="{{ route('seeker.applications.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Back to Applications
        </a>
    </div>
</div>
@endsection
