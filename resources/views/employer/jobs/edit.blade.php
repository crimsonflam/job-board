@extends('layouts.app')

@section('title', 'Edit Job — ' . $jobListing->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Edit Job</h1>
        <p class="mt-1 text-sm text-gray-500">Update the details of <strong>{{ $jobListing->title }}</strong>.</p>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employer.jobs.update', $jobListing) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

            {{-- Title --}}
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $jobListing->title) }}" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Category --}}
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category_id" id="category_id" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $jobListing->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" id="description" rows="6" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description', $jobListing->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Requirements --}}
            <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                <textarea name="requirements" id="requirements" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('requirements', $jobListing->requirements) }}</textarea>
                @error('requirements') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Benefits --}}
            <div>
                <label for="benefits" class="block text-sm font-medium text-gray-700 mb-1">Benefits</label>
                <textarea name="benefits" id="benefits" rows="4"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('benefits', $jobListing->benefits) }}</textarea>
                @error('benefits') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Job Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Job Type <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select type</option>
                        <option value="full-time" {{ old('type', $jobListing->type) == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="part-time" {{ old('type', $jobListing->type) == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="contract" {{ old('type', $jobListing->type) == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="freelance" {{ old('type', $jobListing->type) == 'freelance' ? 'selected' : '' }}>Freelance</option>
                        <option value="internship" {{ old('type', $jobListing->type) == 'internship' ? 'selected' : '' }}>Internship</option>
                    </select>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Experience Level --}}
                <div>
                    <label for="experience_level" class="block text-sm font-medium text-gray-700 mb-1">Experience Level <span class="text-red-500">*</span></label>
                    <select name="experience_level" id="experience_level" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select level</option>
                        <option value="entry" {{ old('experience_level', $jobListing->experience_level) == 'entry' ? 'selected' : '' }}>Entry Level</option>
                        <option value="mid" {{ old('experience_level', $jobListing->experience_level) == 'mid' ? 'selected' : '' }}>Mid Level</option>
                        <option value="senior" {{ old('experience_level', $jobListing->experience_level) == 'senior' ? 'selected' : '' }}>Senior Level</option>
                        <option value="lead" {{ old('experience_level', $jobListing->experience_level) == 'lead' ? 'selected' : '' }}>Lead</option>
                    </select>
                    @error('experience_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Location & Remote --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $jobListing->location) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-end">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="is_remote" value="1" {{ old('is_remote', $jobListing->is_remote) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">This is a remote position</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Compensation</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="salary_min" class="block text-sm font-medium text-gray-700 mb-1">Salary Min</label>
                    <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min', $jobListing->salary_min) }}" step="1" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('salary_min') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="salary_max" class="block text-sm font-medium text-gray-700 mb-1">Salary Max</label>
                    <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max', $jobListing->salary_max) }}" step="1" min="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('salary_max') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="salary_currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <input type="text" name="salary_currency" id="salary_currency" value="{{ old('salary_currency', $jobListing->salary_currency ?? 'USD') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @error('salary_currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Skills & Publishing</h2>

            {{-- Skills --}}
            <div>
                <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Skills</label>
                <input type="text" name="skills" id="skills" value="{{ old('skills', is_array($jobListing->skills) ? implode(', ', $jobListing->skills) : $jobListing->skills) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <p class="mt-1 text-xs text-gray-500">Separate skills with commas.</p>
                @error('skills') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="status" value="draft" {{ old('status', $jobListing->status) == 'draft' ? 'checked' : '' }}
                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="text-sm text-gray-700">Draft</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="status" value="published" {{ old('status', $jobListing->status) == 'published' ? 'checked' : '' }}
                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <span class="text-sm text-gray-700">Published</span>
                    </label>
                </div>
                @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employer.jobs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Update Job
            </button>
        </div>
    </form>
</div>
@endsection
