<!DOCTYPE html>
<html lang="en" x-data="{ sidebarOpen: false }" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Taraba State Indigene Verification')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/taraba-state-logo-nedc.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/taraba-state-logo-nedc.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/taraba-state-logo-nedc.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen h-full">
<!-- Success Toast -->
<div x-data="{ show: false, message: '' }" x-init="
    @if(session('status'))
        message = '{{ session('status') }}';
        show = true;
        setTimeout(() => show = false, 4000);
    @endif
" x-show="show" x-transition class="fixed top-6 right-6 z-50 bg-green-600 text-white px-6 py-3 rounded shadow-lg text-base font-semibold flex items-center gap-2" style="display: none;">
    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span x-text="message"></span>
</div>

<!-- Error Toast -->
<div x-data="{ show: false, message: '' }" x-init="
    @if(session('error'))
        message = '{{ session('error') }}';
        show = true;
        setTimeout(() => show = false, 5000);
    @endif
" x-show="show" x-transition class="fixed top-6 right-6 z-50 bg-red-600 text-white px-6 py-3 rounded shadow-lg text-base font-semibold flex items-center gap-2" style="display: none;">
    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span x-text="message"></span>
</div>

<!-- Dynamic Toast for AJAX responses -->
<div id="dynamic-toast" x-data="{ show: false, message: '', type: 'success' }" x-show="show" x-transition 
     :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'" 
     class="fixed top-6 right-6 z-50 text-white px-6 py-3 rounded shadow-lg text-base font-semibold flex items-center gap-2" style="display: none;">
    <svg x-show="type === 'success'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <svg x-show="type === 'error'" class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <span x-text="message"></span>
