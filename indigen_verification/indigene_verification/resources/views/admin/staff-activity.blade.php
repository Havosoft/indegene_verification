@extends('layouts.dashboard')
@section('title', 'Staff Activity')
@section('header', 'Staff Activity')
@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Staff Activity</h1>
                <p class="mt-2 text-sm text-gray-600">Monitor staff verification and approval activities</p>
            </div>
        </div>
    </div>
    
    <!-- Search Section -->
    <div class="mb-6 bg-white shadow rounded-lg border border-gray-200">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('admin.staff-activity') }}" class="space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <label for="search" class="sr-only">Search staff</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" 
                                   value="{{ request('search') }}"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                   placeholder="Search by staff name or email...">
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex items-center space-x-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            Search
                        </button>
                        
                        <!-- Clear Filters -->
                        @if(request('search'))
                            <a href="{{ route('admin.staff-activity') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            
            <!-- Results Summary -->
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $staff->firstItem() ?? 0 }} to {{ $staff->lastItem() ?? 0 }} of {{ $staff->total() }} staff members
                </div>
                @if(request('search'))
                    <div class="text-sm text-gray-500">
                        Filtered results
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="bg-white shadow rounded-lg border border-gray-200 overflow-x-auto">
        <table class="w-full min-w-[900px] divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Name</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Verified</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Approved</th>
                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Not Approved</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($staff as $s)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $s->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $s->email }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.staff-activity.detail', ['staff' => $s->id, 'action' => 'verified']) }}" class="text-green-700 font-bold hover:underline">
                            {{ $staffActivity[$s->id]['verified'] ?? 0 }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.staff-activity.detail', ['staff' => $s->id, 'action' => 'approved']) }}" class="text-blue-700 font-bold hover:underline">
                            {{ $staffActivity[$s->id]['approved'] ?? 0 }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.staff-activity.detail', ['staff' => $s->id, 'action' => 'not_approved']) }}" class="text-red-700 font-bold hover:underline">
                            {{ $staffActivity[$s->id]['not_approved'] ?? 0 }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="space-y-3">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <div class="text-sm text-gray-500">No staff members found</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($staff->hasPages())
        <div class="mt-6 bg-white shadow rounded-lg border border-gray-200">
            <div class="px-6 py-4">
                {{ $staff->links() }}
            </div>
        </div>
    @endif
</div>
{{-- Staff Activity Modal --}}
<div id="staff-activity-modal" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full p-6 relative">
        <button id="close-staff-activity-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
        <h3 id="staff-activity-modal-title" class="text-xl font-bold mb-4">Staff Activity History</h3>
        <div id="staff-activity-modal-content" class="max-h-96 overflow-y-auto text-sm"></div>
    </div>
</div>
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
    const clearButton = document.querySelector('a[href="{{ route("admin.staff-activity") }}"]');
    if (clearButton) {
        clearButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.href;
        });
    }
    
    // Staff activity modal functionality
    document.querySelectorAll('.staff-activity-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const staffId = this.getAttribute('data-staff-id');
            const action = this.getAttribute('data-action');
            const modal = document.getElementById('staff-activity-modal');
            const content = document.getElementById('staff-activity-modal-content');
            const title = document.getElementById('staff-activity-modal-title');
            title.textContent = 'Staff Activity History (' + action.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) + ')';
            content.innerHTML = '<div class="text-gray-500 py-8 text-center">Loading...</div>';
            modal.classList.remove('hidden');
            fetch(`/admin/staff/${staffId}/activity?action=${action}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML = '<div class="text-gray-500 py-8 text-center">No activity found.</div>';
                    } else {
                        content.innerHTML = '<ul class="divide-y divide-gray-200">' + data.map(item =>
                            `<li class="py-3"><div><span class="font-semibold">Date:</span> ${item.created_at} <span class="font-semibold ml-4">User:</span> ${item.user_name} <span class="font-semibold ml-4">Action:</span> ${item.action} <span class="font-semibold ml-4">Details:</span> ${item.details || ''} <span class="font-semibold ml-4">Description:</span> ${item.description || ''}</div></li>`
                        ).join('') + '</ul>';
                    }
                });
        });
    });
    document.getElementById('close-staff-activity-modal').addEventListener('click', function() {
        document.getElementById('staff-activity-modal').classList.add('hidden');
    });
});
</script>
@endsection 