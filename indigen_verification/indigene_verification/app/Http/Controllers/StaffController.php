<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Payment;
use App\Models\StaffActivityLog;

class StaffController extends Controller
{
    public function dashboard()
    {
        if (Auth::user()->role !== 'staff') abort(403);
        // Remove redirect for incomplete registration; modal form will handle it
        $staffId = Auth::id();
        $verifiedCount = StaffActivityLog::where('staff_id', $staffId)->where('action', 'verified')->count();
        $approvedCount = StaffActivityLog::where('staff_id', $staffId)->where('action', 'approved')->count();
        $disapprovedCount = StaffActivityLog::where('staff_id', $staffId)->where('action', 'disapproved')->count();
        return view('staff.dashboard', compact('verifiedCount', 'approvedCount', 'disapprovedCount'));
    }

    public function userList()
    {
        if (Auth::user()->role !== 'staff') abort(403);
        
        $currentUser = Auth::user();
        $users = $currentUser->getViewableUsers()->with('payments')->get();
        
        return view('staff.user-list', compact('users'));
    }

    public function verifyUser(Request $request, $userId)
    {
        if (Auth::user()->role !== 'staff') abort(403);
        
        $user = User::findOrFail($userId);
        
        if ($user->is_verified) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already verified.'
                ]);
            }
            return back()->with('status', 'User is already verified.');
        }

        // Check if user has a NIN for verification
        if (!$user->nin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a NIN. Cannot perform verification.'
                ]);
            }
            return back()->with('error', 'User does not have a NIN. Cannot perform verification.');
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

                // Log staff activity
                StaffActivityLog::create([
                    'staff_id' => Auth::id(),
                    'user_id' => $user->id,
                    'action' => 'verified',
                    'details' => 'Verified user with NIN validation',
                ]);

                // Send notification to user
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'verified',
                    'message' => 'You have been verified by staff. Your permanent ID is: ' . $user->permanent_id
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'User verified successfully with NIN validation. Permanent ID generated: ' . $user->permanent_id
                    ]);
                }
                return back()->with('status', 'User verified successfully with NIN validation. Permanent ID generated.');
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

                // Log staff activity for failed verification
                StaffActivityLog::create([
                    'staff_id' => Auth::id(),
                    'user_id' => $user->id,
                    'action' => 'verification_failed',
                    'details' => 'NIN verification failed: ' . $failureReason,
                ]);

                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'verification_failed',
                    'message' => 'Staff attempted verification but NIN validation failed: ' . $failureReason
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User verification failed due to NIN validation issues: ' . $failureReason
                    ]);
                }
                return back()->with('error', 'User verification failed due to NIN validation issues: ' . $failureReason);
            }
        } catch (\Exception $e) {
            \Log::error('Staff verification error', [
                'user_id' => $userId,
                'staff_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification failed due to a technical error. Please try again later.'
                ]);
            }
            return back()->with('error', 'Verification failed due to a technical error. Please try again later.');
        }
    }

    public function approveCertificate(Request $request, $paymentId)
    {
        if (Auth::user()->role !== 'staff') abort(403);
        $payment = Payment::findOrFail($paymentId);
        $request->validate([
            'approved' => 'required',
            'file' => $request->approved === 'approved' ? 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120' : 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'description' => $request->approved === 'not_approved' ? 'required|string' : 'nullable',
        ]);
        $payment->approved = $request->approved;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('certificates', 'public');
            $payment->file = $path;
        }
        $payment->save();
        // Log activity
        StaffActivityLog::create([
            'staff_id' => Auth::id(),
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'action' => $request->approved === 'approved' ? 'approved' : 'not_approved',
            'details' => ($request->approved === 'approved' ? 'Approved' : 'Not approved') . ' certificate: ' . $payment->service_name,
            'description' => $request->input('description'),
        ]);
        return back()->with('status', 'Certificate status updated.');
    }

    public function showUserPayment($userId, $paymentId)
    {
        if (Auth::user()->role !== 'staff') abort(403);
        $user = User::findOrFail($userId);
        $payment = Payment::findOrFail($paymentId);
        if ($payment->user_id != $user->id) abort(403);
        return view('staff.payment-show', compact('user', 'payment'));
    }
}