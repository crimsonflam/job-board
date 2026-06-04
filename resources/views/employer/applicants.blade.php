@extends('layouts.app')

@section('title', 'Applicants')

{{--
    ============================================================
    WHAT: Unified "Applicants" screen for an employer — every applicant
          across all their jobs, with search (by name), filter (by job +
          status), and sort. Each applicant row has two actions:
            • Open CV   → modal previewing/downloading the submitted CV
            • Reply     → modal to Accept/Reject with a message
    WHY:  One place to triage candidates. Both modals are SHARED (one of
          each on the page) and populated on demand from the clicked row —
          this keeps the DOM light even with many applicants, versus
          rendering a modal per row.
    HOW:  Alpine `x-data` at the page level holds the open flags and the
          currently-selected applicant's data. Each row's buttons call
          openCv(...) / openReply(...) with that applicant's values.
    ============================================================
--}}

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="applicantsScreen()">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Applicants</h1>
        <p class="mt-1 text-gray-500 text-sm">{{ $applications->total() }} {{ Str::plural('applicant', $applications->total()) }} across your jobs.</p>
    </div>

    {{-- Search + filter + sort toolbar --}}
    <form action="{{ route('employer.applicants.index') }}" method="GET"
          class="bg-white border border-gray-200 rounded-xl p-4 mb-6 flex flex-wrap items-end gap-3">
        {{-- Search by applicant name --}}
        <div class="flex-1 min-w-[180px]">
            <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Search applicants..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
        {{-- Filter by job --}}
        <div class="min-w-[160px]">
            <label for="job" class="block text-xs font-medium text-gray-500 mb-1">Job</label>
            <select name="job" id="job" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">All Jobs</option>
                @foreach($jobs as $j)
                    <option value="{{ $j->id }}" {{ (string) $jobFilter === (string) $j->id ? 'selected' : '' }}>{{ $j->title }}</option>
                @endforeach
            </select>
        </div>
        {{-- Filter by status --}}
        <div class="min-w-[150px]">
            <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="all"      {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                <option value="pending"  {{ $statusFilter === 'pending' ? 'selected' : '' }}>Awaiting Reply</option>
                <option value="accepted" {{ $statusFilter === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        {{-- Sort --}}
        <div class="min-w-[150px]">
            <label for="sort" class="block text-xs font-medium text-gray-500 mb-1">Sort by</label>
            <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Date (newest)</option>
                <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Date (oldest)</option>
                <option value="status" {{ $sort === 'status' ? 'selected' : '' }}>By Status</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
            Apply
        </button>
        @if($search || $jobFilter || $statusFilter !== 'all' || $sort !== 'newest')
            <a href="{{ route('employer.applicants.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Reset</a>
        @endif
    </form>

    {{-- Applicant cards --}}
    @forelse($applications as $application)
        @php
            // MOD 20: status badges use plain text (no ✓/✗ emoji).
            $statusMeta = match($application->status) {
                'accepted' => ['bg-green-100 text-green-800', 'Accepted'],
                'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
                default     => ['bg-gray-100 text-gray-700', 'Awaiting Reply'],
            };
            // MOD 11: CV download URL (forced download via controller route).
            $cvUrl = $application->resume_path ? route('employer.applications.cv', $application) : '';
        @endphp
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-900">{{ $application->user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $application->jobListing->title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Applied on {{ $application->created_at->format('M d, Y') }}</p>
                    <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusMeta[0] }}">
                        {{ $statusMeta[1] }}
                    </span>
                </div>

                <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                    {{-- MOD 11/20: "Open CV" opens a small info modal with a
                         Download button (no in-app PDF preview). SVG icon, no emoji. --}}
                    <button type="button"
                        @click="openCv({
                            name: @js($application->user->name),
                            file: @js($application->resume_file_name ?? 'Resume.pdf'),
                            url: @js($cvUrl),
                            isDefault: @js((bool) $application->cv_is_default),
                            applied: @js($application->created_at->format('M d, Y')),
                        })"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        Open CV
                    </button>

                    {{-- Reply --}}
                    <button type="button"
                        @click="openReply({
                            name: @js($application->user->name),
                            url: @js(route('employer.applications.update-status', $application)),
                        })"
                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition">
                        Reply
                    </button>
                </div>
            </div>

            {{-- Show the response already sent, if any. --}}
            @if($application->hasResponse() && $application->response_message)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Your response</p>
                    <p class="text-sm text-gray-700 line-clamp-2">{{ $application->response_message }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <h3 class="mt-4 text-base font-semibold text-gray-900">No applicants found</h3>
            <p class="mt-1 text-sm text-gray-500">Applicants will appear here once candidates apply, or try clearing your filters.</p>
        </div>
    @endforelse

    @if($applications->hasPages())
        <div class="mt-6">{{ $applications->withQueryString()->links() }}</div>
    @endif

    {{-- ============================================================
         OPEN CV MODAL (shared) — populated by openCv().
         MOD 11: NO in-app PDF preview (no <iframe>/PDF.js). The modal shows
         minimal file info and a "Download CV" button. The download route sets
         Content-Disposition: attachment so the browser saves the file and the
         employer opens it in their own PDF reader. Lighter + no viewer library.
         ============================================================ --}}
    <div x-show="cv.open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="cv.open = false">
        <div @click.away="cv.open = false" x-show="cv.open" x-transition
             class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Applicant CV</h3>
                <button type="button" @click="cv.open = false" class="text-gray-400 hover:text-primary-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5">
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                    <svg class="h-9 w-9 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate" x-text="cv.file"></p>
                        <p class="text-xs text-gray-500">
                            <span x-text="cv.isDefault ? 'Default CV' : 'Custom CV for this job'"></span>
                            · Applied <span x-text="cv.applied"></span>
                        </p>
                    </div>
                </div>
                <template x-if="!cv.url">
                    <p class="mt-4 text-sm text-gray-500 italic">No CV file is attached to this application.</p>
                </template>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button" @click="cv.open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
                <template x-if="cv.url">
                    {{-- Direct download — opens the secure download route. --}}
                    <a :href="cv.url"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Download CV
                    </a>
                </template>
            </div>
        </div>
    </div>

    {{-- ============================================================
         REPLY MODAL (shared) — Accept/Reject + message.
         Message is required, 10–500 chars (live counter); templates
         insert suggested text. The form action is set per-applicant.
         ============================================================ --}}
    <div x-show="reply.open" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         @keydown.escape.window="reply.open = false">
        <div @click.away="reply.open = false" x-show="reply.open" x-transition
             class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

            <form :action="reply.url" method="POST">
                @csrf
                @method('PUT')
                {{-- decision is bound to the chosen radio --}}
                <input type="hidden" name="status" :value="reply.decision">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Reply to <span x-text="reply.name"></span>
                    </h3>
                    <button type="button" @click="reply.open = false" class="text-gray-400 hover:text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    {{-- Decision: Accept or Reject --}}
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border rounded-lg p-3 cursor-pointer text-center text-sm font-medium transition"
                               :class="reply.decision === 'accepted' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                            <input type="radio" name="decision_ui" value="accepted" x-model="reply.decision" class="sr-only">
                            Accept
                        </label>
                        <label class="border rounded-lg p-3 cursor-pointer text-center text-sm font-medium transition"
                               :class="reply.decision === 'rejected' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                            <input type="radio" name="decision_ui" value="rejected" x-model="reply.decision" class="sr-only">
                            Reject
                        </label>
                    </div>

                    {{-- MOD 12: preset templates removed — the employer writes their own message. --}}

                    {{-- Message + character counter. --}}
                    <div>
                        <label for="reply_message" class="block text-sm font-medium text-gray-700 mb-1">Message to Applicant</label>
                        <textarea id="reply_message" name="response_message" rows="4"
                            x-model="reply.message"
                            :placeholder="reply.decision === 'accepted' ? 'Share details about interview, start date, next steps...' : 'Thank you for applying. We appreciate your interest...'"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                        <div class="mt-1 text-right text-xs text-gray-400">
                            {{-- MOD 12: space-insensitive count via _noSpace(). --}}
                            <span x-text="_noSpace(reply.message)"></span>/500 characters
                        </div>
                    </div>

                    {{-- Server-side validation errors (if the submit bounced back). --}}
                    @if($errors->any())
                        <div class="text-sm text-red-600">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                    <button type="button" @click="reply.open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    {{-- MOD 12: Submit disabled until the message has at least 1
                         non-space character. Accept = green bg; Reject = dark red. --}}
                    <button type="submit" :disabled="_noSpace(reply.message) < 1"
                        class="px-5 py-2 text-sm font-medium text-white rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="reply.decision === 'accepted' ? 'bg-green-600 hover:bg-green-700' : 'bg-primary-600 hover:bg-primary-700'"
                        x-text="reply.decision === 'accepted' ? 'Send Acceptance' : 'Send Rejection'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Alpine state for the Applicants screen: the two shared modals + the
    // currently-selected applicant's data. MOD 12: NO templates anymore.
    function applicantsScreen() {
        return {
            cv: { open: false, name: '', file: '', url: '', isDefault: true, applied: '' },
            reply: { open: false, name: '', url: '', decision: 'accepted', message: '' },

            // MOD 12: count characters EXCLUDING whitespace. "Hello World" => 10.
            // Used for both the live counter and the submit-enabled check.
            _noSpace(text) {
                return (text || '').replace(/\s/g, '').length;
            },

            openCv(data) {
                this.cv = { open: true, ...data };
            },
            openReply(data) {
                // Reset the form each time it's opened for a new applicant.
                this.reply = { open: true, name: data.name, url: data.url, decision: 'accepted', message: '' };
            },
        };
    }
</script>
@endpush
@endsection
