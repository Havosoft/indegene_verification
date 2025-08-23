@extends('layouts.dashboard')
@section('header', 'User List')
@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">User List</h1>
                <p class="mt-2 text-sm text-gray-600">
                    View and manage users assigned to you
                    @if(auth()->user()->assigned_lga)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                            LGA: {{ auth()->user()->assigned_lga }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            Assigned Users: {{ auth()->user()->assignedUsers()->count() }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                            No LGA assigned
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-visible">
        <div style="overflow-x: visible;">
            <table class="w-full min-w-[1200px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Information</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LGA</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Services</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payment</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temporary ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permanent ID</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Is Verified</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <!-- User Information -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <!-- LGA -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($user->lga)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->lga }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Not specified</span>
                                @endif
                            </div>
                        </td>
                        <!-- Services Dropdown -->
                        <td class="px-6 py-4" style="overflow: visible;">
                            <div x-data="{ open: false, button: null, dropdown: null, top: 0, left: 0, width: 0, show() { this.open = true; this.$nextTick(() => { this.setPosition(); }); }, setPosition() { if (this.button && this.dropdown) { const rect = this.button.getBoundingClientRect(); this.top = rect.bottom + window.scrollY + 4; this.left = rect.left + window.scrollX; this.width = rect.width; this.dropdown.style.top = this.top + 'px'; this.dropdown.style.left = this.left + 'px'; this.dropdown.style.minWidth = this.width + 'px'; } }, close() { this.open = false; } }" class="inline-block">
                                <button x-ref="button" @click.prevent="show()" @keydown.escape.window="close()" class="px-3 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200 transition text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-green-400">
                                    View Services
                                </button>
                                <div x-ref="dropdown" x-show="open" @click.away="close()" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="fixed z-50 bg-white border border-gray-200 rounded-lg shadow-xl mt-1" style="display: none; min-width: 16rem; max-width: 90vw; max-height: 20rem; overflow-y: auto;" x-init="button = $refs.button; dropdown = $refs.dropdown;" @keydown.escape.window="close()">
                                    <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 rounded-t-lg">
                                        <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Service Status</h4>
                                    </div>
                                    <ul class="py-2">
                                        @php
                                            $allServices = \App\Models\Service::where('is_active', true)->get();
                                            $paidServiceNames = $user->payments->pluck('service_name')->toArray();
                                        @endphp
                                        @forelse($allServices as $service)
                                            @php
                                                $paymentObj = $user->payments->where('service_name', $service->name)->first();
                                            @endphp
                                            <li>
                                                @if($paymentObj)
                                                    <a href="{{ route('staff.user.payment.show', ['user' => $user->id, 'payment' => $paymentObj->id]) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors text-green-700 font-semibold">
                                                        <div class="flex-shrink-0">
                                                            <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center">
                                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                    <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm font-semibold text-green-700">{{ $service->name }}</div>
                                                            @if($paymentObj->approved === 'approved')
                                                                <div class="text-xs text-green-600 font-semibold">Approved</div>
                                                            @elseif($paymentObj->approved === 'not_approved')
                                                                <div class="text-xs text-red-600 font-semibold">Not Approved</div>
                                                            @else
                                                                <div class="text-xs text-yellow-600 font-semibold">Awaiting Review</div>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @else
                                                    <div class="flex items-center gap-3 px-4 py-2 text-gray-500">
                                                        <div class="w-5 h-5 border-2 border-gray-300 rounded-full"></div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-sm text-gray-600">{{ $service->name }}</div>
                                                            <div class="text-xs text-gray-400">Not subscribed</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="px-4 py-3 text-sm text-gray-500 text-center">
                                                No services available
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <!-- Status -->
                        <td class="px-6 py-4">
                            @php
                                $approvedCount = $user->payments->where('approved', 'approved')->count();
                                $notApprovedCount = $user->payments->where('approved', 'not_approved')->count();
                                $awaitingCount = $user->payments->where('approved', 'awaiting_review')->count();
                                $totalCount = $user->payments->count();
                            @endphp
                            @if($totalCount === 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">No Payments</span>
                            @elseif($approvedCount === $totalCount)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">All Approved</span>
                            @elseif($notApprovedCount === $totalCount)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">All Not Approved</span>
                            @elseif($awaitingCount === $totalCount)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Awaiting Review</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Mixed Status</span>
                            @endif
                        </td>
                        <!-- Last Payment -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($user->payments->count())
                                @php
                                    $latestPayment = $user->payments->sortByDesc('created_at')->first();
                                @endphp
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $latestPayment->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $latestPayment->created_at->format('g:i A') }}
                                    </div>
                                </div>
                        @else
                                <span class="text-sm text-gray-400">—</span>
                        @endif
                    </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->temporary_id ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->permanent_id ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                        @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Not Verified</span>
                                        @endif
                                    </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="relative inline-block text-left">
                                @if(!$user->is_verified)
                                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none" id="menu-button-{{ $user->id }}" aria-expanded="true" aria-haspopup="true">
                                        Actions
                                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.584l3.71-3.354a.75.75 0 111.02 1.1l-4 3.5a.75.75 0 01-1.02 0l-4-3.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="origin-top-right absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden" id="dropdown-{{ $user->id }}">
                                        <div class="py-1" role="none">
                                            <button type="button" onclick="verifyUser({{ $user->id }}, 'staff')" class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">Verify</button>
                                        </div>
                                    </div>
                                    <script>
                                        document.getElementById('menu-button-{{ $user->id }}').addEventListener('click', function(e) {
                                            e.preventDefault();
                                            var dropdown = document.getElementById('dropdown-{{ $user->id }}');
                                            dropdown.classList.toggle('hidden');
                                        });
                                        document.addEventListener('click', function(event) {
                                            var isClickInside = document.getElementById('menu-button-{{ $user->id }}').contains(event.target);
                                            if (!isClickInside) {
                                                document.getElementById('dropdown-{{ $user->id }}').classList.add('hidden');
                                            }
                                        });
                                    </script>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                            </div>
                    </td>
                </tr>
                @empty
                <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="space-y-3">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <div class="text-sm text-gray-500">No users found</div>
                            </div>
                        </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
// AJAX User Verification Function
function verifyUser(userId, role) {
    const button = event.target;
    const originalText = button.textContent;
    
    // Disable button and show loading state
    button.disabled = true;
    button.textContent = 'Verifying...';
    button.classList.add('opacity-50');
    
    // Determine the correct route based on role
    const route = role === 'admin' ? `/admin/users/${userId}/verify` : `/staff/users/${userId}/verify`;
    
    fetch(route, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success toast
            showToast(data.message, 'success');
            
            // Update the UI to reflect verification
            setTimeout(() => {
                location.reload(); // Reload to update the verification status
            }, 1500);
        } else {
            // Show error toast
            showToast(data.message || 'Verification failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred during verification', 'error');
    })
    .finally(() => {
        // Re-enable button
        button.disabled = false;
        button.textContent = originalText;
        button.classList.remove('opacity-50');
    });
}
</script>

@endsection