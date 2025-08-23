@extends('layouts.dashboard')
@section('title', 'Staff Activity Detail')
@section('header', 'Staff Activity Detail')
@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <a href="{{ route('admin.staff-activity') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition font-semibold mb-6">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to Staff Activity Overview
    </a>
    <h1 class="text-2xl font-bold text-green-900 mb-4">Staff Activity History ({{ ucfirst($action) }})</h1>
    <div class="bg-white rounded shadow p-8 mb-6">
        <div class="mb-2"><span class="font-semibold">Staff Name:</span> {{ $staff->name }}</div>
        <div class="mb-2"><span class="font-semibold">Email:</span> {{ $staff->email }}</div>
    </div>
    <div class="bg-white rounded shadow p-8">
        <h2 class="text-lg font-semibold mb-4">Activity Logs</h2>
        @if($logs->isEmpty())
            <div class="text-gray-500 py-8 text-center">No activity found.</div>
        @else
            <table class="w-full min-w-[1100px] divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($logs as $log)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $log->user ? $log->user->name : '-' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ ucfirst($log->action) }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $log->details }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $log->description }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            @if($log->payment_id)
                                <a href="{{ route('admin.user.payment.show', ['user' => $log->user_id, 'payment' => $log->payment_id, 'editable' => false, 'backUrl' => url()->current()]) }}" class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded hover:bg-green-200 font-semibold text-xs">View Details</a>
                            @else
                                <span class="text-gray-400 text-xs">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection 