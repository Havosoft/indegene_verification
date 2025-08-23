<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Models\User;
use App\Models\Payment;
use App\Models\Service;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $stats = null;
    $services = null;
    $requiredServices = null;
    $chartData = null;
    $notifications = \App\Models\Notification::where('user_id', $user->id)->latest()->take(20)->get();
    
    if ($user->role === 'admin') {
        // Basic stats
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_staff' => User::where('role', 'staff')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_payments' => Payment::count(),
            'total_revenue' => Payment::sum('price'),
            'total_services' => Service::count(),
            'recent_payments' => Payment::with('user')->latest()->take(3)->get(),
            'new_users' => User::where('role', 'user')->latest()->take(3)->get(),
        ];
        
        // Chart data
        $chartData = [
            // Revenue over time (current year)
            'revenue_data' => Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(price) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('year', 'month')
                ->orderBy('month')
                ->get()
                ->map(function($item) {
                    return [
                        'month' => (int) $item->month,
                        'amount' => $item->total
                    ];
                }),
            
            // User registrations over time (current year)
            'user_registrations' => User::where('role', 'user')
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('year', 'month')
                ->orderBy('month')
                ->get()
                ->map(function($item) {
                    return [
                        'month' => (int) $item->month,
                        'count' => $item->count
                    ];
                }),
            
            // Revenue by service
            'revenue_by_service' => Payment::selectRaw('service_name, SUM(price) as total')
                ->groupBy('service_name')
                ->orderByDesc('total')
                ->get()
                ->map(function($item) {
                    return [
                        'service' => $item->service_name,
                        'amount' => $item->total
                    ];
                }),
            
            // Last 30 days revenue
            'last_30_days_revenue' => Payment::where('created_at', '>=', now()->subDays(30))->sum('price'),
            
            // Last 7 days revenue
            'last_7_days_revenue' => Payment::where('created_at', '>=', now()->subDays(7))->sum('price'),
            
            // Available services for filter
            'available_services' => Service::where('is_active', true)->pluck('name')->toArray()
        ];
    } else {
        $services = Service::where('is_active', true)->get();
        $requiredServices = Service::where('is_active', true)->where('is_required', true)->get();
    }
    
    return view('dashboard', compact('stats', 'services', 'requiredServices', 'notifications', 'chartData'));
})->middleware(['auth', 'verified', 'check.nin.verification'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/register/service', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'showServiceSelection'])->name('register.service');
Route::post('/register/service', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'selectService']);
Route::get('/register/payment', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'showPayment'])->name('register.payment');
Route::post('/register/payment', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'processPayment']);
Route::get('/register/admin', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'createAdmin'])->name('register.admin');
Route::post('/register/admin', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'storeAdmin'])->name('register.admin.store');

Route::post('/payment/initialize', [PaymentController::class, 'initialize'])->name('payment.initialize');
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/paystack/callback', [PaymentController::class, 'paystackCallback'])->name('payment.paystack.callback');
Route::get('/user/payment/paystack/callback', [\App\Http\Controllers\UserPaymentController::class, 'paystackCallback'])->name('user.paystack.callback');

// NIN Reverification route
Route::post('/nin/reverify', [\App\Http\Controllers\UserPaymentController::class, 'reverifyNin'])->name('nin.reverify')->middleware(['auth']);

// AJAX endpoint for email existence check
Route::get('/register/check-email', function (\Illuminate\Http\Request $request) {
    $email = $request->query('email');
    $exists = \App\Models\User::where('email', $email)->exists();
    return response()->json(['exists' => $exists]);
});

// AJAX endpoint for phone existence check
Route::get('/register/check-phone', function (\Illuminate\Http\Request $request) {
    $phone = $request->query('phone');
    $exists = \App\Models\User::where('phone', $phone)->exists();
    return response()->json(['exists' => $exists]);
});
// AJAX endpoint for NIN existence check
Route::get('/register/check-nin', function (\Illuminate\Http\Request $request) {
    $nin = $request->query('nin');
    $exists = \App\Models\User::where('nin', $nin)->exists();
    return response()->json(['exists' => $exists]);
});

// Service management (admin only)
Route::middleware(['auth'])->group(function () {
    Route::resource('services', \App\Http\Controllers\ServiceController::class);
});

// Admin User Payments
use App\Http\Controllers\AdminController;
Route::get('/admin/user-payments', [AdminController::class, 'userPayments'])->name('admin.user-payments')->middleware(['auth']);
// Admin view and approve/disapprove a user's payment
Route::get('/admin/users/{user}/payments/{payment}', [AdminController::class, 'showUserPayment'])->name('admin.user.payment.show')->middleware(['auth']);
Route::post('/admin/users/{user}/payments/{payment}/approval', [AdminController::class, 'updatePaymentApproval'])->name('admin.user.payment.approval')->middleware(['auth']);
// AJAX endpoint for staff activity log
Route::get('/admin/staff/{staff}/activity', function ($staffId) {
    $action = request('action');
    $query = \App\Models\StaffActivityLog::where('staff_id', $staffId);
    if ($action) {
        $query->where('action', $action);
    }
    $logs = $query->orderByDesc('created_at')->with('user')->get()->map(function($log) {
        return [
            'created_at' => $log->created_at->format('Y-m-d H:i'),
            'user_name' => $log->user ? $log->user->name : '-',
            'action' => $log->action,
            'details' => $log->details,
            'description' => $log->description,
        ];
    });
    return response()->json($logs);
});
// Admin Create Staff placeholder
Route::get('/register/staff', function () {
    return view('admin.register-staff');
})->name('register.staff')->middleware(['auth']);

