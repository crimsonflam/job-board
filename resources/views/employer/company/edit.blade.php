@extends('layouts.app')

@section('title', $company->hasCompanyProfile() ? 'Edit Company Profile' : 'Set Up Company Profile')

{{--
    ============================================================
    WHAT: Form for an employer to create / edit their company profile.
    WHY:  Company data lives on the employer's USER record. `$company` IS the
          authenticated user.
    MOD 18: NO company logo / branding / image upload — text fields only, so
          the form no longer needs enctype=multipart.
    MOD 2/18: Location is a Moroccan-cities dropdown (not free text).
    ============================================================
--}}

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $company->hasCompanyProfile() ? 'Edit Company Profile' : 'Set Up Your Company Profile' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ $company->hasCompanyProfile() ? 'Keep your company information up to date.' : 'Complete your company profile to start posting jobs.' }}
        </p>
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

    {{-- MOD 18: no file uploads → no enctype="multipart/form-data". --}}
    <form action="{{ route('employer.company.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

            {{-- Company Name (required) --}}
            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $company->company_name) }}" required maxlength="255"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Company Name">
                @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="company_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="company_description" id="company_description" rows="5"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Company Description">{{ old('company_description', $company->company_description) }}</textarea>
                @error('company_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Website (must be a valid URL, e.g. https://...) --}}
                <div>
                    <label for="company_website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="company_website" id="company_website" value="{{ old('company_website', $company->company_website) }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                        placeholder="Website URL">
                    @error('company_website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- MOD 2/18: Location — Moroccan cities dropdown (not free text). --}}
                <div>
                    <label for="company_location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="company_location" id="company_location"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="">Select a city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ old('company_location', $company->company_location) === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                    @error('company_location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Industry --}}
            <div>
                <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                <input type="text" name="industry" id="industry" value="{{ old('industry', $company->industry) }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Industry">
                @error('industry') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- MOD 18: "Branding" section (logo upload) removed entirely. --}}

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employer.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                {{ $company->hasCompanyProfile() ? 'Save Changes' : 'Create Company Profile' }}
            </button>
        </div>
    </form>
</div>
@endsection
