{{--
    ============================================================
    Shared filter sidebar for Browse Jobs AND Saved Jobs (MOD 7 requires the
    same filters on both). Pass $action = the form's target route.
    Filters (MOD 2/3/4/13):
      - keyword search (title / company / location)
      - job type (multi-select: Full-time, Part-time, Remote, Internship)
      - location (Moroccan cities dropdown — NOT free text)
      - education level (No Requirements / Bac / Bac+2 / Bac+3 / Bac+5)
      - work experience (multi-select: Entry/Mid/Senior/Lead)
      - salary range (MAD)
    MOD 4: NO posted-date filter. There is also no sort control anymore
           (deadline sort was removed with deadlines).
    ============================================================
--}}
@php
    $activeFilterCount = collect(['search', 'type', 'location', 'education', 'experience', 'salary_min', 'salary_max'])
        ->filter(fn ($k) => filled(request($k)))
        ->count();
@endphp

<form action="{{ $action }}" method="GET" class="bg-white border border-gray-200 rounded-xl p-5 space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">
            Filters
            @if($activeFilterCount > 0)
                <span class="ml-1 text-xs font-medium text-primary-600">({{ $activeFilterCount }} active)</span>
            @endif
        </h2>
        @if($activeFilterCount > 0)
            <a href="{{ $action }}" class="text-xs text-gray-500 hover:text-primary-600">Clear all</a>
        @endif
    </div>

    {{-- Search --}}
    <div>
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1.5">Search</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            </span>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                placeholder="Job title, company..."
                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>

    {{-- MOD 2: Job Type (multi-select). "Remote" is one of the four types. --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 mb-2">Job Type</h3>
        <div class="space-y-2">
            @foreach($jobTypes as $value => $label)
                <label class="flex items-center">
                    <input type="checkbox" name="type[]" value="{{ $value }}"
                        {{ in_array($value, (array) request('type', [])) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- MOD 2: Location — Moroccan cities dropdown (alphabetical), not free text. --}}
    <div>
        <label for="location" class="block text-sm font-medium text-gray-700 mb-1.5">Location</label>
        <select name="location" id="location"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">All Cities</option>
            @foreach($cities as $city)
                <option value="{{ $city }}" {{ request('location') === $city ? 'selected' : '' }}>{{ $city }}</option>
            @endforeach
        </select>
    </div>

    {{-- MOD 13: Work Experience (multi-select, OR logic). --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 mb-2">Work Experience</h3>
        <div class="space-y-2">
            @foreach($experienceLevels as $value => $label)
                <label class="flex items-center">
                    <input type="checkbox" name="experience[]" value="{{ $value }}"
                        {{ in_array($value, (array) request('experience', [])) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- MOD 3: Education Level (Moroccan scale). --}}
    <div>
        <label for="education" class="block text-sm font-medium text-gray-700 mb-1.5">Education Level</label>
        <select name="education" id="education"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Any</option>
            @foreach($educationLevels as $value => $label)
                <option value="{{ $value }}" {{ request('education') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- MOD 15: Salary Range in MAD. --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 mb-2">Salary Range (MAD)</h3>
        <div class="flex items-center gap-2">
            <input type="number" name="salary_min" value="{{ request('salary_min') }}" placeholder="Min" min="0"
                class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <span class="text-gray-400">–</span>
            <input type="number" name="salary_max" value="{{ request('salary_max') }}" placeholder="Max" min="0"
                class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>

    <div class="space-y-2 pt-2">
        <button type="submit" class="w-full py-2.5 px-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition text-sm">
            Apply Filters
        </button>
        @if($activeFilterCount > 0)
            <a href="{{ $action }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">Reset filters</a>
        @endif
    </div>
</form>
