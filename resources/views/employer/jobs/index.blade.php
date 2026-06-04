@extends('layouts.app')

@section('title', 'My Jobs')

{{--
    ============================================================
    WHAT: "My Jobs" — every job this employer has posted, as cards.
    WHY:  Each card surfaces the job's status (Active/Inactive), its
          applicant count (clickable → that job's applicants), and the
          three core actions: View Applicants, Edit, Delete.
    STATUS: We present a single Active/Inactive switch. Active = published
          (visible to seekers); Inactive = closed/draft (hidden). Toggling
          posts to employer.jobs.toggle-status.
    ============================================================
--}}

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">My Jobs</h1>
        <a href="{{ route('employer.jobs.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Post New Job
        </a>
    </div>

    @forelse($jobListings as $job)
        {{-- ============================================================
             MOD 7 BUG FIX: Job status UI showed the wrong state.
             ROOT CAUSE: this compared against the OLD status value 'published',
             but the status enum is now 'active'/'inactive' (changed in an
             earlier modification). So `$isActive` was ALWAYS false — every job
             rendered as "Inactive" / "Set Active" even when it was active in the
             database. The toggle worked server-side, but the badge + button
             label never reflected it.
             FIX: compare against the real value, 'active'. The status pill and
             toggle button below both derive from `$isActive`, so they now stay
             in sync with the database on every page load (after a toggle the
             form posts, the row is updated, and the reloaded page reflects it).
             ============================================================ --}}
        @php $isActive = $job->status === 'active'; @endphp
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                {{-- Left: title + meta --}}
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $job->title }}</h3>
                        {{-- Status pill --}}
                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $isActive ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    {{-- MOD 17: views count removed — only the posted date remains. --}}
                    <p class="mt-1 text-sm text-gray-500">
                        Posted {{ $job->created_at->format('M d, Y') }}
                    </p>
                    {{-- Applicant count — clickable, red number per spec. --}}
                    <a href="{{ route('employer.jobs.applications', $job) }}" class="mt-2 inline-flex items-center text-sm text-gray-600 hover:text-primary-600">
                        <span class="text-lg font-bold text-primary-600 mr-1.5">{{ $job->applications_count ?? 0 }}</span>
                        {{ Str::plural('applicant', $job->applications_count ?? 0) }}
                    </a>
                </div>

                {{-- Right: actions --}}
                <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                    {{-- Active/Inactive toggle --}}
                    <form action="{{ route('employer.jobs.toggle-status', $job) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                            class="px-3 py-1.5 text-sm font-medium rounded-lg border transition
                            {{ $isActive ? 'border-gray-300 text-gray-700 hover:bg-gray-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                            {{ $isActive ? 'Set Inactive' : 'Set Active' }}
                        </button>
                    </form>

                    <a href="{{ route('employer.jobs.applications', $job) }}"
                        class="px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        View Applicants
                    </a>

                    <a href="{{ route('employer.jobs.edit', $job) }}"
                        class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Edit
                    </a>

                    {{-- Delete with confirmation. bg-burgundy (dark red) per spec. --}}
                    <form action="{{ route('employer.jobs.destroy', $job) }}" method="POST"
                          onsubmit="return confirm('Delete “{{ $job->title }}”? This also removes its applications and cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1.5 text-sm font-medium text-white rounded-lg transition"
                            style="background-color:#8b0000" onmouseover="this.style.backgroundColor='#6b1b1b'" onmouseout="this.style.backgroundColor='#8b0000'">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        {{-- Empty state --}}
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-4 text-base font-semibold text-gray-900">No job listings yet</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by posting your first job.</p>
            <a href="{{ route('employer.jobs.create') }}" class="inline-block mt-4 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                Post New Job
            </a>
        </div>
    @endforelse

    @if($jobListings->hasPages())
        <div class="mt-6">
            {{ $jobListings->links() }}
        </div>
    @endif
</div>
@endsection
