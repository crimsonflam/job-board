@extends('layouts.app')

@section('title', 'My Applications')

{{--
    ============================================================
    WHAT: "My Applications" — a card list of every job the seeker applied
          to, showing the employer's response status and message.
    WHY:  This is where a seeker learns the outcome of each application.
          The status badge + the employer's written response are the two
          most important things, so they're given visual prominence.
    STATUS MODEL (seeker-facing):
          pending  → "No Response Yet"  (gray)
          accepted → "Accepted ✓"       (green) + employer message
          rejected → "Rejected ✗"       (red)   + employer message
    ============================================================
--}}

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Applications</h1>
        <p class="mt-1 text-gray-500 text-sm">Track the status of all your job applications.</p>
    </div>

    {{-- Filter + sort controls (only shown when there are applications). --}}
    @if($applications->total() > 0)
        <form action="{{ route('seeker.applications.index') }}" method="GET"
              class="flex flex-wrap items-center gap-3 mb-6">
            {{-- Filter by status --}}
            <div class="flex items-center gap-2">
                <label for="status" class="text-sm text-gray-600">Status:</label>
                <select name="status" id="status" onchange="this.form.submit()"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="all"      {{ $status === 'all' ? 'selected' : '' }}>All Applications</option>
                    <option value="pending"  {{ $status === 'pending' ? 'selected' : '' }}>No Response Yet</option>
                    <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            {{-- Sort --}}
            <div class="flex items-center gap-2">
                <label for="sort" class="text-sm text-gray-600">Sort by:</label>
                <select name="sort" id="sort" onchange="this.form.submit()"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="status" {{ $sort === 'status' ? 'selected' : '' }}>By Status</option>
                </select>
            </div>
        </form>
    @endif

    {{-- Application cards --}}
    @forelse($applications as $application)
        @php
            // Map status → badge style + label + (for accepted) a celebratory prefix.
            $statusMeta = match($application->status) {
                'accepted' => ['classes' => 'bg-green-100 text-green-800', 'label' => 'Accepted ✓'],
                'rejected' => ['classes' => 'bg-red-100 text-red-800',     'label' => 'Rejected ✗'],
                default     => ['classes' => 'bg-gray-100 text-gray-700',   'label' => 'No Response Yet'],
            };
        @endphp

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    {{-- Job title links to the job details page. --}}
                    <h3 class="text-base font-semibold text-gray-900 truncate">
                        <a href="{{ route('jobs.show', $application->jobListing->slug) }}" class="hover:text-primary-600">
                            {{ $application->jobListing->title }}
                        </a>
                    </h3>
                    <p class="text-sm font-medium text-gray-500">{{ $application->jobListing->user->company_name ?? 'Company' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Applied on {{ $application->created_at->format('M d, Y') }}</p>
                </div>
                {{-- Status badge --}}
                <span class="flex-shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusMeta['classes'] }}">
                    {{ $statusMeta['label'] }}
                </span>
            </div>

            {{-- Employer's response message (shown once they've replied). --}}
            @if($application->hasResponse() && $application->response_message)
                <div x-data="{ expanded: false }" class="mt-4">
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-3">
                        <p class="text-sm text-gray-700"
                           :class="expanded ? '' : 'line-clamp-2'">
                            @if($application->status === 'accepted')🎉 @endif{{ $application->response_message }}
                        </p>
                        {{-- "Show more" toggle for long messages. --}}
                        <button type="button" @click="expanded = !expanded"
                            class="mt-1 text-xs font-medium text-primary-600 hover:text-primary-700"
                            x-text="expanded ? 'Show less' : 'Show more'"></button>
                    </div>
                </div>
            @endif
        </div>
    @empty
        {{-- Empty state — distinguishes "no applications at all" from
             "no applications match this filter". --}}
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            @if($status !== 'all')
                <h3 class="mt-3 text-base font-semibold text-gray-900">No {{ $status }} applications</h3>
                <p class="mt-1 text-sm text-gray-500">Try a different status filter.</p>
                <a href="{{ route('seeker.applications.index') }}" class="inline-block mt-4 text-sm text-primary-600 hover:text-primary-700 font-medium">Show all applications</a>
            @else
                <h3 class="mt-3 text-base font-semibold text-gray-900">You haven't applied to any jobs yet.</h3>
                <a href="{{ route('jobs.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                    Browse Jobs
                </a>
            @endif
        </div>
    @endforelse

    @if($applications->hasPages())
        <div class="mt-6">
            {{ $applications->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
