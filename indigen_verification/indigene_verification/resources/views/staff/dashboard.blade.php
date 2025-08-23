@extends('layouts.dashboard')
@section('header', 'Staff Dashboard')
@section('content')
@if (!auth()->user()->is_verified)
    <div x-data="{ 
        open: true, 
        ninValidating: false, 
        ninValid: false, 
        ninError: '',
        checkNin() {
            const nin = document.getElementById('nin').value;
            if (nin.length >= 8) {
                this.ninValidating = true;
                this.ninError = '';
                fetch(`/register/check-nin?nin=${nin}`)
                    .then(response => response.json())
                    .then(data => {
                        this.ninValidating = false;
                        if (data.exists) {
                            this.ninValid = false;
                            this.ninError = 'This NIN is already taken.';
                        } else {
                            this.ninValid = true;
                            this.ninError = '';
                        }
                    })
                    .catch(() => {
                        this.ninValidating = false;
                        this.ninError = 'Error checking NIN.';
                    });
            } else {
                this.ninValid = false;
                this.ninError = '';
            }
        }
    }" x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-black bg-opacity-50 absolute inset-0"></div>
        <div class="bg-white rounded-lg shadow-lg p-8 z-10 max-w-md w-full text-center">
            <h2 class="text-xl font-bold mb-4 text-red-700">Complete Your Registration</h2>
            <p class="mb-6 text-gray-700">Please fill in the missing information to proceed to your dashboard.</p>
            @if ($errors->any())
                <div class="mb-4 text-red-600 text-sm">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 text-left">
                @csrf
                @method('patch')
                <!-- Always include name and email as hidden fields -->
                <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                @if (!auth()->user()->date_of_birth)
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                @endif
                @if (!auth()->user()->address)
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <input id="address" name="address" type="text" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                @endif
                @if (!auth()->user()->place_of_birth)
                    <div>
                        <label for="place_of_birth" class="block text-sm font-medium text-gray-700">Place of Birth</label>
                        <input id="place_of_birth" name="place_of_birth" type="text" class="mt-1 block w-full border-gray-300 rounded-md" required>
                    </div>
                @endif
                @if (!auth()->user()->nin)
                    <div>
                        <label for="nin" class="block text-sm font-medium text-gray-700">NIN</label>
                        <input id="nin" name="nin" type="text" class="mt-1 block w-full border-gray-300 rounded-md" required 
                               @input="checkNin()" 
                               :class="{ 'border-red-500': ninError, 'border-green-500': ninValid && !ninError }">
                        <div x-show="ninValidating" class="mt-1 text-sm text-blue-600">Checking NIN...</div>
                        <div x-show="ninError" class="mt-1 text-sm text-red-600" x-text="ninError"></div>
                        <div x-show="ninValid && !ninError" class="mt-1 text-sm text-green-600">NIN is available</div>
                    </div>
                @endif
                <button type="submit" 
                        class="w-full px-6 py-2 rounded font-semibold transition-colors"
                        :class="ninValid || !document.getElementById('nin') ? 'bg-green-700 text-white hover:bg-green-800' : 'bg-gray-400 text-gray-200 cursor-not-allowed'"
                        :disabled="!ninValid && document.getElementById('nin')">
                    Save & Continue
                </button>
            </form>
        </div>
    </div>
@endif
<div class="max-w-3xl mx-auto py-10">
    <h1 class="text-2xl font-bold text-green-900 mb-6">Staff Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-green-50 p-6 rounded shadow flex flex-col items-center">
            <div class="text-3xl font-bold text-green-900">{{ $verifiedCount }}</div>
            <div class="text-gray-700 mt-2">Users Verified</div>
        </div>
        <div class="bg-blue-50 p-6 rounded shadow flex flex-col items-center">
            <div class="text-3xl font-bold text-blue-900">{{ $approvedCount }}</div>
            <div class="text-gray-700 mt-2">Certificates Approved</div>
        </div>
        <div class="bg-red-50 p-6 rounded shadow flex flex-col items-center">
            <div class="text-3xl font-bold text-red-900">{{ $disapprovedCount }}</div>
            <div class="text-gray-700 mt-2">Certificates Disapproved</div>
        </div>
    </div>
</div>
@endsection 