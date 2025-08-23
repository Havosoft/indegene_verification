@extends('layouts.dashboard')
@section('header', 'Dashboard')
@section('content')
<div x-data="{ 
    ...paystackModal(),
    verificationModal: {{ (!auth()->user()->is_verified || auth()->user()->nin_verification_status === 'failed' || session('nin_verification_failed')) ? 'true' : 'false' }} === 'true',
    closeVerificationModal() {
        this.verificationModal = false;
    }
}" class="dashboard-content h-full">
    <div class="max-w-7xl mx-auto p-6 h-full">
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-8">
            <!-- Left Column - Existing Content -->
            <div class="xl:col-span-3">
        <div class="chart-card rounded-xl shadow-lg p-8 mb-8">
            @if(isset($stats))
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-green-50 p-4 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="text-2xl font-bold text-green-900">{{ $stats['total_users'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Users</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="text-2xl font-bold text-blue-900">{{ $stats['total_staff'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Staff</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="text-2xl font-bold text-yellow-900">{{ $stats['total_admins'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Admins</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="text-2xl font-bold text-purple-900">{{ $stats['total_payments'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Payments</div>
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg shadow-sm flex flex-col items-center">
                    <div class="text-2xl font-bold text-indigo-900">{{ $stats['total_services'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">Services</div>
                </div>
            </div>
            
            <!-- Revenue Card - Larger and below other cards -->
            <div class="bg-gradient-to-r from-pink-50 to-pink-100 p-6 rounded-lg shadow-sm mb-8 border border-pink-200">
                <div class="text-center">
                    <div class="text-4xl font-bold text-pink-900 mb-2">₦{{ number_format($stats['total_revenue']) }}</div>
                    <div class="text-lg text-pink-700 font-semibold">Total Revenue</div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded shadow">
                    <div class="font-bold text-green-900 mb-2">Recent Payments</div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($stats['recent_payments'] as $payment)
                            <li class="py-2 flex justify-between text-sm">
                                <span>{{ $payment->user->name ?? 'User' }} paid for <b>{{ $payment->service_name }}</b></span>
                                <span class="text-gray-500">₦{{ number_format($payment->price) }}<br><span class="text-xs">{{ $payment->created_at->format('Y-m-d H:i') }}</span></span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-400">No recent payments.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <div class="font-bold text-green-900 mb-2">New Users</div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($stats['new_users'] as $newUser)
                            <li class="py-2 flex justify-between text-sm">
                                <span>{{ $newUser->name }}</span>
                                <span class="text-gray-500">{{ $newUser->created_at->format('Y-m-d H:i') }}</span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-400">No new users.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            @endif
            <div class="text-2xl font-bold text-green-900 mb-2">Welcome, {{ auth()->user()->name }}!</div>
            <div class="text-gray-700 mb-4">You are logged in to the Taraba State Indigene Verification Portal.</div>
            
            <!-- Verification Status Badge -->
            @if (auth()->user()->is_verified)
                <div class="mb-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Verified Account
                </div>
            @else
                <div class="mb-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pending Verification
                </div>
            @endif

            <!-- NIN Verification Status Badge -->
            @if (auth()->user()->nin_verification_status === 'verified')
                <div class="mb-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    NIN Verified (Taraba Indigene)
                </div>
            @elseif (auth()->user()->nin_verification_status === 'failed')
                <div class="mb-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    NIN Verification Failed
                </div>
                
                <!-- Detailed Failure Reason -->
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="text-red-800">
                            <h4 class="font-semibold mb-2">Verification Failed</h4>
                            @if (auth()->user()->nin_verification_failure_reason)
                                <div class="text-sm leading-relaxed">
                                    {!! nl2br(e(auth()->user()->nin_verification_failure_reason)) !!}
                                </div>
                            @else
                                <p class="text-sm leading-relaxed">
                                    Your NIN verification could not be completed. This may be because:
                                </p>
                                <ul class="text-sm mt-2 space-y-1 list-disc list-inside">
                                    <li>Your NIN is not registered in Taraba State</li>
                                    <li>Your state of origin is not Taraba State</li>
                                    <li>There was a technical issue with the verification service</li>
                                </ul>
                                <p class="text-sm mt-2">
                                    <strong>Note:</strong> Only Taraba State indigenes are eligible for verification.
                                </p>
                            @endif
                            
                            <!-- Re-verify Button -->
                            <div class="mt-3">
                                <form action="{{ route('nin.reverify') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Try Again
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (auth()->user()->nin_verification_status === 'pending')
                <div class="mb-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    NIN Verification Pending
                </div>
            @endif
            @if (auth()->user()->is_verified && auth()->user()->permanent_id)
                <div class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-900 rounded-lg flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <div>
                        <div class="font-semibold">Permanent ID:</div>
                        <div class="text-lg font-mono tracking-wider">{{ auth()->user()->permanent_id }}</div>
                    </div>
                </div>
            @elseif (auth()->user()->temporary_id)
                <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-900 rounded-lg flex items-center gap-3">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <div>
                        <div class="font-semibold">Temporary ID:</div>
                        <div class="text-lg font-mono tracking-wider">{{ auth()->user()->temporary_id }}</div>
                    </div>
                </div>
            @endif
        </div>
        @if(auth()->user()->role !== 'admin')
        <div class="chart-card rounded-xl shadow-lg p-8">
            <div class="text-xl font-bold text-green-900 mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Available Services
            </div>
            <table class="w-full text-left border-collapse mb-2">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                        <th class="py-3 px-4">Service</th>
                        <th class="py-3 px-4 text-right">Price (₦)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $user = auth()->user();
                        $paidServices = $user->payments()->pluck('service_name')->toArray();
                    @endphp
                    @foreach ($services ?? [] as $service)
                        <tr class="border-b last:border-b-0">
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $service->name }}</td>
                            <td class="py-3 px-4 text-right text-gray-700">₦{{ number_format($service->price) }}</td>
                            <td class="py-3 px-4 text-center">
                                @if (in_array($service->name, $paidServices))
                                    <span class="px-4 py-2 bg-green-200 text-green-900 font-semibold rounded shadow">Paid</span>
                                @else
                                    <button type="button" @click="openPaystackModal('{{ $service->name }}', {{ $service->price }})" class="px-4 py-2 bg-red-200 text-red-900 font-semibold rounded shadow hover:bg-red-300 hover:text-red-800 transition inline-block">Pay Now</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-xs text-gray-500 mt-2">Download will be enabled after payment and verification.</div>
        </div>
        @endif
            </div>
            
            <!-- Right Column - Charts (Admin Only) -->
            @if(auth()->user()->role === 'admin' && isset($chartData))
            <div class="xl:col-span-2 charts-section">
                <!-- Revenue Summary Cards -->
                <div class="chart-card rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue Summary</h3>
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                            <div class="text-2xl font-bold text-green-900">₦{{ number_format($chartData['last_30_days_revenue']) }}</div>
                            <div class="text-sm text-green-700">Last 30 Days</div>
                        </div>
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
                            <div class="text-2xl font-bold text-blue-900">₦{{ number_format($chartData['last_7_days_revenue']) }}</div>
                            <div class="text-sm text-blue-700">Last 7 Days</div>
                        </div>
                    </div>
                </div>
                
                <!-- Revenue Chart -->
                <div class="chart-card rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue Over Time</h3>
                    <div class="mb-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="yearFilter" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                                <select id="yearFilter" class="w-full p-2 border border-gray-300 rounded-md text-sm bg-white">
                                    @php
                                        $availableYears = \App\Models\Payment::selectRaw('YEAR(created_at) as year')
                                            ->distinct()
                                            ->orderByDesc('year')
                                            ->pluck('year');
                                        $currentYear = date('Y');
                                    @endphp
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                    @if(!$availableYears->contains($currentYear))
                                        <option value="{{ $currentYear }}" selected>{{ $currentYear }}</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label for="serviceFilter" class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                                <select id="serviceFilter" class="w-full p-2 border border-gray-300 rounded-md text-sm bg-white">
                                    <option value="">All Services</option>
                                    @foreach($chartData['available_services'] as $service)
                                        <option value="{{ $service }}">{{ $service }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container relative">
                        <div id="chartLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                            <div class="text-gray-600">Loading...</div>
                        </div>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                
                <!-- User Registrations Chart -->
                <div class="chart-card rounded-xl shadow-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">User Registrations</h3>
                    <div class="mb-4">
                        <div>
                            <label for="userYearFilter" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="userYearFilter" class="w-full p-2 border border-gray-300 rounded-md text-sm bg-white">
                                @php
                                    $userAvailableYears = \App\Models\User::selectRaw('YEAR(created_at) as year')
                                        ->distinct()
                                        ->orderByDesc('year')
                                        ->pluck('year');
                                    $currentYear = date('Y');
                                @endphp
                                @foreach($userAvailableYears as $year)
                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                                @if(!$userAvailableYears->contains($currentYear))
                                    <option value="{{ $currentYear }}" selected>{{ $currentYear }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="chart-container relative">
                        <div id="userChartLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                            <div class="text-gray-600">Loading...</div>
                        </div>
                        <canvas id="userRegistrationsChart"></canvas>
                    </div>
                </div>
                
                <!-- Revenue by Service -->
                <div class="chart-card rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue by Service</h3>
                    <div class="chart-container doughnut">
                        <canvas id="revenueByServiceChart"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Payment Modal for users who need to pay after registration -->
    @if(auth()->user()->role !== 'admin' && auth()->user()->payments()->count() === 0 && \App\Models\PaymentSetting::isPaymentBeforeDataSave() === false)
    <div x-data="dashboardPaymentModal()" x-init="checkPaymentStatus()">
        <div x-show="showPaymentModal" style="background: rgba(0,0,0,0.6)" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
            <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-lg relative mx-4">
                <button @click="closePaymentModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Complete Your Registration</h2>
                    <p class="text-gray-600">Please select and pay for the required services to complete your registration.</p>
                </div>
                
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Required Services:</h3>
                    <div class="space-y-3">
                        @foreach($requiredServices ?? [] as $service)
                        <div class="flex items-center justify-between p-3 border rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900">{{ $service->name }}</div>
                                <div class="text-sm text-gray-600">₦{{ number_format($service->price) }}</div>
                            </div>
                            <label class="flex items-center">
                                <input type="checkbox" value="{{ $service->name }}" class="service-checkbox mr-2" 
                                       data-price="{{ $service->price }}" data-name="{{ $service->name }}">
                                <span class="text-sm text-gray-700">Select</span>
                            </label>
                        </div>
                        @endforeach
                        @if(empty($requiredServices) || count($requiredServices) === 0)
                        <div class="text-center p-4 text-gray-500">
                            No required services available. Please contact support.
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-800">Total Amount:</span>
                        <span class="text-xl font-bold text-green-600" x-text="'₦' + totalAmount.toLocaleString()">₦0</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button @click="closePaymentModal" class="flex-1 py-3 px-4 border border-gray-300 text-gray-700 font-semibold rounded hover:bg-gray-50 transition">
                        Pay Later
                    </button>
                    <button @click="proceedToPayment" :disabled="selectedServices.length === 0" 
                            class="flex-1 py-3 px-4 bg-green-700 text-white font-semibold rounded hover:bg-green-800 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Pay Now
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Verification Modal -->
    <div x-show="verificationModal" style="background: rgba(0,0,0,0.6)" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative mx-4">
            <button @click="closeVerificationModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Verification Required</h3>
                
                @if(!auth()->user()->is_verified)
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 text-sm">
                        <strong>General Verification:</strong> Your account is pending verification. Please wait for admin approval.
                    </div>
                @endif
                
                @if(auth()->user()->nin_verification_status === 'failed' || session('nin_verification_failed'))
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-left">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="text-red-800">
                                <h4 class="font-semibold mb-2">NIN Verification Failed</h4>
                                @if(session('failure_reason'))
                                    <div class="text-sm leading-relaxed">
                                        {!! nl2br(e(session('failure_reason'))) !!}
                                    </div>
                                @elseif(auth()->user()->nin_verification_failure_reason)
                                    <div class="text-sm leading-relaxed">
                                        {!! nl2br(e(auth()->user()->nin_verification_failure_reason)) !!}
                                    </div>
                                @else
                                    <p class="text-sm leading-relaxed mb-2">
                                        Your NIN verification could not be completed. This may be because:
                                    </p>
                                    <ul class="text-sm space-y-1 list-disc list-inside mb-2">
                                        <li>Your NIN is not registered in Taraba State</li>
                                        <li>Your state of origin is not Taraba State</li>
                                        <li>There was a technical issue with the verification service</li>
                                    </ul>
                                    <p class="text-sm">
                                        <strong>Note:</strong> Only Taraba State indigenes are eligible for verification.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                
                <p class="text-sm text-gray-600 mb-6">
                    Some features may be limited until your account is fully verified. Please contact support if you need assistance.
                </p>
                
                <div class="space-y-3">
                    @if(auth()->user()->nin_verification_status === 'failed')
                        <!-- Reverify Button -->
                        <form action="{{ route('nin.reverify') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full py-2 px-4 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Re-verify NIN
                            </button>
                        </form>
                    @endif
                    
                    <button @click="closeVerificationModal" class="w-full py-2 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        I Understand
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div x-show="show" style="background: rgba(0,0,0,0.4)" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative">
            <button @click="close" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
            <h2 class="text-xl font-bold mb-4 text-center">Pay for Service</h2>
            <div class="mb-6">
                <div class="font-semibold text-gray-700 mb-2">Service:</div>
                <div class="text-lg font-bold text-green-900" x-text="serviceName"></div>
                <div class="mt-2 font-semibold text-gray-700">Amount:</div>
                <div class="text-xl font-bold text-green-900" x-text="price.toLocaleString()"></div>
            </div>
            <button type="button" class="w-full py-3 bg-green-700 text-white font-semibold rounded hover:bg-green-800 transition" @click="pay">Proceed to Payment</button>
        </div>
    </div>
</div>
<script>
function paystackModal() {
    return {
        show: false,
        serviceName: '',
        price: 0,
        openPaystackModal(name, price) {
            this.serviceName = name;
            this.price = price;
            this.show = true;
        },
        close() { this.show = false; },
        pay() {
            const user = {
                email: '{{ auth()->user()->email ?? '' }}',
                name: '{{ auth()->user()->name ?? '' }}',
                phone: '{{ auth()->user()->phone ?? '' }}',
            };
            const amount = this.price * 100;
            const metadata = {
                registration_service: [this.serviceName],
                registration_data: user
            };
            let handler = PaystackPop.setup({
                key: '{{ env('PAYSTACK_PUBLIC_KEY') }}',
                email: user.email,
                amount: amount,
                currency: 'NGN',
                ref: 'TARABA_' + Math.floor((Math.random() * 1000000000) + 1),
                metadata: metadata,
                callback: function(response) {
                    window.location.href = '/user/payment/paystack/callback?reference=' + response.reference;
                },
                onClose: function() {}
            });
            handler.openIframe();
        }
    }
}

function dashboardPaymentModal() {
    return {
        showPaymentModal: false,
        selectedServices: [],
        totalAmount: 0,
        
        checkPaymentStatus() {
            // Show modal if user has no payments and payment setting is off
            const hasPayments = {{ auth()->user()->payments()->count() }};
            const paymentBeforeDataSave = {{ \App\Models\PaymentSetting::isPaymentBeforeDataSave() ? 'true' : 'false' }};
            
            if (hasPayments === 0 && !paymentBeforeDataSave) {
                this.showPaymentModal = true;
            }
        },
        
        closePaymentModal() {
            this.showPaymentModal = false;
        },
        
        updateTotalAmount() {
            this.totalAmount = this.selectedServices.reduce((total, service) => total + service.price, 0);
        },
        
        proceedToPayment() {
            if (this.selectedServices.length === 0) return;
            
            const user = {
                email: '{{ auth()->user()->email ?? '' }}',
                name: '{{ auth()->user()->name ?? '' }}',
                phone: '{{ auth()->user()->phone ?? '' }}',
            };
            
            const serviceNames = this.selectedServices.map(s => s.name);
            const amount = this.totalAmount * 100;
            const metadata = {
                registration_service: serviceNames,
                registration_data: user
            };
            
            let handler = PaystackPop.setup({
                key: '{{ env('PAYSTACK_PUBLIC_KEY') }}',
                email: user.email,
                amount: amount,
                currency: 'NGN',
                ref: 'TARABA_' + Math.floor((Math.random() * 1000000000) + 1),
                metadata: metadata,
                callback: function(response) {
                    window.location.href = '/user/payment/paystack/callback?reference=' + response.reference;
                },
                onClose: function() {}
            });
            handler.openIframe();
        }
    }
}

// Add event listeners for service checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.service-checkbox');
    const modal = document.querySelector('[x-data="dashboardPaymentModal()"]');
    
    if (modal && checkboxes.length > 0) {
        const alpineData = Alpine.$data(modal);
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const serviceName = this.value;
                const servicePrice = parseInt(this.dataset.price);
                const serviceData = { name: serviceName, price: servicePrice };
                
                if (this.checked) {
                    alpineData.selectedServices.push(serviceData);
                } else {
                    alpineData.selectedServices = alpineData.selectedServices.filter(s => s.name !== serviceName);
                }
                
                alpineData.updateTotalAmount();
            });
        });
    }
    
    // Initialize charts if admin and chart data exists
    @if(auth()->user()->role === 'admin' && isset($chartData))
    initializeCharts();
    @endif
});

@if(auth()->user()->role === 'admin' && isset($chartData))
// Add some custom CSS for better chart display
const style = document.createElement('style');
style.textContent = `
    .chart-container {
        position: relative;
        height: 250px;
        width: 100%;
    }
    .chart-container canvas {
        max-height: 100% !important;
    }
    .chart-container.doughnut {
        height: 280px;
    }
    @media (max-width: 1280px) {
        .chart-container {
            height: 220px;
        }
        .chart-container.doughnut {
            height: 250px;
        }
    }
    @media (max-width: 768px) {
        .chart-container {
            height: 180px;
        }
        .chart-container.doughnut {
            height: 200px;
        }
    }
    .dashboard-content {
        height: 100%;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        overflow-y: auto;
    }
    .chart-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .charts-section {
        max-height: calc(100vh - 8rem);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        padding-right: 0.5rem;
    }
    .charts-section::-webkit-scrollbar {
        width: 6px;
    }
    .charts-section::-webkit-scrollbar-track {
        background: transparent;
    }
    .charts-section::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 3px;
    }
    .charts-section::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.7);
    }
`;
document.head.appendChild(style);

function initializeCharts() {
    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: false
            }
        },
        elements: {
            point: {
                radius: 4,
                hoverRadius: 6,
                backgroundColor: '#fff',
                borderWidth: 2
            },
            line: {
                borderWidth: 3
            }
        }
    };

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($chartData['revenue_data']);
    
    // Process initial data to show 12 months
    const currentYear = new Date().getFullYear();
    const processedData = [];
    for (let month = 1; month <= 12; month++) {
        const monthData = revenueData.find(item => item.month === month);
        processedData.push({
            date: `${currentYear}-${month.toString().padStart(2, '0')}-01`,
            amount: monthData ? monthData.amount : 0
        });
    }
    
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: processedData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short' });
            }),
            datasets: [{
                label: 'Revenue (₦)',
                data: processedData.map(item => item.amount),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 10
                        },
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        },
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // User Registrations Chart
    const userRegCtx = document.getElementById('userRegistrationsChart').getContext('2d');
    const userRegData = @json($chartData['user_registrations']);
    
    // Process initial user data to show 12 months
    const userProcessedData = [];
    for (let month = 1; month <= 12; month++) {
        const monthData = userRegData.find(item => item.month === month);
        userProcessedData.push({
            date: `${currentYear}-${month.toString().padStart(2, '0')}-01`,
            count: monthData ? monthData.count : 0
        });
    }
    
    const userRegChart = new Chart(userRegCtx, {
        type: 'line',
        data: {
            labels: userProcessedData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short' });
            }),
            datasets: [{
                label: 'New Users',
                data: userProcessedData.map(item => item.count),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff'
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        },
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Revenue by Service Chart
    const serviceCtx = document.getElementById('revenueByServiceChart').getContext('2d');
    const serviceData = @json($chartData['revenue_by_service']);
    
    const serviceChart = new Chart(serviceCtx, {
        type: 'doughnut',
        data: {
            labels: serviceData.map(item => item.service),
            datasets: [{
                data: serviceData.map(item => item.amount),
                backgroundColor: [
                    '#22c55e',
                    '#3b82f6',
                    '#a855f7',
                    '#ec4899',
                    '#f59e0b',
                    '#ef4444',
                    '#10b981',
                    '#6366f1'
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        },
                        color: '#374151'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ₦${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Filter functionality
    const serviceFilter = document.getElementById('serviceFilter');
    const yearFilter = document.getElementById('yearFilter');
    
    if (serviceFilter) {
        serviceFilter.addEventListener('change', function() {
            updateRevenueChart();
        });
    }
    
    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            updateRevenueChart();
        });
    }
    
    // User registrations filter functionality
    const userYearFilter = document.getElementById('userYearFilter');
    
    if (userYearFilter) {
        userYearFilter.addEventListener('change', function() {
            updateUserRegistrationsChart();
        });
    }

    // Function to update revenue chart with filtered data
    async function updateRevenueChart() {
        const loadingDiv = document.getElementById('chartLoading');
        const selectedService = serviceFilter ? serviceFilter.value : '';
        const selectedYear = yearFilter ? yearFilter.value : new Date().getFullYear();
        
        try {
            // Show loading state
            if (loadingDiv) loadingDiv.classList.remove('hidden');
            
            const response = await fetch(`/admin/revenue-data?service=${encodeURIComponent(selectedService)}&year=${selectedYear}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }

            const data = await response.json();
            
            // Update chart data
            revenueChart.data.labels = data.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short' });
            });
            revenueChart.data.datasets[0].data = data.map(item => item.amount);
            
            // Update chart
            revenueChart.update('active');
            
        } catch (error) {
            console.error('Error updating revenue chart:', error);
        } finally {
            // Hide loading state
            if (loadingDiv) loadingDiv.classList.add('hidden');
        }
    }

    // Function to update user registrations chart with filtered data
    async function updateUserRegistrationsChart() {
        const loadingDiv = document.getElementById('userChartLoading');
        const selectedYear = userYearFilter ? userYearFilter.value : new Date().getFullYear();
        
        try {
            // Show loading state
            if (loadingDiv) loadingDiv.classList.remove('hidden');
            
            const response = await fetch(`/admin/user-registrations-data?year=${selectedYear}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }

            const data = await response.json();
            
            // Update chart data
            userRegChart.data.labels = data.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('en-US', { month: 'short' });
            });
            userRegChart.data.datasets[0].data = data.map(item => item.count);
            
            // Update chart
            userRegChart.update('active');
            
        } catch (error) {
            console.error('Error updating user registrations chart:', error);
        } finally {
            // Hide loading state
            if (loadingDiv) loadingDiv.classList.add('hidden');
        }
    }
}
@endif
</script>
@endsection