<div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition group">
    <div class="flex items-start justify-between">
        <div class="flex items-start space-x-4">
            {{-- Company logo --}}
            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                @if($job->company && $job->company->logo)
                    <img src="{{ asset('storage/' . $job->company->logo) }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                @endif
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition">
                    <a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a>
                </h3>
                @if($job->company)
                    <a href="{{ route('companies.show', $job->company->slug) }}" class="text-sm text-gray-500 hover:text-indigo-600">
                        {{ $job->company->name }}
                    </a>
                @endif
            </div>
        </div>
        @if($job->is_featured)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Featured</span>
        @endif
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-gray-500">
        {{-- Location --}}
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
            {{ $job->location ?? 'Not specified' }}
        </span>

        {{-- Job Type --}}
        <span class="inline-flex items-center px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-xs font-medium">{{ ucfirst(str_replace('-', ' ', $job->type)) }}</span>

        {{-- Remote badge --}}
        @if($job->is_remote)
            <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-50 text-green-700 text-xs font-medium">Remote</span>
        @endif

        {{-- Salary --}}
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $job->salaryRange() }}
        </span>
    </div>

    <div class="mt-4 flex items-center justify-between">
        {{-- Skills --}}
        <div class="flex flex-wrap gap-1">
            @if($job->skills)
                @foreach(array_slice($job->skills, 0, 3) as $skill)
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-xs">{{ $skill }}</span>
                @endforeach
                @if(count($job->skills) > 3)
                    <span class="text-xs text-gray-400">+{{ count($job->skills) - 3 }}</span>
                @endif
            @endif
        </div>
        {{-- Date --}}
        <span class="text-xs text-gray-400">{{ $job->published_at?->diffForHumans() ?? 'Recently' }}</span>
    </div>
</div>
