<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function userPayments(Request $request)
    {
        $currentUser = auth()->user();
        
        // Use the new method to get viewable users based on role and LGA assignment
        $query = $currentUser->getViewableUsers()->with(['payments']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nin', 'like', "%{$search}%")
                  ->orWhere('temporary_id', 'like', "%{$search}%")
                  ->orWhere('permanent_id', 'like', "%{$search}%");
            });
        }
        
        // Only show users (no role filtering needed)
        $query->where('role', 'user');
        
        // Pagination
        $users = $query->paginate(15)->withQueryString();
        
        // Staff activity log counts
        $staff = User::where('role', 'staff')->orderBy('name')->get();
        $staffActivity = [];
        foreach ($staff as $s) {
            $totalVerified = \App\Models\StaffActivityLog::where('staff_id', $s->id)->where('action', 'verified')->count();
            $totalApproved = \App\Models\StaffActivityLog::where('staff_id', $s->id)->where('action', 'approved')->count();
            $totalNotApproved = \App\Models\StaffActivityLog::where('staff_id', $s->id)->where('action', 'not_approved')->count();
            $staffActivity[$s->id] = [
                'verified' => $totalVerified,
                'approved' => $totalApproved,
                'not_approved' => $totalNotApproved,
            ];
        }
        return view('admin.user-payments', compact('users', 'staff', 'staffActivity'));
    }

    public function showUserPayment(User $user, Payment $payment, Request $request)
    {
        // Only allow if payment belongs to user
        if ($payment->user_id !== $user->id) {
            abort(404);
        }
        $editable = $request->query('editable', true);
        $backUrl = $request->query('backUrl');
        return view('admin.payment-detail', compact('user', 'payment', 'editable', 'backUrl'));
    }

    public function updatePaymentApproval(Request $request, User $user, Payment $payment)
    {
        if ($payment->user_id !== $user->id) {
            abort(404);
        }
        $request->validate([
            'approved' => 'required',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'description' => $request->approved === 'not_approved' ? 'required|string' : 'nullable',
        ]);
        $wasApproved = $payment->approved;
        $hadFile = (bool)$payment->file;
        $payment->approved = $request->approved;
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            // Basic file content validation
            $mime = $uploadedFile->getMimeType();
            $allowedMimes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            if (!in_array($mime, $allowedMimes)) {
                return redirect()->back()->withErrors(['file' => 'Invalid file type.']);
            }
            // Store in a private location (not public)
            $path = $uploadedFile->store('certificates');
            $payment->file = $path;
        }
        $payment->save();
        // Log disapproval reason if not_approved
        if ($request->approved === 'not_approved') {
            \App\Models\StaffActivityLog::create([
                'staff_id' => auth()->id(),
                'user_id' => $user->id,
                'payment_id' => $payment->id,
                'action' => 'not_approved',
                'details' => 'Admin disapproved certificate: ' . $payment->service_name,
                'description' => $request->input('description'),
            ]);
        }
        // Generate permanent ID if user is being verified for the first time
        // if (!$user->is_verified && $payment->approved) {
        //     $user->is_verified = true;
        //     $user->permanent_id = 'PRM-' . strtoupper(\Illuminate\Support\Str::random(8));
        //     $user->save();
        //     \Mail::to($user->email)->send(new \App\Mail\UserVerifiedMail($user));
        //     \App\Models\Notification::create([
        //         'user_id' => $user->id,
        //         'type' => 'verified',
        //         'message' => 'You have been verified. Your permanent ID is: ' . $user->permanent_id
        //     ]);
        // }
        // Send email if just approved and file is present
        if (!$wasApproved && $payment->approved && $payment->file) {
            \Mail::to($user->email)->send(new \App\Mail\CertificateApprovedMail($user, $payment->service_name));
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'certificate_approved',
                'message' => 'Your certificate for ' . $payment->service_name . ' has been approved. You can now download it from your dashboard.'
            ]);
        }
        return redirect()->back()->with('status', 'Payment approval updated.');
    }

    // Verify user action from admin dropdown
    public function verifyUser(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        if ($user->is_verified) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already verified.'
                ]);
            }
            return redirect()->back()->with('status', 'User is already verified.');
        }

        // Check if user has a NIN for verification
        if (!$user->nin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a NIN. Cannot perform verification.'
                ]);
            }
            return redirect()->back()->with('error', 'User does not have a NIN. Cannot perform verification.');
        }

        try {
            // Perform comprehensive NIN verification
            $ninVerificationService = new \App\Services\NinVerificationService();
            $ninVerification = $ninVerificationService->verifyNin($user->nin, $user);

            if ($ninVerification['status'] === 'success') {
                // NIN verification successful - proceed with user verification
                $user->is_verified = true;
                $user->nin_verification_status = 'verified';
                $user->nin_verification_failure_reason = null;
                $user->nin_verification_data = json_encode($ninVerification['data']);
                
                if (!$user->permanent_id) {
                    $permanentId = 'TAR-' . strtoupper(substr($user->state_of_origin, 0, 3)) . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                    $user->permanent_id = $permanentId;
                }
                
                $user->save();

                // Send verification email and notification
                \Mail::to($user->email)->send(new \App\Mail\UserVerifiedMail($user));
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'verified',
                    'message' => 'You have been verified by admin. Your permanent ID is: ' . $user->permanent_id
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'User verified successfully with NIN validation. Permanent ID generated: ' . $user->permanent_id
                    ]);
                }
                return redirect()->back()->with('status', 'User verified successfully with NIN validation. Permanent ID generated.');
            } else {
                // NIN verification failed - update failure reason
                if (isset($ninVerification['validation_details'])) {
                    $failureReason = $ninVerificationService->getVerificationFailureReason(
                        $ninVerification['data'] ?? [], 
                        $ninVerification['validation_details']
                    );
                } else {
                    $failureReason = $ninVerification['message'] ?? 'NIN verification failed';
                }

                $user->nin_verification_status = 'failed';
                $user->nin_verification_failure_reason = $failureReason;
                $user->nin_verification_data = isset($ninVerification['data']) ? json_encode($ninVerification['data']) : null;
                $user->save();

                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'verification_failed',
                    'message' => 'Admin attempted verification but NIN validation failed: ' . $failureReason
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User verification failed due to NIN validation issues: ' . $failureReason
                    ]);
                }
                return redirect()->back()->with('error', 'User verification failed due to NIN validation issues: ' . $failureReason);
            }
        } catch (\Exception $e) {
            \Log::error('Admin verification error', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification failed due to a technical error. Please try again later.'
                ]);
            }
            return redirect()->back()->with('error', 'Verification failed due to a technical error. Please try again later.');
        }
    }

    public function staffActivity(Request $request)
    {
        $query = \App\Models\User::where('role', 'staff')->orderBy('name');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $staff = $query->paginate(15)->withQueryString();
        
        // Calculate activity counts for paginated staff
        $staffActivity = [];
        foreach ($staff as $s) {
            $totalVerified = \App\Models\StaffActivityLog::where('staff_id', $s->id)->where('action', 'verified')->count();
            $totalApproved = \App\Models\StaffActivityLog::where('staff_id', $s->id)->where('action', 'approved')->count();
            $totalNotApproved = \App\Models\StaffActivityLog::where('staff_id', $s->id)->where('action', 'not_approved')->count();
            $staffActivity[$s->id] = [
                'verified' => $totalVerified,
                'approved' => $totalApproved,
                'not_approved' => $totalNotApproved,
            ];
        }
        
        return view('admin.staff-activity', compact('staff', 'staffActivity'));
    }

    public function staffActivityDetail($staffId, $action)
    {
        $staff = \App\Models\User::findOrFail($staffId);
        $logs = \App\Models\StaffActivityLog::where('staff_id', $staffId)
            ->where('action', $action)
            ->orderByDesc('created_at')
            ->with('user')
            ->get();
        $backUrl = route('admin.staff-activity.detail', ['staff' => $staffId, 'action' => $action]);
        return view('admin.staff-activity-detail', compact('staff', 'action', 'logs', 'backUrl'));
    }

    public function getRevenueData(Request $request)
    {
        // Get year filter, default to current year
        $year = $request->filled('year') ? $request->year : date('Y');
        
        $query = Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(price) as amount')
            ->whereYear('created_at', $year)
            ->groupBy('year', 'month')
            ->orderBy('month');

        // Filter by service if specified
        if ($request->filled('service') && $request->service !== '') {
            $query->where('service_name', $request->service);
        }

        $data = $query->get()->map(function($item) {
            return [
                'month' => (int) $item->month,
                'amount' => (float) $item->amount
            ];
        });

        // Fill in missing months with zero values
        $filledData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->where('month', $month)->first();
            $filledData[] = [
                'date' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01',
                'amount' => $monthData ? $monthData['amount'] : 0
            ];
        }

        return response()->json($filledData);
    }

    public function getUserRegistrationsData(Request $request)
    {
        // Get year filter, default to current year
        $year = $request->filled('year') ? $request->year : date('Y');
        
        $query = User::where('role', 'user')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('year', 'month')
            ->orderBy('month');

        $data = $query->get()->map(function($item) {
            return [
                'month' => (int) $item->month,
                'count' => (int) $item->count
            ];
        });

        // Fill in missing months with zero values
        $filledData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $data->where('month', $month)->first();
            $filledData[] = [
                'date' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01',
                'count' => $monthData ? $monthData['count'] : 0
            ];
        }

        return response()->json($filledData);
    }

    public function debugRevenue()
    {
        $payments = Payment::select('created_at', 'price', 'service_name')
            ->orderBy('created_at')
            ->get();
        
        $years = Payment::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year')
            ->pluck('year');
        
        $monthlyData = Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(price) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        return response()->json([
            'payments' => $payments,
            'available_years' => $years,
            'monthly_data' => $monthlyData
        ]);
    }
}