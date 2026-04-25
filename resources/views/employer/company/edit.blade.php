@extends('layouts.app')

@section('title', $company ? 'Edit Company Profile' : 'Set Up Company Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $company ? 'Edit Company Profile' : 'Set Up Your Company Profile' }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ $company ? 'Keep your company information up to date.' : 'Complete your company profile to start posting jobs.' }}
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

    <form action="{{ route('employer.company.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Basic Information</h2>

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $company->name ?? '') }}" required
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    placeholder="e.g. Acme Corporation">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="5"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                    placeholder="Tell candidates about your company, mission, and culture...">{{ old('description', $company->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Website --}}
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $company->website ?? '') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="https://example.com">
                    @error('website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $company->email ?? '') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="hr@example.com">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone ?? '') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="+1 (555) 123-4567">
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Location --}}
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $company->location ?? '') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="San Francisco, CA">
                    @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Industry --}}
                <div>
                    <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                    <input type="text" name="industry" id="industry" value="{{ old('industry', $company->industry ?? '') }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                        placeholder="e.g. Technology, Healthcare, Finance">
                    @error('industry') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Size --}}
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Company Size</label>
                    <select name="size" id="size"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Select size</option>
                        <option value="1-10" {{ old('size', $company->size ?? '') == '1-10' ? 'selected' : '' }}>1-10 employees</option>
                        <option value="11-50" {{ old('size', $company->size ?? '') == '11-50' ? 'selected' : '' }}>11-50 employees</option>
                        <option value="51-200" {{ old('size', $company->size ?? '') == '51-200' ? 'selected' : '' }}>51-200 employees</option>
                        <option value="201-500" {{ old('size', $company->size ?? '') == '201-500' ? 'selected' : '' }}>201-500 employees</option>
                        <option value="501-1000" {{ old('size', $company->size ?? '') == '501-1000' ? 'selected' : '' }}>501-1,000 employees</option>
                        <option value="1001-5000" {{ old('size', $company->size ?? '') == '1001-5000' ? 'selected' : '' }}>1,001-5,000 employees</option>
                        <option value="5001+" {{ old('size', $company->size ?? '') == '5001+' ? 'selected' : '' }}>5,001+ employees</option>
                    </select>
                    @error('size') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3">Branding</h2>

            {{-- Logo --}}
            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                @if($company && $company->logo_path)
                    <div class="mb-3 flex items-center space-x-4">
                        <img src="{{ Storage::url($company->logo_path) }}" alt="Current logo" class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                        <span class="text-sm text-gray-500">Current logo</span>
                    </div>
                @endif
                <input type="file" name="logo" id="logo" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Recommended: 400x400px, PNG or JPG.</p>
                @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Banner --}}
            <div>
                <label for="banner" class="block text-sm font-medium text-gray-700 mb-1">Company Banner</label>
                @if($company && $company->banner_path)
                    <div class="mb-3">
                        <img src="{{ Storage::url($company->banner_path) }}" alt="Current banner" class="w-full h-32 rounded-lg object-cover border border-gray-200">
                        <span class="text-sm text-gray-500 mt-1 inline-block">Current banner</span>
                    </div>
                @endif
                <input type="file" name="banner" id="banner" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">Recommended: 1200x300px, PNG or JPG.</p>
                @error('banner') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('employer.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                {{ $company ? 'Save Changes' : 'Create Company Profile' }}
            </button>
        </div>
    </form>
</div>
@endsection
