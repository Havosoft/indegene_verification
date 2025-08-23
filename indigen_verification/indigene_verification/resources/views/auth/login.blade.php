<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Taraba State Indigene Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
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
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-carousel">
        <img src="{{ asset('img/bg/tarabe1.jpeg') }}" class="active" alt="Taraba 1">
        <img src="{{ asset('img/bg/taraba2.jpg') }}" alt="Taraba 2">
        <img src="{{ asset('img/bg/taraba3.jpg') }}" alt="Taraba 3">
    </div>
    <div class="bg-overlay"></div>
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-10 z-10 relative">
        <div class="flex flex-col items-center mb-8">
            <div class="bg-white border-4 border-green-700 rounded-full p-2 shadow mb-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('img/taraba-state-logo-nedc.png') }}" alt="Taraba State Logo" style="width: 50px; height: 50px; object-fit: contain;" />
            </div>
            <div class="text-lg font-bold text-center leading-tight text-green-900">Taraba State<br>Indigene Verification</div>
            <div class="text-gray-500 text-xs mt-1">Official Login Portal</div>
        </div>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-700 shadow-sm focus:ring-green-700" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>
            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-green-700 hover:text-green-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                <button type="submit" class="px-6 py-3 bg-green-800 text-white font-bold rounded-lg shadow hover:bg-green-900 transition">Log in</button>
            </div>
        </form>
        <div class="mt-6 text-center">
            <span class="text-gray-600 text-sm">Don't have an account?</span>
            <a href="{{ route('register') }}" class="ml-2 text-green-800 font-semibold underline hover:text-green-900">Register</a>
        </div>
    </div>
    <script>
        const images = document.querySelectorAll('.bg-carousel img');
        let current = 0;
        setInterval(() => {
            images[current].classList.remove('active');
            current = (current + 1) % images.length;
            images[current].classList.add('active');
        }, 5000);
    </script>
</body>
</html>
