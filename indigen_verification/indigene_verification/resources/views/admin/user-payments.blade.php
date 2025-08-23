@extends('layouts.dashboard')
@section('title', 'User Management')
@section('header', 'User Management')
@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">User Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage user accounts, roles, and service subscriptions</p>
            </div>
        </div>
    </div>
    
    <!-- Filters Section -->
    <div class="mb-6 bg-white shadow rounded-lg border border-gray-200">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('admin.user-payments') }}" class="space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <label for="search" class="sr-only">Search users</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" 
                                   value="{{ request('search') }}"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                   placeholder="Search by name, email, phone, NIN, or ID...">
                        </div>
                    </div>
                    
                    <!-- Role Filter -->
                                            <div class="flex items-center space-x-4">

                        
                        <!-- Search Button -->
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Search
                        </button>
                        
                        <!-- Clear Filters -->
                        @if(request('search') || (request('role') && request('role') !== 'user'))
                            <a href="{{ route('admin.user-payments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            
            <!-- Results Summary -->
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                </div>
                <div class="flex items-center space-x-4">
                    @if(request('search'))
                        <div class="text-sm text-gray-500">
                            Filtered results
                        </div>
                    @endif
                    <form action="{{ route('admin.reassign-users') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reassign Users
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-visible">
        <div style="overflow-x: visible;">
            <table class="w-full min-w-[1200px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User Information
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            LGA
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Assigned Staff
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Services
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Payment
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temporary ID</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permanent ID</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Is Verified</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                        <!-- User Information -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
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
                        
                        <!-- Assigned Staff -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($user->assignedStaff)
                                    <button 
                                        onclick="showStaffProfile({{ $user->assignedStaff->id }}, '{{ $user->assignedStaff->name }}', '{{ $user->assignedStaff->email }}', '{{ $user->assignedStaff->phone }}', '{{ $user->assignedStaff->assigned_lga }}', '{{ $user->assignedStaff->gender }}', '{{ $user->assignedStaff->date_of_birth }}', '{{ $user->assignedStaff->role }}')"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 cursor-pointer transition-colors duration-200"
                                    >
                                        {{ $user->assignedStaff->name }}
                                    </button>
                                @else
                                    <span class="text-gray-400">Not assigned</span>
                                @endif
                            </div>

                        
                        <!-- Services - Click-to-Toggle Dropdown with Fixed Positioning -->
                        <td class="px-6 py-4" style="overflow: visible;">
                            @if($user->role === 'user')
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
                                                        <a href="{{ route('admin.user.payment.show', ['user' => $user->id, 'payment' => $paymentObj->id]) }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors text-green-700 font-semibold">
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
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        
                        <!-- Status column update -->
                        <td class="px-6 py-4">
                            @if($user->payments->count())
                                <div class="space-y-1">
                                    @php
                                        $approvedCount = $user->payments->where('approved', 'approved')->count();
                                        $notApprovedCount = $user->payments->where('approved', 'not_approved')->count();
                                        $awaitingCount = $user->payments->where('approved', 'awaiting_review')->count();
                                        $totalCount = $user->payments->count();
                                    @endphp
                                    @if($approvedCount === $totalCount)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            All Approved
                                        </span>
                                    @elseif($awaitingCount === $totalCount)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Awaiting Review
                                        </span>
                                    @elseif($notApprovedCount === $totalCount)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            All Not Approved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Mixed Status
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400">—</span>
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
                            @if(!$user->is_verified)
                                <div class="relative inline-block text-left">
                                    <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none" id="menu-button-{{ $user->id }}" aria-expanded="true" aria-haspopup="true">
                                        Actions
                                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.584l3.71-3.354a.75.75 0 111.02 1.1l-4 3.5a.75.75 0 01-1.02 0l-4-3.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="origin-top-right absolute right-0 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden" id="dropdown-{{ $user->id }}">
                                        <div class="py-1" role="none">
                                            <button type="button" onclick="verifyUser({{ $user->id }}, 'admin')" class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">Verify</button>
                                        </div>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
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
    
    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-6 bg-white shadow rounded-lg border border-gray-200">
            <div class="px-6 py-4">
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Staff Profile Modal -->
<div id="staffProfileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Staff Profile</h3>
                <button onclick="closeStaffProfileModal()" class="text-gray-400 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Staff Avatar and Name -->
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-700 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-lg" id="staffInitials"></span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900" id="staffName"></h4>
                        <p class="text-sm text-gray-500" id="staffRole"></p>
                    </div>
                </div>
                
                <!-- Staff Details -->
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Email:</span>
                        <span class="text-sm text-gray-900" id="staffEmail"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Phone:</span>
                        <span class="text-sm text-gray-900" id="staffPhone"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Assigned LGA:</span>
                        <span class="text-sm text-gray-900" id="staffLga"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Gender:</span>
                        <span class="text-sm text-gray-900" id="staffGender"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Date of Birth:</span>
                        <span class="text-sm text-gray-900" id="staffDob"></span>
                    </div>
                </div>
                
                <!-- Assigned Users Count -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-purple-700">Assigned Users:</span>
                        <span class="text-lg font-bold text-purple-900" id="assignedUsersCount">0</span>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeStaffProfileModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-xl">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
            <span class="text-sm font-medium text-gray-900">Processing...</span>
        </div>
    </div>
