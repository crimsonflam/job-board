@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md" x-data="{ role: '{{ old('role', 'seeker') }}' }">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create your account</h1>
            <p class="mt-2 text-gray-600">Join thousands of professionals finding their next opportunity</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>
                        <span class="text-sm font-medium text-red-800">Please fix the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('name') border-red-500 @enderror"
                        placeholder="Full Name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('email') border-red-500 @enderror"
                        placeholder="Email Address">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('password') border-red-500 @enderror"
                        placeholder="Password">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Confirmation --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                        placeholder="Confirm Password">
                </div>

                {{-- Role selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">I want to</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer" @click="role = 'seeker'">
                            <input type="radio" name="role" value="seeker" x-bind:checked="role === 'seeker'" class="sr-only peer">
                            <div class="border-2 rounded-lg p-4 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50"
                                :class="role === 'seeker' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <svg class="w-8 h-8 mx-auto mb-2" :class="role === 'seeker' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <span class="text-sm font-medium" :class="role === 'seeker' ? 'text-indigo-700' : 'text-gray-700'">Job Seeker</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer" @click="role = 'employer'">
                            <input type="radio" name="role" value="employer" x-bind:checked="role === 'employer'" class="sr-only peer">
                            <div class="border-2 rounded-lg p-4 text-center transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50"
                                :class="role === 'employer' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <svg class="w-8 h-8 mx-auto mb-2" :class="role === 'employer' ? 'text-indigo-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                <span class="text-sm font-medium" :class="role === 'employer' ? 'text-indigo-700' : 'text-gray-700'">Employer</span>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Company Name (shown when employer) --}}
                <div x-show="role === 'employer'" x-transition x-cloak>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('company_name') border-red-500 @enderror"
                        placeholder="Company Name">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-2.5 px-4 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition text-sm">
                    Create Account
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:text-indigo-500">Sign in</a>
        </p>
    </div>
</div>

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush
@endsection
