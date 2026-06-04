@extends('layouts.app')

@section('title', 'Edit Job — ' . $jobListing->title)

{{--
    ============================================================
    WHAT: Employer "Edit Job" form. Same fields/rules as create (MODs
          2/3/13/14/15) but pre-filled. Status is NOT edited here — it's
          managed by the Active/Inactive toggle on My Jobs (MOD 16).
    REMOTE↔LOCATION (M14): Alpine `jobType` initialises from the saved type
          so a remote job loads with its location field already disabled.
    ============================================================
--}}

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{ jobType: '{{ old('type', $jobListing->type) }}' }">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Job</h1>
        <p class="mt-1 text-sm text-gray-500">Update the details of <strong>{{ $jobListing->title }}</strong>.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-red-800 mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employer.jobs.update', $jobListing) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $jobListing->title) }}" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" id="category_id"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $jobListing->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="6" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ old('description', $jobListing->description) }}</textarea>
            </div>

            <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                <textarea name="requirements" id="requirements" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ old('requirements', $jobListing->requirements) }}</textarea>
            </div>

            <div>
                <label for="benefits" class="block text-sm font-medium text-gray-700 mb-1">Benefits</label>
                <textarea name="benefits" id="benefits" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">{{ old('benefits', $jobListing->benefits) }}</textarea>
            </div>
        </div>

        {{-- Job details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Job Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Job Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required x-model="jobType"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select type</option>
                        @foreach(\App\Models\JobListing::TYPE_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('type', $jobListing->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-1">Experience Level <span class="text-red-500">*</span></label>
                    <select name="experience_level" id="experience_level" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        @foreach(\App\Models\JobListing::EXPERIENCE_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('experience_level', $jobListing->experience_level) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="education_level" class="block text-sm font-medium text-gray-700 mb-1">Education Requirement <span class="text-red-500">*</span></label>
                    <select name="education_level" id="education_level" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        @foreach(\App\Models\JobListing::EDUCATION_LABELS as $value => $label)
                            <option value="{{ $value }}" {{ old('education_level', $jobListing->education_level) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" id="location"
                        :disabled="jobType === 'remote'"
                        :class="jobType === 'remote' ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select a city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('location', $jobListing->location) === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    <p x-show="jobType === 'remote'" x-cloak class="mt-1 text-xs text-gray-500">
                        This job is remote — location not required.
                    </p>
                </div>
            </div>
        </div>

        {{-- Compensation (MAD) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Compensation</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-1">Salary Min (MAD)</label>
                    <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min', $jobListing->salary_min) }}" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                </div>
                <div>
                    <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-1">Salary Max (MAD)</label>
                    <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max', $jobListing->salary_max) }}" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                </div>
            </div>
        </div>

        {{-- Skills --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Skills</label>
            <input type="text" name="skills" id="skills" value="{{ old('skills', is_array($jobListing->skills) ? implode(', ', $jobListing->skills) : '') }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                placeholder="Skills (comma separated)">
            <p class="mt-1 text-xs text-gray-500">Separate skills with commas.</p>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employer.jobs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">Save Changes</button>
        </div>
    </form>
</div>
@endsection