</div>

<style>
/* Additional CSS to ensure dropdown visibility */
.table-container {
    overflow: visible !important;
}

/* Ensure table cells don't clip the dropdown */
td.relative {
    overflow: visible !important;
}

/* Smooth dropdown animation */
.group:hover .group-hover\:opacity-100 {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Prevent dropdown from being cut off at table boundaries */
.overflow-x-auto {
    overflow-y: visible;
}

/* Ensure proper stacking context */
.relative.group {
    z-index: 10;
}

.relative.group:hover {
    z-index: 20;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on Enter key in search field
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('form').submit();
            }
        });
    }
    

    
    // Clear search when clear button is clicked
    const clearButton = document.querySelector('a[href="{{ route("admin.user-payments") }}"]');
    if (clearButton) {
        clearButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    }
});

// Staff Profile Modal Functions - Define these globally
window.showStaffProfile = function(staffId, name, email, phone, lga, gender, dob, role) {
    // Set staff details in modal
    document.getElementById('staffName').textContent = name;
    document.getElementById('staffEmail').textContent = email;
    document.getElementById('staffPhone').textContent = phone;
    document.getElementById('staffLga').textContent = lga || 'Not assigned';
    document.getElementById('staffGender').textContent = gender ? gender.charAt(0).toUpperCase() + gender.slice(1) : 'Not specified';
    document.getElementById('staffDob').textContent = dob ? new Date(dob).toLocaleDateString() : 'Not specified';
    document.getElementById('staffRole').textContent = role ? role.charAt(0).toUpperCase() + role.slice(1) : 'Staff';
    
    // Set staff initials
    const initials = name.split(' ').map(n => n.charAt(0)).join('').toUpperCase();
    document.getElementById('staffInitials').textContent = initials;
    
    // Fetch assigned users count
    fetchAssignedUsersCount(staffId);
    
    // Show modal
    document.getElementById('staffProfileModal').classList.remove('hidden');
};

window.closeStaffProfileModal = function() {
    document.getElementById('staffProfileModal').classList.add('hidden');
};

function fetchAssignedUsersCount(staffId) {
    // Fetch real-time count via AJAX
    fetch(`/admin/staff/${staffId}/assigned-users-count`)
        .then(response => response.json())
        .then(data => {
            if (data.count !== undefined) {
                document.getElementById('assignedUsersCount').textContent = data.count;
            } else {
                document.getElementById('assignedUsersCount').textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error fetching assigned users count:', error);
            document.getElementById('assignedUsersCount').textContent = '0';
        });
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('staffProfileModal');
    if (e.target === modal) {
        closeStaffProfileModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStaffProfileModal();
    }
});

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