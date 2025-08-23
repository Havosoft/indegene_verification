<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verification Failed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <!-- Error Icon -->
                        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                            <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                            Verification Failed
                        </h3>

                        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                            <p class="text-red-800 text-lg leading-relaxed">
                                {{ session('failure_reason', 'Your verification could not be completed at this time.') }}
                            </p>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3">What happens next?</h4>
                            <ul class="text-left text-gray-700 space-y-2">
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">•</span>
                                    Your payment has been received and recorded
                                </li>
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">•</span>
                                    You cannot access the dashboard until verification is resolved
                                </li>
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">•</span>
                                    If you believe this is an error, please contact our support team
                                </li>
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">•</span>
                                    Refunds may be processed for eligible cases
                                </li>
                            </ul>
                        </div>

                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h5 class="font-semibold text-blue-900 mb-2">Contact Support</h5>
                                <p class="text-blue-800 text-sm">
                                    Email: support@tarabaverification.gov.ng<br>
                                    Phone: +234 (0) 123 456 7890<br>
                                    Office Hours: Monday - Friday, 8:00 AM - 5:00 PM
                                </p>
                            </div>

                            <div class="flex justify-center space-x-4">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                        Logout
                                    </button>
                                </form>
                                
                                <a href="mailto:support@tarabaverification.gov.ng" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for showing verification failure (if accessed via AJAX) -->
    @if(session('nin_verification_failed'))
    <div id="verificationFailedModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Verification Failed</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        {{ session('failure_reason') }}
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModal" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('verificationFailedModal').style.display = 'none';
        });
    </script>
    @endif
</x-app-layout>