@extends('layouts.app')

@section('title', 'Applicants — ' . $jobListing->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('employer.jobs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Job Listings</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900">Applicants for: {{ $jobListing->title }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $applications->total() }} total application{{ $applications->total() !== 1 ? 's' : '' }}</p>
    </div>

    {{-- Applications --}}
    <div class="space-y-6">
        @forelse($applications as $application)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('employer.applications.update-status', $application) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                            {{-- Applicant Info --}}
                            <div class="lg:col-span-4 space-y-3">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $application->user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $application->user->email }}</p>
                                </div>

                                {{-- Cover Letter Preview --}}
                                @if($application->cover_letter)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Cover Letter</label>
                                        <p class="text-sm text-gray-700 line-clamp-4">{{ Str::limit($application->cover_letter, 250) }}</p>
                                    </div>
                                @endif

                                {{-- Resume Download --}}
                                @if($application->resume_path)
                                    <div>
                                        <a href="{{ Storage::url($application->resume_path) }}" target="_blank"
                                            class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Download Resume
                                        </a>
                                    </div>
                                @endif

                                <div class="text-xs text-gray-400">
                                    Applied {{ $application->created_at->format('M d, Y \a\t g:i A') }}
                                </div>
                            </div>

                            {{-- Status & Notes --}}
                            <div class="lg:col-span-5 space-y-4">
                                <div>
                                    <label for="status_{{ $application->id }}" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                                    <select name="status" id="status_{{ $application->id }}"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="reviewing" {{ $application->status == 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                                        <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                        <option value="interview" {{ $application->status == 'interview' ? 'selected' : '' }}>Interview</option>
                                        <option value="offered" {{ $application->status == 'offered' ? 'selected' : '' }}>Offered</option>
                                        <option value="hired" {{ $application->status == 'hired' ? 'selected' : '' }}>Hired</option>
                                        <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="employer_notes_{{ $application->id }}" class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Employer Notes</label>
                                    <textarea name="employer_notes" id="employer_notes_{{ $application->id }}" rows="3"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        placeholder="Internal notes about this applicant...">{{ $application->employer_notes }}</textarea>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="lg:col-span-3 flex lg:flex-col items-start lg:items-end justify-end space-x-3 lg:space-x-0 lg:space-y-3">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                    Update Status
                                </button>
                                <a href="{{ route('messages.start', $application->user) }}"
                                    class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Message
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="mt-4 text-sm font-medium text-gray-900">No applications yet</h3>
                <p class="mt-1 text-sm text-gray-500">Applications will appear here once candidates apply.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($applications->hasPages())
        <div class="mt-8">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
