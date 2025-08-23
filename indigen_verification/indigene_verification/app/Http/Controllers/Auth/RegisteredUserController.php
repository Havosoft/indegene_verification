<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffWelcomeMail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $requiredServices = \App\Models\Service::where('is_active', true)->where('is_required', true)->get();
        return view('auth.register', compact('requiredServices'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Check payment setting first
        $paymentBeforeDataSave = \App\Models\PaymentSetting::isPaymentBeforeDataSave();
        
        // Define validation rules based on payment setting
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'regex:/^[+0-9\s]{7,20}$/'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'state_of_origin' => ['required', 'string', 'max:100'],
            'lga' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'nin' => ['required', 'digits_between:8,20'],
        ];
        
        // Only require selected_services and payment_ref if payment is before data save
        if ($paymentBeforeDataSave) {
            $validationRules['selected_services'] = ['required', 'string'];
            $validationRules['payment_ref'] = ['required', 'string'];
        }
        
        $validated = $request->validate($validationRules);

        // Check for duplicate email, phone, or NIN
        if (User::where('email', $request->email)->exists()) {
            return back()->withInput()->withErrors(['email' => 'This email is already registered.']);
        }
        if (User::where('phone', $request->phone)->exists()) {
            return back()->withInput()->withErrors(['phone' => 'This phone number is already registered.']);
        }
        if (User::where('nin', $request->nin)->exists()) {
            return back()->withInput()->withErrors(['nin' => 'This NIN is already registered.']);
        }

        // Check payment setting
        $paymentBeforeDataSave = \App\Models\PaymentSetting::isPaymentBeforeDataSave();

        if ($paymentBeforeDataSave) {
            // Store registration data in session for payment flow
            Session::put('registration_data', $validated);
            return redirect()->route('register.service');
        } else {
            // Create user immediately and redirect to dashboard
            try {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'state_of_origin' => $validated['state_of_origin'],
                    'lga' => $validated['lga'],
                    'address' => $validated['address'],
                    'place_of_birth' => $validated['place_of_birth'],
                    'nin' => $validated['nin'],
                    'temporary_id' => 'TMP-' . strtoupper(Str::random(8)),
                    'is_verified' => false,
                    'services_paid_for' => '',
                ]);

                // Assign user to a random staff member in their LGA
                User::assignUserToRandomStaff($user);
                
                \Mail::to($user->email)->send(new \App\Mail\RegistrationSuccessMail($user));
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'type' => 'registration',
                    'message' => 'Registration successful. Please await verification.'
                ]);
                event(new Registered($user));
                Auth::login($user);
                return redirect()->route('dashboard');
            } catch (\Exception $e) {
                // Log the error for debugging
                \Log::error('Registration error: ' . $e->getMessage());
                return back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
            }
        }
    }

    public function showServiceSelection(): View
    {
        if (!Session::has('registration_data')) {
            return redirect()->route('register');
        }
        return view('auth.service-selection');
    }

    public function selectService(Request $request): RedirectResponse
    {
        // Check if we have registration data in session (payment before data save flow)
        if (!Session::has('registration_data')) {
            return redirect()->route('register');
        }

        $registrationData = Session::get('registration_data');
        
        // Duplicate check before proceeding to payment
        if (User::where('email', $registrationData['email'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'This email is already registered.']);
        }
        if (User::where('phone', $registrationData['phone'])->exists()) {
            return back()->withInput()->withErrors(['phone' => 'This phone number is already registered.']);
        }
        if (User::where('nin', $registrationData['nin'])->exists()) {
            return back()->withInput()->withErrors(['nin' => 'This NIN is already registered.']);
        }

        $request->validate([
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['in:lga_certificate,lga_id'],
        ]);
        
        $services = $request->services;
        $serviceDetails = [
            'lga_certificate' => [
                'label' => 'Get LGA Certificate',
                'price' => 10000
            ],
            'lga_id' => [
                'label' => 'Get LGA ID',
                'price' => 5000
            ]
        ];
        $selected = [];
        $total = 0;
        foreach ($services as $service) {
            $selected[] = [
                'key' => $service,
                'label' => $serviceDetails[$service]['label'],
                'price' => $serviceDetails[$service]['price']
            ];
            $total += $serviceDetails[$service]['price'];
        }
        Session::put('registration_service', [
            'services' => $selected,
            'total' => $total
        ]);
        return redirect()->route('register.payment');
    }

    public function showPayment(Request $request): View
    {
        // If services are passed as a query parameter, parse and show payment for logged-in users
        if ($request->has('services') && auth()->check()) {
            $serviceNames = explode(',', $request->query('services'));
            $services = \App\Models\Service::whereIn('name', $serviceNames)->where('is_active', true)->get();
            $total = $services->sum('price');
            // Ensure each service has 'name', 'label', and 'price' keys for the frontend
            $serviceArr = $services->map(function($srv) {
                return [
                    'name' => $srv->name,
                    'label' => $srv->label ?? $srv->name,
                    'price' => $srv->price
                ];
            })->toArray();
            $service = [
                'services' => $serviceArr,
                'total' => $total
            ];
            return view('auth.payment', ['service' => $service, 'is_additional_payment' => true]);
        }
        // Fallback to registration flow
        if (!Session::has('registration_data') || !Session::has('registration_service')) {
            return view('auth.payment', ['service' => null, 'is_additional_payment' => false]);
        }
        $service = Session::get('registration_service');
        return view('auth.payment', ['service' => $service, 'is_additional_payment' => false]);
    }

    public function processPayment(Request $request): RedirectResponse
    {
        // Mock payment: always successful
        if (!Session::has('registration_data') || !Session::has('registration_service')) {
            return redirect()->route('register');
        }
        $data = Session::get('registration_data');
        $service = Session::get('registration_service');
        
        // Create user with services paid for
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
            'is_verified' => false,
            'services_paid_for' => implode(',', array_column($service['services'], 'key')),
        ]);

        // Assign user to a random staff member in their LGA
        User::assignUserToRandomStaff($user);
        
        \Mail::to($user->email)->send(new \App\Mail\RegistrationSuccessMail($user));
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'registration',
            'message' => 'Registration successful. Please await verification.'
        ]);
        
        event(new Registered($user));
        Auth::login($user);
        Session::forget('registration_data');
        Session::forget('registration_service');
        return redirect()->route('dashboard');
    }

    /**
     * Show the admin registration form.
     */
    public function createAdmin(): View
    {
        return view('auth.register_admin');
    }

    /**
     * Handle admin registration submission.
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'regex:/^[+0-9\s]{7,20}$/'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other'],
            'state_of_origin' => ['required', 'string', 'max:100'],
            'lga' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['required', 'string', 'max:100'],
            'nin' => ['required', 'digits_between:8,20'],
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'state_of_origin' => $validated['state_of_origin'],
            'lga' => $validated['lga'],
            'address' => $validated['address'],
            'place_of_birth' => $validated['place_of_birth'],
            'nin' => $validated['nin'],
            'temporary_id' => 'TMP-' . strtoupper(Str::random(8)),
            'role' => 'admin',
        ]);
        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('dashboard');
    }

    /**
     * Handle staff registration from the admin form.
     */
    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^[+0-9\\s]{7,20}$/'],
            'gender' => ['required', 'in:male,female,other'],
            'state_of_origin' => ['required', 'string', 'max:100'],
            'lga' => ['required', 'string', 'max:100'],
        ]);
        $plainPassword = Str::random(10);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'state_of_origin' => $validated['state_of_origin'],
            'lga' => $validated['lga'],
            'role' => 'staff',
            'temporary_id' => 'TMP-' . strtoupper(Str::random(8)),
        ]);
        Mail::to($user->email)->queue(new StaffWelcomeMail($user->email, $plainPassword));
        return redirect()->back()->with('success', 'Staff user created successfully!');
    }
}
