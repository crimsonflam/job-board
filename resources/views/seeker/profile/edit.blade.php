@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
        <p class="mt-1 text-gray-600">Keep your profile up to date to attract the right opportunities.</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Success Message --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form action="{{ route('seeker.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Personal Information --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 @enderror" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $profile->phone ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('phone') border-red-300 @enderror" />
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $profile->location ?? '') }}" placeholder="e.g. New York, NY"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('location') border-red-300 @enderror" />
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">Website / Portfolio</label>
                    <input type="url" id="website" name="website" value="{{ old('website', $profile->website ?? '') }}" placeholder="https://"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('website') border-red-300 @enderror" />
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <textarea id="bio" name="bio" rows="4" placeholder="Tell employers about yourself..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('bio') border-red-300 @enderror">{{ old('bio', $profile->bio ?? '') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Professional Details --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Professional Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="skills" class="block text-sm font-medium text-gray-700">Skills</label>
                    <p class="mt-0.5 text-xs text-gray-500">Separate skills with commas (e.g. PHP, Laravel, JavaScript)</p>
                    <input type="text" id="skills" name="skills"
                        value="{{ old('skills', isset($profile) && $profile->skills ? (is_array($profile->skills) ? implode(', ', $profile->skills) : $profile->skills) : '') }}"
                        placeholder="PHP, Laravel, JavaScript, Vue.js"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('skills') border-red-300 @enderror" />
                    @error('skills')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expected_salary" class="block text-sm font-medium text-gray-700">Expected Salary (Annual)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" id="expected_salary" name="expected_salary" value="{{ old('expected_salary', $profile->expected_salary ?? '') }}"
                            placeholder="0"
                            class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('expected_salary') border-red-300 @enderror" />
                    </div>
                    @error('expected_salary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700">Availability</label>
                    <select id="availability" name="availability"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('availability') border-red-300 @enderror">
                        <option value="">Select availability</option>
                        <option value="available" {{ old('availability', $profile->availability ?? '') === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="open_to_offers" {{ old('availability', $profile->availability ?? '') === 'open_to_offers' ? 'selected' : '' }}>Open to Offers</option>
                        <option value="not_available" {{ old('availability', $profile->availability ?? '') === 'not_available' ? 'selected' : '' }}>Not Available</option>
                    </select>
                    @error('availability')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- File Uploads --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Uploads</h2>
            <div class="space-y-6">
                {{-- Avatar --}}
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                    <div class="mt-2 flex items-center space-x-4">
                        @if (isset($profile) && $profile->avatar)
                            <img src="{{ Storage::url($profile->avatar) }}" alt="Current avatar" class="h-16 w-16 rounded-full object-cover" />
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <input type="file" id="avatar" name="avatar" accept="image/*"
                            class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Resume --}}
                <div>
                    <label for="resume" class="block text-sm font-medium text-gray-700">Resume</label>
                    @if (isset($profile) && $profile->resume_path)
                        <p class="mt-1 text-sm text-gray-500">
                            Current file: <span class="font-medium text-gray-700">{{ basename($profile->resume_path) }}</span>
                        </p>
                    @endif
                    <div class="mt-2">
                        <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx"
                            class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500">PDF, DOC, or DOCX (max 5MB). Leave empty to keep current file.</p>
                    @error('resume')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('seeker.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Save Profile
            </button>
        </div>
    </form>
</div>
@endsection