// Staff registration POST
use App\Http\Controllers\Auth\RegisteredUserController;
Route::post('/register/staff', [RegisteredUserController::class, 'storeStaff'])->name('register.staff.store')->middleware(['auth']);

Route::get('/certificates', function () {
    $user = auth()->user();
    $certificates = $user->payments()->get();
    return view('certificates.index', compact('certificates'));
})->middleware(['auth'])->name('certificates.index');

Route::post('/admin/users/{user}/verify', [\App\Http\Controllers\AdminController::class, 'verifyUser'])->name('admin.user.verify')->middleware(['auth']);

Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->middleware(['auth'])->name('notifications.markRead');
Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->middleware(['auth'])->name('notifications.markSingleRead');
Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'delete'])->middleware(['auth'])->name('notifications.delete');

Route::get('/certificates/download/{id}', [\App\Http\Controllers\CertificateController::class, 'downloadCertificate'])->middleware(['auth'])->name('certificates.download');

// NIN Verification Failed route (now handled via modal on dashboard)
// Route::get('/verification/failed', function () {
//     return view('auth.verification-failed');
// })->middleware('auth')->name('verification.failed');

// Test route for NIN verification
Route::get('/test-nin-verification/{nin}', function ($nin) {
    $ninService = new \App\Services\NinVerificationService();
    $result = $ninService->verifyNin($nin);
    
    return response()->json([
        'nin' => $nin,
        'verification_result' => $result
    ]);
})->middleware('auth');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/staff/dashboard', [\App\Http\Controllers\StaffController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/staff/users', [\App\Http\Controllers\StaffController::class, 'userList'])->name('staff.users');
    Route::post('/staff/users/{user}/verify', [\App\Http\Controllers\StaffController::class, 'verifyUser'])->name('staff.users.verify');
    Route::post('/staff/certificates/{payment}/approve', [\App\Http\Controllers\StaffController::class, 'approveCertificate'])->name('staff.certificates.approve');
    Route::post('/staff/certificates/{payment}/disapprove', [\App\Http\Controllers\StaffController::class, 'disapproveCertificate'])->name('staff.certificates.disapprove');
    Route::get('/staff/users/{user}/payments/{payment}', [\App\Http\Controllers\StaffController::class, 'showUserPayment'])->name('staff.user.payment.show')->middleware(['auth']);
});

// Admin Staff Activity page
Route::get('/admin/staff-activity', [\App\Http\Controllers\AdminController::class, 'staffActivity'])->name('admin.staff-activity')->middleware(['auth']);
// Admin Staff Activity detail page
Route::get('/admin/staff-activity/{staff}/{action}', [\App\Http\Controllers\AdminController::class, 'staffActivityDetail'])->name('admin.staff-activity.detail')->middleware(['auth']);

// Payment Settings (admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/payment-settings', [\App\Http\Controllers\PaymentSettingController::class, 'index'])->name('admin.payment-settings');
    Route::post('/admin/payment-settings/update', [\App\Http\Controllers\PaymentSettingController::class, 'update'])->name('admin.payment-settings.update');
    Route::post('/admin/payment-settings/toggle', [\App\Http\Controllers\PaymentSettingController::class, 'toggle'])->name('admin.payment-settings.toggle');
});

// Staff LGA Management (admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/staff-lga', [\App\Http\Controllers\StaffLgaController::class, 'index'])->name('admin.staff-lga');
    Route::put('/admin/staff-lga/{staff}', [\App\Http\Controllers\StaffLgaController::class, 'update'])->name('admin.staff-lga.update');
    Route::post('/admin/staff-lga/bulk-update', [\App\Http\Controllers\StaffLgaController::class, 'bulkUpdate'])->name('admin.staff-lga.bulk-update');
    Route::post('/admin/reassign-users', [\App\Http\Controllers\StaffLgaController::class, 'reassignUsers'])->name('admin.reassign-users');
    Route::get('/admin/staff/{staff}/assigned-users-count', [\App\Http\Controllers\StaffLgaController::class, 'getAssignedUsersCount'])->name('admin.staff.assigned-users-count');
});

// Chart data routes (admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/revenue-data', [\App\Http\Controllers\AdminController::class, 'getRevenueData'])->name('admin.revenue-data');
    Route::get('/admin/user-registrations-data', [\App\Http\Controllers\AdminController::class, 'getUserRegistrationsData'])->name('admin.user-registrations-data');
    Route::get('/admin/debug-revenue', [\App\Http\Controllers\AdminController::class, 'debugRevenue'])->name('admin.debug-revenue');
});
