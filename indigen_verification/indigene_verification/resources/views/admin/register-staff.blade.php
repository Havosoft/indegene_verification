@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold text-green-900 mb-6">Create Staff User</h1>
    <form method="POST" action="{{ route('register.staff') }}" class="bg-white rounded shadow p-8">
        @csrf
        @if ($errors->any())
            <div class="mb-4">
                <ul class="text-red-600 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2 mt-1" required value="{{ old('name') }}">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2 mt-1" required value="{{ old('email') }}">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Phone</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2 mt-1" required value="{{ old('phone') }}">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Gender</label>
            <select name="gender" class="w-full border rounded px-3 py-2 mt-1" required>
                <option value="">Select Gender</option>
                <option value="male" @if(old('gender')=='male') selected @endif>Male</option>
                <option value="female" @if(old('gender')=='female') selected @endif>Female</option>
                <option value="other" @if(old('gender')=='other') selected @endif>Other</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">State of Origin</label>
            <select name="state_of_origin" id="state-select" class="w-full border rounded px-3 py-2 mt-1" required>
                <option value="">Select State</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">LGA</label>
            <select name="lga" id="lga-select" class="w-full border rounded px-3 py-2 mt-1" required>
                <option value="">Select LGA</option>
            </select>
        </div>
        <div class="mb-6 text-green-700 text-sm font-semibold">
            Password will be generated automatically and sent to the staff's email address.
        </div>
        <input type="hidden" name="role" value="staff">
        <button type="submit" class="w-full bg-green-700 text-white py-2 rounded hover:bg-green-800 transition">Create Staff</button>
    </form>
</div>
<div x-data="{ show: false, message: '', type: '' }"
     x-init="
        @if(session('success'))
            show = true; message = '{{ session('success') }}'; type = 'success'; setTimeout(() => show = false, 4000);
        @elseif($errors->any())
            show = true; message = 'Failed to create staff. Please check the form.'; type = 'error'; setTimeout(() => show = false, 5000);
        @endif
     "
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed top-6 right-6 z-50">
    <div :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'" class="text-white px-6 py-3 rounded shadow-lg flex items-center gap-2">
        <svg x-show="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <svg x-show="type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span x-text="message"></span>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/json/nigeria-states-lgas.json')
        .then(res => res.json())
        .then(statesData => {
            const stateSelect = document.getElementById('state-select');
            const lgaSelect = document.getElementById('lga-select');
            // Populate states
            statesData.forEach(state => {
                const option = document.createElement('option');
                option.value = state.name;
                option.textContent = state.name;
                stateSelect.appendChild(option);
            });
            // Handle state change
            stateSelect.addEventListener('change', function() {
                lgaSelect.innerHTML = '<option value="">Select LGA</option>';
                const selectedState = statesData.find(s => s.name === stateSelect.value);
                if (selectedState) {
                    selectedState.lgas.forEach(lga => {
                        const option = document.createElement('option');
                        option.value = lga;
                        option.textContent = lga;
                        lgaSelect.appendChild(option);
                    });
                }
            });
            // If old values exist (for validation errors), set them
            @if(old('state_of_origin'))
                stateSelect.value = @json(old('state_of_origin'));
                stateSelect.dispatchEvent(new Event('change'));
            @endif
            @if(old('lga'))
                setTimeout(() => { lgaSelect.value = @json(old('lga')); }, 100);
            @endif
        });
});
</script>
@endsection 