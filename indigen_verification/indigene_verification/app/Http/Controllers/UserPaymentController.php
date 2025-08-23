<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use App\Services\NinVerificationService;

class UserPaymentController extends Controller
{
    // Handle Paystack payment callback for authenticated users only
    public function paystackCallback(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to complete payment.');
        }

        $reference = $request->query('reference');
        if (!$reference) {
            return redirect()->route('dashboard')->with('error', 'No payment reference provided.');
        }

        $secretKey = env('PAYSTACK_SECRET_KEY');
        $response = Http::withToken($secretKey)
            ->get('https://api.paystack.co/transaction/verify/' . $reference);

        Log::info('User Paystack callback', [
            'user_id' => Auth::id(),
            'metadata' => $response['data']['metadata'] ?? null,
            'full_response' => $response->json(),
        ]);

        if ($response->successful() && isset($response['data']['status']) && $response['data']['status'] === 'success') {
            $user = Auth::user();
            $services = $response['data']['metadata']['registration_service'] ?? [];
            try {
                foreach ($services as $serviceName) {
                    $serviceModel = Service::where('name', $serviceName)->first();
                    if ($serviceModel) {
                        Payment::create([
                            'user_id' => $user->id,
                            'service_name' => $serviceModel->name,
                            'price' => $serviceModel->price,
                            'payment_ref' => $response['data']['reference'],
                            'payer_name' => $user->name,
                            'payer_email' => $user->email,
                            'approved' => 'awaiting_review', // Keep payment awaiting admin review
                        ]);
                    } else {
                        Log::warning('Service not found for payment', ['serviceName' => $serviceName]);
                    }
                }
                
                // Verify NIN with comprehensive validation
                $ninVerificationService = new NinVerificationService();
                $ninVerification = $ninVerificationService->verifyNin($user->nin, $user);
                
                if ($ninVerification['status'] === 'success') {
                    // The service now handles comprehensive validation internally
                    // Auto-verify user account after successful NIN verification
                    $user->update([
                        'is_verified' => true,
                        'services_paid_for' => implode(',', $services),
                        'nin_verification_status' => 'verified',
                        'nin_verification_data' => json_encode($ninVerification['data'])
                    ]);
                    
                    // Create a permanent ID for the user
                    $permanentId = 'TAR-' . strtoupper(substr($user->state_of_origin, 0, 3)) . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                    $user->update(['permanent_id' => $permanentId]);
                    
                    \Mail::to($user->email)->send(new \App\Mail\PaymentReceivedMail($user, $services));
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'payment_received',
                        'message' => 'Your payment has been received and your account has been verified! Your certificate will be available after admin approval.'
                    ]);
                    
                    return redirect()->route('dashboard')->with('success', 'Payment successful! Your account has been verified. Your certificate will be available after admin approval.');
                } else {
                    // Check if this is a validation failure (detailed reason provided)
                    if (isset($ninVerification['validation_details'])) {
                        $failureReason = $ninVerificationService->getVerificationFailureReason(
                            $ninVerification['data'] ?? [], 
                            $ninVerification['validation_details']
                        );
                    } else {
                        // Enhanced error messages for service failures
                        $baseMessage = $ninVerification['message'] ?? 'NIN verification failed';
                        $failureReason = $baseMessage;
                        
                        if (stripos($baseMessage, 'unavailable') !== false) {
                            $failureReason = "NIN verification service is currently unavailable due to network connectivity issues or server maintenance. Please check your internet connection and try again in a few minutes.";
                        } elseif (stripos($baseMessage, 'try again later') !== false) {
                            $failureReason = "NIN verification temporarily failed due to high server load or temporary service disruption. Please wait a few minutes and try again. If the problem persists, contact support.";
                        } elseif (stripos($baseMessage, 'invalid') !== false) {
                            $failureReason = "The NIN format appears to be invalid or the NIN could not be found in the national database. Please verify your NIN is correct (11 digits) and try again.";
                        } else {
                            $failureReason = $baseMessage . " - This could be due to network issues, server problems, or an invalid NIN format. Please check your NIN and internet connection, then try again.";
                        }
                    }
                    
                    $user->update([
                        'services_paid_for' => implode(',', $services),
                        'nin_verification_status' => 'failed',
                        'nin_verification_failure_reason' => $failureReason,
                        'nin_verification_data' => isset($ninVerification['data']) ? json_encode($ninVerification['data']) : null
                    ]);
                    
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'type' => 'verification_failed',
                        'message' => 'Your payment was received but verification failed: ' . $failureReason
                    ]);
                    
                    return redirect()->route('dashboard')
                        ->with('nin_verification_failed', true)
                        ->with('failure_reason', $failureReason);
                }
                
            } catch (\Exception $e) {
                Log::error('Payment creation failed', ['error' => $e->getMessage()]);
                return redirect()->route('dashboard')->with('error', 'Payment could not be saved. Please contact support.');
            }
        }

        return redirect()->route('dashboard')->with('error', 'Payment verification failed.');
    }

    // Handle NIN reverification for users with failed verification
    public function reverifyNin(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to reverify your NIN.');
        }

        $user = Auth::user();
        
        // Check if user has failed NIN verification
        if ($user->nin_verification_status !== 'failed') {
            return redirect()->route('dashboard')->with('error', 'NIN reverification is only available for users with failed verification.');
        }

        try {
            // Perform NIN verification with comprehensive validation
            $ninVerificationService = new NinVerificationService();
            $ninVerification = $ninVerificationService->verifyNin($user->nin, $user);
            
            if ($ninVerification['status'] === 'success') {
                // The service now handles comprehensive validation internally
                // Update user verification status
                $user->update([
                    'is_verified' => true,
                    'nin_verification_status' => 'verified',
                    'nin_verification_data' => json_encode($ninVerification['data']),
                    'nin_verification_failure_reason' => null // Clear previous failure reason
                ]);
                
                // Create a permanent ID for the user if not already created
                if (!$user->permanent_id) {
                    $permanentId = 'TAR-' . strtoupper(substr($user->state_of_origin, 0, 3)) . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                    $user->update(['permanent_id' => $permanentId]);
                }
                
                // Create notification
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'verification_success',
                    'message' => 'Your NIN has been successfully reverified! Your account is now verified.'
                ]);
                
                return redirect()->route('dashboard')->with('success', 'NIN reverification successful! Your account has been verified.');
            } else {
                // Check if this is a validation failure (detailed reason provided)
                if (isset($ninVerification['validation_details'])) {
                    $failureReason = $ninVerificationService->getVerificationFailureReason(
                        $ninVerification['data'] ?? [], 
                        $ninVerification['validation_details']
                    );
                } else {
                    // Enhanced error messages for service failures
                    $baseMessage = $ninVerification['message'] ?? 'NIN verification failed';
                    $failureReason = $baseMessage;
                    
                    if (stripos($baseMessage, 'unavailable') !== false) {
                        $failureReason = "NIN verification service is currently unavailable due to network connectivity issues or server maintenance. Please check your internet connection and try again in a few minutes.";
                    } elseif (stripos($baseMessage, 'try again later') !== false) {
                        $failureReason = "NIN verification temporarily failed due to high server load or temporary service disruption. Please wait a few minutes and try again. If the problem persists, contact support.";
                    } elseif (stripos($baseMessage, 'invalid') !== false) {
                        $failureReason = "The NIN format appears to be invalid or the NIN could not be found in the national database. Please verify your NIN is correct (11 digits) and try again.";
                    } else {
                        $failureReason = $baseMessage . " - This could be due to network issues, server problems, or an invalid NIN format. Please check your NIN and internet connection, then try again.";
                    }
                }
                
                $user->update([
                    'nin_verification_status' => 'failed',
                    'nin_verification_failure_reason' => $failureReason,
                    'nin_verification_data' => isset($ninVerification['data']) ? json_encode($ninVerification['data']) : null
                ]);
                
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'verification_failed',
                    'message' => 'NIN reverification failed: ' . $failureReason
                ]);
                
                return redirect()->route('dashboard')
                    ->with('nin_verification_failed', true)
                    ->with('failure_reason', $failureReason);
            }
            
        } catch (\Exception $e) {
            Log::error('NIN reverification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('dashboard')->with('error', 'NIN reverification failed due to a system error. Please try again later.');
        }
    }
}