</div>
<div class="flex min-h-screen h-full">
    <!-- Sidebar -->
    <aside :class="{'-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen}" class="fixed md:static z-40 md:z-auto top-0 left-0 h-screen w-64 bg-green-900 text-white flex flex-col py-8 px-6 shadow-lg transform md:translate-x-0 transition-transform duration-200 ease-in-out md:relative md:flex md:translate-x-0 md:w-64" style="min-width: 16rem;">
        <div class="flex flex-col items-center mb-10">
            <div class="bg-white border-4 border-green-700 rounded-full p-2 shadow mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('img/taraba-state-logo-nedc.png') }}" alt="Taraba State Logo" style="width: 50px; height: 50px; object-fit: contain;" />
            </div>
            <div class="text-lg font-bold text-center leading-tight">Taraba State<br>Indigene Verification</div>
        </div>
        <nav class="flex-1">
            <ul class="space-y-2">
                <li>
                    @if(auth()->user()->role === 'staff')
                        <a href="{{ route('staff.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('staff.dashboard') ? 'bg-green-800 font-semibold' : '' }}">
                    @else
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('dashboard') ? 'bg-green-800 font-semibold' : '' }}">
                    @endif
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Dashboard
                    </a>
                </li>
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('services.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('services.*') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Service Management
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.user-payments') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.user-payments') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a4 4 0 018 0v2M12 7a4 4 0 110 8 4 4 0 010-8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        User Management
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.staff-activity') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.staff-activity') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a4 4 0 014-4h4M7 7h.01M7 11h.01M7 15h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Staff Activity
                    </a>
                </li>
                <li>
                    <a href="{{ route('register.staff') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('register.staff') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        Create Staff
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.payment-settings') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.payment-settings') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Payment Settings
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.staff-lga') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('admin.staff-lga*') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Staff LGA Management
                    </a>
                </li>
                @elseif(auth()->user()->role === 'staff')
                <li>
                    <a href="{{ route('staff.users') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('staff.users') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2a4 4 0 018 0v2M12 7a4 4 0 110 8 4 4 0 010-8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        User List
                    </a>
                </li>
                @else
                <li>
                    <a href="{{ route('certificates.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('certificates.index') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Downloaded Certificates
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-green-800 transition {{ request()->routeIs('profile.edit') ? 'bg-green-800 font-semibold' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Profile
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-3 rounded-lg hover:bg-green-800 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <div class="mt-10 text-xs text-green-200 text-center">&copy; {{ date('Y') }} Taraba State Government</div>
        <!-- Mobile close button -->
        <button @click="sidebarOpen = false" class="md:hidden absolute top-4 right-4 text-white text-3xl focus:outline-none">&times;</button>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow px-4 md:px-8 py-5 flex items-center justify-between">
            <!-- Hamburger for mobile -->
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-green-900 text-3xl focus:outline-none mr-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="text-xl font-bold text-green-900">@yield('header', 'Dashboard')</div>
            <div class="flex items-center gap-4">
                <div x-data="{ open: false }" class="relative">
                    <button @click="if (!open) { open = true; markNotificationsRead(); } else { open = false; }" class="relative focus:outline-none group" aria-label="Notifications" id="notification-bell">
                        <svg class="w-7 h-7 text-green-900 group-hover:text-green-700 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php $unread = isset($notifications) ? $notifications->whereNull('read_at')->count() : 0; @endphp
                        @if($unread > 0)
                            <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5">{{ $unread }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                        <div class="px-4 py-2 border-b font-semibold text-gray-700">Notifications</div>
                        <ul id="notification-list">
                            @forelse($notifications ?? [] as $notification)
                                <li class="px-4 py-3 border-b last:border-b-0 text-sm flex items-center {{ $notification->read_at ? 'bg-white' : 'bg-green-50' }}" data-id="{{ $notification->id }}">
                                    @if(!$notification->read_at)
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    @endif
                                    <div class="flex-1">
                                        @if(isset($notification->link) && $notification->link)
                                            <a href="{{ $notification->link }}" class="font-medium text-green-800 hover:underline">{{ $notification->message }}</a>
                                        @else
                                            <div class="font-medium text-gray-900">{{ $notification->message }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                    <button class="ml-2 mark-read-btn" @click.prevent="markSingleRead({{ $notification->id }}, $event)" title="Mark as read">
                                        <svg class="w-4 h-4 text-green-600 hover:text-green-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button class="ml-2 delete-btn" @click.prevent="deleteNotification({{ $notification->id }}, $event)" title="Delete">
                                        <svg class="w-4 h-4 text-red-600 hover:text-red-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </li>
                            @empty
                                <li class="px-4 py-3 text-gray-400 text-sm">No notifications.</li>
                            @endforelse
                        </ul>
                        @if(($notifications ?? collect())->count() >= 20)
                            <button id="load-more-notifications" class="w-full py-2 text-center text-green-700 hover:bg-green-50 border-t">Load More</button>
                        @endif
                    </div>
                </div>
                <div class="text-gray-700 font-semibold truncate max-w-xs">{{ auth()->user()->name }}</div>
                <div class="w-10 h-10 rounded-full bg-green-200 flex items-center justify-center text-green-900 font-bold text-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>
        <!-- Content Area -->
        <section class="flex-1 p-4 md:p-10 bg-gray-50 h-screen overflow-y-auto">
            @yield('content')
        </section>
    </main>
</div>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
// Function to show dynamic toast notifications
function showToast(message, type = 'success') {
    const toast = document.getElementById('dynamic-toast');
    if (toast) {
        // Use Alpine.js to update the toast
        Alpine.store('toast', {
            show: true,
            message: message,
            type: type
        });
        
        // Update the toast element directly
        toast.__x.$data.message = message;
        toast.__x.$data.type = type;
        toast.__x.$data.show = true;
        
        // Hide after 4-5 seconds
        setTimeout(() => {
            toast.__x.$data.show = false;
        }, type === 'error' ? 5000 : 4000);
    }
}

function markNotificationsRead() {
    fetch("{{ route('notifications.markRead') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json',
        },
    }).then(() => {
        const badge = document.getElementById('notification-badge');
        if (badge) badge.style.display = 'none';
        // Optionally update all notifications to read in UI
        document.querySelectorAll('#notification-list li').forEach(li => {
            li.classList.remove('bg-green-50');
            li.classList.add('bg-white');
            const dot = li.querySelector('.w-2.h-2');
            if (dot) dot.style.display = 'none';
        });
    });
}
function markSingleRead(id, event) {
    fetch(`/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json',
        },
    }).then(() => {
        const li = event.target.closest('li');
        if (li) {
            li.classList.remove('bg-green-50');
            li.classList.add('bg-white');
            const dot = li.querySelector('.w-2.h-2');
            if (dot) dot.style.display = 'none';
            event.target.style.display = 'none';
        }
        // Optionally update badge
        const badge = document.getElementById('notification-badge');
        if (badge) {
            let count = parseInt(badge.textContent);
            if (count > 1) badge.textContent = count - 1;
            else badge.style.display = 'none';
        }
    });
}
function deleteNotification(id, event) {
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json',
        },
    }).then(() => {
        const li = event.target.closest('li');
        if (li) li.remove();
        // Optionally update badge
        const badge = document.getElementById('notification-badge');
        if (badge) {
            let count = parseInt(badge.textContent);
            if (count > 1) badge.textContent = count - 1;
            else badge.style.display = 'none';
        }
    });
}
// Load More button (basic, reloads page with ?page=2 for now)
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-notifications');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            window.location.href = window.location.pathname + '?load_more_notifications=1';
        });
    }
});
</script>
</body>
</html>