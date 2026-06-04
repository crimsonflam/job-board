@extends('layouts.app')

@section('title', 'Post New Job')

{{--
    ============================================================
    WHAT: Employer "Post New Job" form (auto-publishes on submit — MOD 16).
    MODS APPLIED:
      M2/M14 — Job type is one of four; "Remote" disables the location field.
      M14    — Location is a Moroccan-cities dropdown (not free text).
      M3     — Education uses the Bac scale.
      M13    — Experience level is a required dropdown.
      M15    — Salary in MAD, no currency selector.
      M16    — No draft/status selector — the button is "Publish Job".
    REMOTE↔LOCATION (M14): Alpine watches the job-type <select>. When "remote"
      is chosen, the location <select> is disabled, cleared, and a hint shows;
      switching back re-enables it. The backend enforces the same rule, so the
      UI and server can never disagree.
    ============================================================
--}}

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ jobType: '{{ old('type', '') }}' }">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Post New Job</h1>
        <p class="mt-1 text-sm text-gray-500">Fill in the details below. Your job is published immediately.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employer.jobs.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Basic info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Job Title">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" id="category_id"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="6" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Job Description">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                <textarea name="requirements" id="requirements" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Requirements">{{ old('requirements') }}</textarea>
            </div>

            <div>
                <label for="benefits" class="block text-sm font-medium text-gray-700 mb-1">Benefits</label>
                <textarea name="benefits" id="benefits" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Benefits">{{ old('benefits') }}</textarea>
            </div>
        </div>

        {{-- Job details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Job Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- M2/M14: Job Type — drives the remote/location logic. --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Job Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required x-model="jobType"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select type</option>
                        @foreach(\App\Models\JobListing::TYPE_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- M13: Experience level (required). --}}
                <div>
                    <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-1">Experience Level <span class="text-red-500">*</span></label>
                    <select name="experience_level" id="experience_level" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select level</option>
                        @foreach(\App\Models\JobListing::EXPERIENCE_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('experience_level') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- M3: Education requirement (Bac scale). --}}
                <div>
                    <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Requirement <span class="text-red-500">*</span></label>
                    <select name="education_level" id="education_level" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        @foreach(\App\Models\JobListing::EDUCATION_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('education_level', 'none') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- M14: Location — Moroccan cities dropdown. Disabled + cleared
                     when job type is "remote" (Alpine binds `disabled`). --}}
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" id="location"
                        :disabled="jobType === 'remote'"
                        :class="jobType === 'remote' ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select a city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('location') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    {{-- Hint shown only for remote jobs. --}}
                    <p x-show="jobType === 'remote'" x-cloak class="mt-1 text-xs text-gray-500">
                        This job is remote — location not required.
                    </p>
                </div>
            </div>
        </div>

        {{-- Compensation (MAD, no currency) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Compensation</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-1">Salary Min (MAD)</label>
                    <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min') }}" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        placeholder="Minimum salary (MAD)">
                </div>
                <div>
                    <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-1">Salary Max (MAD)</label>
                    <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max') }}" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        placeholder="Maximum salary (MAD)">
                </div>
            </div>
        </div>

        {{-- Skills --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Skills</label>
            <input type="text" name="skills" id="skills" value="{{ old('skills') }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                placeholder="Skills (comma separated)">
            <p class="mt-1 text-xs text-gray-500">Separate skills with commas.</p>
        </div>

        {{-- MOD 16: no status/draft selector — the button publishes directly. --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employer.jobs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Publish Job</button>
        </div>
    </form>
</div>
@endsection
