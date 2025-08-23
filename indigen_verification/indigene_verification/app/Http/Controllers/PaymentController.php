<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\NinVerificationService;

class PaymentController extends Controller
{
    // Handle Paystack payment callback and verification
    public function paystackCallback(Request $request)
    {
        $reference = $request->query('reference');
        if (!$reference) {
            return redirect()->route('register')->with('error', 'No payment reference provided.');
        }

        $secretKey = env('PAYSTACK_SECRET_KEY');
        $response = Http::withToken($secretKey)
            ->get('https://api.paystack.co/transaction/verify/' . $reference);

        // Debug logging
        $data = session('registration_data');
        $service = session('registration_service');
        Log::info('Paystack callback debug', [
            'metadata' => $response['data']['metadata'] ?? null,
            'registration_data' => $data ?? null,
            'registration_service' => $service ?? null,
        ]);
        Log::info('Paystack full response', $response->json());

        if ($response->successful() && isset($response['data']['status']) && $response['data']['status'] === 'success') {
            // Prefer session, but fallback to Paystack metadata
            if (!$data && isset($response['data']['metadata']['registration_data'])) {
                $data = $response['data']['metadata']['registration_data'];
            }
            if (!$service && isset($response['data']['metadata']['registration_service'])) {
                $service = [ 'services' => [] ];
                foreach ($response['data']['metadata']['registration_service'] as $srv) {
                    if ($srv === 'lga_certificate') {
                        $service['services'][] = [ 'label' => 'LGA Certificate', 'price' => 10000 ];
                    } else if ($srv === 'lga_id') {
                        $service['services'][] = [ 'label' => 'LGA ID', 'price' => 5000 ];
                    }
                }
            }
            // Duplicate check before creating user
            if ($data) {
                if (isset($data['email']) && User::where('email', $data['email'])->exists()) {
                    return redirect()->route('register')->withInput()->withErrors(['email' => 'This email is already registered.']);
                }
                if (isset($data['phone']) && User::where('phone', $data['phone'])->exists()) {
                    return redirect()->route('register')->withInput()->withErrors(['phone' => 'This phone number is already registered.']);
                }
                if (isset($data['nin']) && User::where('nin', $data['nin'])->exists()) {
                    return redirect()->route('register')->withInput()->withErrors(['nin' => 'This NIN is already registered.']);
                }
            }
            if ($data && $service) {
                // Verify NIN before creating user
                $ninVerificationService = new NinVerificationService();
                $ninVerification = $ninVerificationService->verifyNin($data['nin']);
                
                if (!$ninVerification['success']) {
                    return redirect()->route('register')->with('error', 'NIN verification failed: ' . $ninVerification['message']);
                }
                
                if (!$ninVerification['is_taraba_indigene']) {
                    $failureReason = $ninVerificationService->getVerificationFailureReason($ninVerification['data']);
                    return redirect()->route('register')->with('error', $failureReason);
                }
                
                // Create user with verified status if NIN verification passed
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'phone' => $data['phone'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'state_of_origin' => $data['state_of_origin'],
                    'lga' => $data['lga'],
                    'address' => $data['address'],
                    'place_of_birth' => $data['place_of_birth'],
                    'nin' => $data['nin'],
                    'temporary_id' => 'TMP-' . strtoupper(Str::random(8)),
                    'is_verified' => true, // Auto-verify user after NIN verification
                    'services_paid_for' => implode(',', $response['data']['metadata']['registration_service'] ?? []),
                    'nin_verification_status' => 'verified',
                    'nin_verification_data' => $ninVerification['data'],
                ]);
                foreach (($response['data']['metadata']['registration_service'] ?? []) as $serviceName) {
                    $serviceModel = \App\Models\Service::where('name', $serviceName)->first();
                    if ($serviceModel) {
                        Payment::create([
                            'user_id' => $user->id,
                            'service_name' => $serviceModel->name,
                            'price' => $serviceModel->price,
                            'payment_ref' => $response['data']['reference'],
                            'payer_name' => $data['name'],
                            'payer_email' => $data['email'],
                            'approved' => 'awaiting_review', // Keep payment awaiting admin review
                        ]);
                    }
                }
                // Create a permanent ID for the user
                $permanentId = 'TAR-' . strtoupper(substr($data['state_of_origin'], 0, 3)) . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                $user->update(['permanent_id' => $permanentId]);
                
                Auth::login($user);
                \Mail::to($user->email)->send(new \App\Mail\PaymentReceivedMail($user, array_map(fn($s) => is_array($s) ? ($s['label'] ?? $s['name'] ?? $s) : $s, $response['data']['metadata']['registration_service'] ?? [])));
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'payment_received',
                    'message' => 'Your payment has been received and your account has been verified! Your certificate will be available after admin approval.'
                ]);
                session()->forget('registration_data');
                session()->forget('registration_service');
                return redirect()->route('dashboard')->with('success', 'Payment and registration successful! Your account has been verified. Your certificate will be available after admin approval.');
            }
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }

        return redirect()->route('register')->with('error', 'Payment verification failed.');
    }
}