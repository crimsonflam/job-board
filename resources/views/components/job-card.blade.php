{{--
    ============================================================
    WHAT: Compact job card for the Browse Jobs / Saved Jobs lists.
    WHY:  A minimal card — title, company, education, experience, location,
          type — lets seekers scan quickly and click through for detail.
    MOD 5:  Deadline removed (jobs stay active indefinitely).
    MOD 17: Views count removed.
    MOD 20: Emojis (📅/🎓/📍) replaced with inline SVG icons.
    MOD 7:  A heart toggle saves/unsaves the job. Because the heart is an
            interactive <form>, the card is a <div> (not a wrapping <a>) —
            nesting a form inside an anchor is invalid HTML. The title and a
            "View Details" link navigate to the job page instead.
    NOTE:   `$showHeart` lets callers hide the heart (e.g. for guests). It
            defaults to true for logged-in seekers.
    ============================================================
--}}
@php
    // Heart shows only for seekers. Saved state drives the filled/outline icon.
    $showHeart = ($showHeart ?? true) && auth()->check() && auth()->user()->isSeeker();
    $isSaved = $showHeart && auth()->user()->hasSavedJob($job->id);
@endphp

<div class="block bg-white border border-gray-200 rounded-xl p-6 transition duration-200 hover:shadow-md hover:border-primary-200 group">

    <div class="flex items-start justify-between gap-4">
        <a href="{{ route('jobs.show', $job->slug) }}" class="flex items-start space-x-4 min-w-0">
            {{-- Company logo (from the employer user). MOD 18: logos removed,
                 so this is always the generic building icon now. --}}
            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-primary-700 transition truncate">{{ $job->title }}</h3>
                <p class="text-sm font-medium text-gray-500 truncate">{{ $job->user->company_name ?? 'Company' }}</p>
            </div>
        </a>

        <div class="flex items-center gap-2 flex-shrink-0">
            {{-- MOD 1 (refinement): "Featured" badge removed — all jobs display
                 equally, no premium placement or highlighting. --}}

            {{-- MOD 7: Heart save/unsave toggle. Filled = saved. Submits a POST
                 to the toggle route; the page reloads with a flash message. --}}
            @if($showHeart)
                <form action="{{ route('seeker.saved-jobs.toggle', $job) }}" method="POST">
                    @csrf
                    <button type="submit" title="{{ $isSaved ? 'Remove from saved' : 'Save job' }}"
                        class="p-1.5 rounded-full hover:bg-primary-50 transition">
                        @if($isSaved)
                            {{-- Filled heart (saved) --}}
                            <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                        @else
                            {{-- Outline heart (not saved) --}}
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        @endif
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Meta line: education, experience, location — each with an SVG icon. --}}
    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-500">
        {{-- Education requirement (graduation-cap icon) --}}
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
            {{ $job->educationLabel() }}
        </span>

        {{-- MOD 13: Experience level (badge icon) --}}
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/></svg>
            {{ $job->experienceLabel() }}
        </span>

        {{-- Location (pin icon). MOD 2: remote jobs show "Remote", no city. --}}
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
            {{ $job->isRemote() ? 'Remote' : ($job->location ?? 'Not specified') }}
        </span>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <span class="inline-flex items-center px-2 py-0.5 rounded bg-primary-50 text-primary-700 text-xs font-medium">
            {{ $job->typeLabel() }}
        </span>
        <a href="{{ route('jobs.show', $job->slug) }}"
           class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700">
            View Details
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
