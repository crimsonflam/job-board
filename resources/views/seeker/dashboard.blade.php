@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Welcome --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="mt-1 text-gray-600">Here's an overview of your job search activity.</p>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Applications</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $applicationsCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-pink-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Saved Jobs</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $savedJobsCount }}</p>
                </div>
            </div>
        </div>

        {{-- MOD 8: "Active Alerts" stat removed; replaced with "Awaiting Reply"
             (applications with no employer response yet) — more relevant. --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Awaiting Reply</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ auth()->user()->applications()->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Applications --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
                    <a href="{{ route('seeker.applications.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse ($recentApplications as $application)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <a href="{{ route('seeker.applications.show', $application) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                    {{ $application->jobListing->title }}
                                </a>
                                <p class="text-sm text-gray-500">{{ $application->jobListing->user->company_name }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                @php
                                    // 3-state model: pending (no response), accepted, rejected.
                                    $statusMeta = match($application->status) {
                                        'accepted' => ['bg-green-100 text-green-800', 'Accepted'],
                                        'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
                                        default     => ['bg-gray-100 text-gray-700', 'No Response'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusMeta[0] }}">
                                    {{ $statusMeta[1] }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $application->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500">
                            <p>You haven't applied to any jobs yet.</p>
                            <a href="{{ route('seeker.applications.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm">Browse Jobs</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Links</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('jobs.index') }}" class="flex items-center px-4 py-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Browse Jobs
                    </a>
                    <a href="{{ route('seeker.profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Edit Profile
                    </a>
                    {{-- MOD 8: "Manage Alerts" quick-link removed (alerts feature deleted). --}}
                    <a href="{{ route('seeker.saved-jobs.index') }}" class="flex items-center px-4 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Saved Jobs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
