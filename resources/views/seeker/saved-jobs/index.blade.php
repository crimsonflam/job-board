@extends('layouts.app')

@section('title', 'Saved Jobs')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Saved Jobs</h1>
        <p class="mt-1 text-gray-600">Jobs you've bookmarked for later.</p>
    </div>

    @if ($savedJobs->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($savedJobs as $savedJob)
                @php $job = $savedJob->jobListing; @endphp
                <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-gray-900 truncate">
                                    <a href="{{ route('jobs.show', $job) }}" class="hover:text-blue-600">{{ $job->title }}</a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $job->company->name }}</p>
                            </div>
                            <form action="{{ route('seeker.saved-jobs.toggle', $job) }}" method="POST" class="ml-2 flex-shrink-0">
                                @csrf
                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Remove from saved">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 space-y-2">
                            @if ($job->location)
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $job->location }}
                                </div>
                            @endif

                            @if ($job->job_type)
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ ucfirst(str_replace('_', ' ', $job->job_type)) }}
                                </div>
                            @endif

                            @if ($job->salary_min || $job->salary_max)
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-4 w-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @if ($job->salary_min && $job->salary_max)
                                        ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                                    @elseif ($job->salary_min)
                                        From ${{ number_format($job->salary_min) }}
                                    @else
                                        Up to ${{ number_format($job->salary_max) }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Saved {{ $savedJob->created_at->diffForHumans() }}</span>
                            <a href="{{ route('jobs.show', $job) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">View Job</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($savedJobs->hasPages())
            <div class="mt-8">
                {{ $savedJobs->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow px-6 py-16 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No saved jobs yet</h3>
            <p class="mt-2 text-sm text-gray-500">Browse available positions and save the ones that interest you.</p>
            <a href="{{ route('jobs.index') }}" class="mt-6 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Browse Jobs
            </a>
        </div>
    @endif
</div>
@endsection
