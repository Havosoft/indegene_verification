<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Taraba State Indigene Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Add Paystack script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-carousel">
        <img src="{{ asset('img/bg/tarabe1.jpeg') }}" class="active" alt="Taraba 1">
        <img src="{{ asset('img/bg/taraba2.jpg') }}" alt="Taraba 2">
        <img src="{{ asset('img/bg/taraba3.jpg') }}" alt="Taraba 3">
    </div>
    <div class="bg-overlay"></div>
    <div class="w-full max-w-lg bg-white rounded-xl shadow-lg p-10 z-10 relative">
        <div class="flex flex-col items-center mb-8">
            <div class="bg-white border-4 border-green-700 rounded-full p-2 shadow mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('img/taraba-state-logo-nedc.png') }}" alt="Taraba State Logo" style="width: 50px; height: 50px; object-fit: contain;" />
            </div>
            <div class="text-lg font-bold text-center leading-tight text-green-900">Taraba State<br>Indigene Verification</div>
            <div class="text-gray-500 text-xs mt-1">Official Registration Portal</div>
        </div>
        <!-- Registration Form (multi-step and modal logic as before) -->
        <div x-data="registrationModal()" x-init="initValidation()">
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="text-red-800 font-semibold mb-2">Registration Errors:</div>
                <ul class="text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form method="POST" action="{{ route('register') }}" id="multiStepForm" @submit.prevent="openModal">
        @csrf
                <div id="step-1" class="step">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" x-model="step1.name" required autofocus autocomplete="name" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" />
                        <template x-if="step1.touched.name && !step1.valid.name">
                            <div class="text-red-600 text-xs mt-1">Name is required and must contain only letters and spaces.</div>
                        </template>
        </div>
        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" x-model="step1.email" required autocomplete="username" />
                        <template x-if="step1.touched.email && !step1.valid.email">
                            <div class="text-red-600 text-xs mt-1">Enter a valid email address.</div>
                        </template>
                        <div id="email-exists-error" class="text-red-600 text-xs mt-1 hidden">This email is already registered.</div>
        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" x-model="step1.password" required autocomplete="new-password" />
                        <template x-if="step1.touched.password && !step1.valid.password">
                            <div class="text-red-600 text-xs mt-1">
                                Password must be at least 8 characters, contain a number, and an uppercase letter.
                            </div>
                        </template>
        </div>
        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" x-model="step1.password_confirmation" required autocomplete="new-password" />
                        <template x-if="step1.touched.password_confirmation && !step1.valid.password_confirmation">
                            <div class="text-red-600 text-xs mt-1">Passwords do not match.</div>
                        </template>
                    </div>
                    <div class="flex items-center justify-end mt-6">
                        <button type="button" id="next-1" class="btn btn-primary" :disabled="!step1.isValid" :class="{'opacity-50 cursor-not-allowed': !step1.isValid}">Next</button>
                    </div>
                </div>
                <div id="step-2" class="step hidden">
                    <!-- Phone Number -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" x-model="step2.phone" required autocomplete="tel" inputmode="tel" pattern="[+0-9\s]+" title="Enter a valid phone number, e.g. +2348012345678 or 08012345678" />
                        <template x-if="step2.touched.phone && !step2.valid.phone">
                            <div class="text-red-600 text-xs mt-1">Enter a valid phone number (e.g. +2348012345678 or 08012345678).</div>
                        </template>
                        <div id="phone-exists-error" class="text-red-600 text-xs mt-1 hidden">This phone number is already registered.</div>
                    </div>
                    <!-- Date of Birth -->
                    <div class="mt-4">
                        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                        <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth')" required />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>
                    <!-- Gender -->
                    <div class="mt-4">
                        <x-input-label for="gender" :value="__('Gender')" />
                        <select id="gender" name="gender" class="block mt-1 w-full" required>
                            <option value="">Select Gender</option>
                            <option value="male" @if(old('gender')=='male') selected @endif>Male</option>
                            <option value="female" @if(old('gender')=='female') selected @endif>Female</option>
                            <option value="other" @if(old('gender')=='other') selected @endif>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                    <div class="flex items-center justify-between mt-6">
                        <button type="button" id="back-2" class="btn btn-secondary">Back</button>
                        <button type="button" id="next-2" class="btn btn-primary">Next</button>
                    </div>
                </div>
                <div id="step-3" class="step hidden">
                    <!-- State of Origin -->
                    <div>
                        <x-input-label for="state_of_origin" :value="__('State of Origin')" />
                        <x-text-input id="state_of_origin" class="block mt-1 w-full" type="text" name="state_of_origin" :value="old('state_of_origin', 'Taraba')" required pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" />
                        <x-input-error :messages="$errors->get('state_of_origin')" class="mt-2" />
                    </div>
                    <!-- Local Government Area (LGA) -->
                    <div class="mt-4">
                        <x-input-label for="lga" :value="__('Local Government Area (LGA)')" />
                        <select id="lga" name="lga" class="block mt-1 w-full" required>
                            <option value="">Select LGA</option>
                            <option value="Ardo Kola" @if(old('lga')=='Ardo Kola') selected @endif>Ardo Kola</option>
                            <option value="Bali" @if(old('lga')=='Bali') selected @endif>Bali</option>
                            <option value="Donga" @if(old('lga')=='Donga') selected @endif>Donga</option>
                            <option value="Gashaka" @if(old('lga')=='Gashaka') selected @endif>Gashaka</option>
                            <option value="Gassol" @if(old('lga')=='Gassol') selected @endif>Gassol</option>
                            <option value="Ibi" @if(old('lga')=='Ibi') selected @endif>Ibi</option>
                            <option value="Jalingo" @if(old('lga')=='Jalingo') selected @endif>Jalingo</option>
                            <option value="Karim Lamido" @if(old('lga')=='Karim Lamido') selected @endif>Karim Lamido</option>
                            <option value="Kurmi" @if(old('lga')=='Kurmi') selected @endif>Kurmi</option>
                            <option value="Lau" @if(old('lga')=='Lau') selected @endif>Lau</option>
                            <option value="Sardauna" @if(old('lga')=='Sardauna') selected @endif>Sardauna</option>
                            <option value="Takum" @if(old('lga')=='Takum') selected @endif>Takum</option>
                            <option value="Ussa" @if(old('lga')=='Ussa') selected @endif>Ussa</option>
                            <option value="Wukari" @if(old('lga')=='Wukari') selected @endif>Wukari</option>
                            <option value="Yorro" @if(old('lga')=='Yorro') selected @endif>Yorro</option>
                            <option value="Zing" @if(old('lga')=='Zing') selected @endif>Zing</option>
                        </select>
                        <x-input-error :messages="$errors->get('lga')" class="mt-2" />
                    </div>
                    <!-- Address -->
                    <div class="mt-4">
                        <x-input-label for="address" :value="__('Residential Address')" />
                        <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                    <!-- Place of Birth -->
                    <div class="mt-4">
                        <x-input-label for="place_of_birth" :value="__('Place of Birth')" />
                        <x-text-input id="place_of_birth" class="block mt-1 w-full" type="text" name="place_of_birth" :value="old('place_of_birth')" required pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" />
                        <x-input-error :messages="$errors->get('place_of_birth')" class="mt-2" />
                    </div>
                    <!-- NIN (required) -->
                    <div class="mt-4">
                        <x-input-label for="nin" :value="__('National Identification Number (NIN)')" />
                        <x-text-input id="nin" class="block mt-1 w-full" type="number" name="nin" :value="old('nin')" required inputmode="numeric" pattern="[0-9]{8,20}" min="10000000" max="99999999999999999999" title="Only numbers allowed, 8-20 digits" />
                        <x-input-error :messages="$errors->get('nin')" class="mt-2" />
                        <div id="nin-exists-error" class="text-red-600 text-xs mt-1 hidden">This NIN is already registered.</div>
                    </div>
                    <div class="flex items-center justify-between mt-6">
                        <button type="button" id="back-3" class="btn btn-secondary">Back</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </div>
                @if(\App\Models\PaymentSetting::isPaymentBeforeDataSave())
                <input type="hidden" name="selected_services" x-model="selectedServicesString">
                <input type="hidden" name="payment_ref" x-model="paymentRef">
                @endif
            </form>
            <!-- Modal -->
            @if(\App\Models\PaymentSetting::isPaymentBeforeDataSave())
            <div x-show="showModal" style="background: rgba(0,0,0,0.4)" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
                <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative">
                    <button @click="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">&times;</button>
                    <h2 class="text-xl font-bold mb-4 text-center">Select Service(s) to Pay For</h2>
                    <div class="space-y-4 mb-6">
                        @foreach(($requiredServices ?? []) as $service)
                        <label class="flex items-center gap-3">
                            <input type="checkbox" value="{{ $service->name }}" x-model="selectedServices" class="h-5 w-5 text-green-700 border-gray-300 rounded focus:ring-green-700">
                            <span class="font-semibold">{{ $service->name }}</span>
                            <span class="ml-auto text-green-700 font-bold">₦{{ number_format($service->price) }}</span>
                        </label>
                        @endforeach
                        @if(empty($requiredServices) || count($requiredServices) === 0)
                        <div class="text-red-600 text-sm">No required services set by admin. Please contact support.</div>
                        @endif
                    </div>
                    <div class="mb-4 text-lg font-bold text-green-900 text-center">
                        Total: ₦<span x-text="total"></span>
                    </div>
                    <button
                        type="button"
                        class="w-full py-3 bg-green-700 text-white font-semibold rounded hover:bg-green-800 transition"
                        :disabled="selectedServices.length === 0"
                        @click="
                            if (selectedServices.length === 0) return;
                            let amount = total * 100; // Paystack expects amount in kobo
                            let email = document.querySelector('input[name=email]')?.value || 'guest@example.com';
                            let name = document.querySelector('input[name=name]')?.value || 'Guest User';
                            let phone = document.querySelector('input[name=phone]')?.value || '';
                            let password = document.querySelector('input[name=password]')?.value || '';
                            let date_of_birth = document.querySelector('input[name=date_of_birth]')?.value || '';
                            let gender = document.querySelector('select[name=gender]')?.value || '';
                            let state_of_origin = document.querySelector('input[name=state_of_origin]')?.value || '';
                            let lga = document.querySelector('select[name=lga]')?.value || '';
                            let address = document.querySelector('input[name=address]')?.value || '';
                            let place_of_birth = document.querySelector('input[name=place_of_birth]')?.value || '';
                            let nin = document.querySelector('input[name=nin]')?.value || '';
                            let services = selectedServices;
                            let metadata = {
                                registration_data: {
                                    name, email, password, phone, date_of_birth, gender, state_of_origin, lga, address, place_of_birth, nin
                                },
                                registration_service: services
                            };
                            let handler = PaystackPop.setup({
                                key: 'pk_test_570c989491a2cb90d7889e119d8da889fb01d568',
                                email: email,
                                amount: amount,
                                currency: 'NGN',
                                ref: 'TARABA_' + Date.now(),
                                metadata: metadata,
                                callback: function(response) {
                                    // Redirect to backend for verification
                                    window.location.href = '/payment/paystack/callback?reference=' + response.reference;
                                },
                                onClose: function() {
                                    // Optionally handle modal close
                                }
                            });
                            handler.openIframe();
                        "
                    >
                        Proceed to Payment
                    </button>
                </div>
            </div>
            @endif
        </div>
        <!-- After registration form or in the appropriate place, add the payment button -->
        <a
            x-show="selectedServices.length > 0"
            :href="'{{ route('register.payment') }}?services=' + encodeURIComponent(selectedServicesString)"
            class="w-full py-4 mt-6 bg-green-800 text-white text-lg font-bold rounded-lg shadow hover:bg-green-900 transition block text-center"
            style="display: none;"
        >
            Proceed to Payment
        </a>
        <div class="mt-6 text-center">
            <span class="text-gray-600 text-sm">Already have an account?</span>
            <a href="{{ route('login') }}" class="ml-2 text-green-800 font-semibold underline hover:text-green-900">Log in</a>
        </div>
    </div>
    <style>
        .bg-carousel {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100vw; height: 100vh;
            z-index: 0;
        }
        .bg-carousel img {
            position: absolute;
            top: 0; left: 0; width: 100vw; height: 100vh;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1.5s ease;
        }
        .bg-carousel img.active {
            opacity: 1;
            z-index: 1;
        }
        .bg-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100vw; height: 100vh;
            background: rgba(255,255,255,0.65);
            z-index: 1;
        }
    </style>
    <script>
    function registrationModal() {
        return {
            showModal: false,
            selectedServices: [],
            paymentRef: '',
            get total() {
                let total = 0;
                // Dynamically sum prices from requiredServices
                const priceMap = {};
                @foreach(($requiredServices ?? []) as $service)
                priceMap["{{ $service->name }}"] = {{ $service->price }};
                @endforeach
                this.selectedServices.forEach(s => { if (priceMap[s]) total += priceMap[s]; });
                return total;
            },
            get selectedServicesString() {
                return this.selectedServices.join(',');
            },
            openModal(e) {
                @if(\App\Models\PaymentSetting::isPaymentBeforeDataSave())
                // If payment is required before data save, show the service selection modal
                this.showModal = true;
                @else
                // If payment is not required before data save, submit the form directly
                console.log('Payment setting is OFF - submitting form directly');
                document.getElementById('multiStepForm').submit();
                @endif
            },
            closeModal() {
                this.showModal = false;
            },
            step1: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                touched: { name: false, email: false, password: false, password_confirmation: false },
                valid: { name: false, email: false, password: false, password_confirmation: false },
                get isValid() {
                    return this.valid.name && this.valid.email && this.valid.password && this.valid.password_confirmation;
                }
            },
            step2: {
                phone: '',
                touched: { phone: false },
                valid: { phone: false },
                get isValid() {
                    return this.valid.phone;
                }
            },
            initValidation() {
                const self = this;
                // Name validation
                this.$watch('step1.name', value => {
                    self.step1.touched.name = true;
                    self.step1.valid.name = /^[A-Za-z\s]+$/.test(value) && value.trim().length > 0;
                });
                // Email validation
                this.$watch('step1.email', value => {
                    self.step1.touched.email = true;
                    self.step1.valid.email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                });
                // Password validation
                this.$watch('step1.password', value => {
                    self.step1.touched.password = true;
                    self.step1.valid.password = value.length >= 8 && /[A-Z]/.test(value) && /[0-9]/.test(value);
                    // Also revalidate confirmation
                    self.step1.valid.password_confirmation = value === self.step1.password_confirmation && value.length > 0;
                });
                // Confirm password validation
                this.$watch('step1.password_confirmation', value => {
                    self.step1.touched.password_confirmation = true;
                    self.step1.valid.password_confirmation = value === self.step1.password && value.length > 0;
                });
                // Phone validation (step 2)
                this.$watch('step2.phone', value => {
                    self.step2.touched.phone = true;
                    self.step2.valid.phone = /^\+?[0-9\s]{7,20}$/.test(value.trim());
                });
            }
            // Removed pay() and FlutterwaveCheckout logic
        }
    }
    </script>
    <script>
        const images = document.querySelectorAll('.bg-carousel img');
        let current = 0;
        setInterval(() => {
            images[current].classList.remove('active');
            current = (current + 1) % images.length;
            images[current].classList.add('active');
        }, 5000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const steps = [
                document.getElementById('step-1'),
                document.getElementById('step-2'),
                document.getElementById('step-3')
            ];
            let currentStep = 0;
            function showStep(idx) {
                steps.forEach((step, i) => {
                    if (step) step.classList.toggle('hidden', i !== idx);
                });
            }
            const next1 = document.getElementById('next-1');
            const next2 = document.getElementById('next-2');
            const back2 = document.getElementById('back-2');
            const back3 = document.getElementById('back-3');
            if (next1) next1.onclick = () => { showStep(1); currentStep = 1; };
            if (next2) next2.onclick = () => { showStep(2); currentStep = 2; };
            if (back2) back2.onclick = () => { showStep(0); currentStep = 0; };
            if (back3) back3.onclick = () => { showStep(1); currentStep = 1; };
            showStep(0);

            const emailInput = document.getElementById('email');
            const next1Button = document.getElementById('next-1');
            const emailExistsError = document.getElementById('email-exists-error');
            let emailCheckTimeout = null;
            let emailExists = false;
            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    clearTimeout(emailCheckTimeout);
                    emailExistsError.classList.add('hidden');
                    emailExists = false;
                    next1Button.disabled = true;
                    next1Button.classList.add('opacity-50', 'cursor-not-allowed');
                    const email = emailInput.value.trim();
                    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                        return;
                    }
                    emailCheckTimeout = setTimeout(function() {
                        fetch(`/register/check-email?email=${encodeURIComponent(email)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.exists) {
                                    emailExists = true;
                                    emailExistsError.classList.remove('hidden');
                                    next1Button.disabled = true;
                                    next1Button.classList.add('opacity-50', 'cursor-not-allowed');
                                } else {
                                    emailExists = false;
                                    emailExistsError.classList.add('hidden');
                                    // Only enable if all other validations pass
                                    if (window.Alpine && Alpine.store) {
                                        // If using Alpine store, check step1.isValid
                                        if (Alpine.store('registrationModal')?.step1?.isValid) {
                                            next1Button.disabled = false;
                                            next1Button.classList.remove('opacity-50', 'cursor-not-allowed');
                                        }
                                    } else {
                                        next1Button.disabled = false;
                                        next1Button.classList.remove('opacity-50', 'cursor-not-allowed');
                                    }
                                }
                            });
                    }, 500);
                });
            }

            const phoneInput = document.getElementById('phone');
            const next2Button = document.getElementById('next-2');
            const phoneExistsError = document.getElementById('phone-exists-error');
            let phoneCheckTimeout = null;
            let phoneExists = false;
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    clearTimeout(phoneCheckTimeout);
                    phoneExistsError.classList.add('hidden');
                    phoneExists = false;
                    next2Button.disabled = true;
                    next2Button.classList.add('opacity-50', 'cursor-not-allowed');
                    const phone = phoneInput.value.trim();
                    if (!phone || !/^\+?[0-9\s]{7,20}$/.test(phone)) {
                        return;
                    }
                    phoneCheckTimeout = setTimeout(function() {
                        fetch(`/register/check-phone?phone=${encodeURIComponent(phone)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.exists) {
                                    phoneExists = true;
                                    phoneExistsError.classList.remove('hidden');
                                    next2Button.disabled = true;
                                    next2Button.classList.add('opacity-50', 'cursor-not-allowed');
                                } else {
                                    phoneExists = false;
                                    phoneExistsError.classList.add('hidden');
                                    next2Button.disabled = false;
                                    next2Button.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                            });
                    }, 500);
                });
            }

            // NIN check
            const ninInput = document.getElementById('nin');
            const registerButton = document.querySelector('button[type="submit"]');
            const ninExistsError = document.getElementById('nin-exists-error');
            let ninCheckTimeout = null;
            let ninExists = false;
            if (ninInput) {
                ninInput.addEventListener('input', function() {
                    clearTimeout(ninCheckTimeout);
                    ninExistsError.classList.add('hidden');
                    ninExists = false;
                    registerButton.disabled = true;
                    registerButton.classList.add('opacity-50', 'cursor-not-allowed');
                    const nin = ninInput.value.trim();
                    if (!nin || nin.length < 8) {
                        return;
                    }
                    ninCheckTimeout = setTimeout(function() {
                        fetch(`/register/check-nin?nin=${encodeURIComponent(nin)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.exists) {
                                    ninExists = true;
                                    ninExistsError.classList.remove('hidden');
                                    registerButton.disabled = true;
                                    registerButton.classList.add('opacity-50', 'cursor-not-allowed');
                                } else {
                                    ninExists = false;
                                    ninExistsError.classList.add('hidden');
                                    registerButton.disabled = false;
                                    registerButton.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                            });
                    }, 500);
                });
            }
        });
    </script>
</body>
</html>
