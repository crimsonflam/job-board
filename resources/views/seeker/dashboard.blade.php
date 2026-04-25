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

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Alerts</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $activeAlertsCount }}</p>
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
                                <p class="text-sm text-gray-500">{{ $application->jobListing->company->name }}</p>
                            </div>
                            <div class="flex items-center space-x-4">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                    {{ ucfirst($application->status) }}
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
                    <a href="{{ route('seeker.alerts.index') }}" class="flex items-center px-4 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        Manage Alerts
                    </a>
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
