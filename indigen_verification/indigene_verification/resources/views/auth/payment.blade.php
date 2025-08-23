<x-guest-layout>
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-12 border border-gray-200">
        <div class="flex flex-col items-center mb-10">
            <div class="bg-white border-4 border-green-700 rounded-full p-2 shadow" style="width: 90px; height: 90px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('img/taraba-state-logo-nedc.png') }}" alt="Taraba State Logo" style="width: 70px; height: 70px; object-fit: contain;" />
            </div>
            <h2 class="text-2xl font-bold text-center mt-6 text-green-900 tracking-wide">Taraba State Indigene Verification</h2>
            <div class="text-gray-500 text-sm mt-1">Official Government Payment Portal</div>
        </div>
        <div class="mb-10">
            <div class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Selected Service(s)</div>
            <table class="w-full text-left border-collapse mb-6">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 uppercase text-xs">
                        <th class="py-3 px-4">Service</th>
                        <th class="py-3 px-4 text-right">Amount (₦)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($service['services'] as $srv)
                        <tr class="border-b last:border-b-0">
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $srv['label'] }}</td>
                            <td class="py-3 px-4 text-right text-gray-700">{{ number_format($srv['price']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex items-center justify-between px-4 py-4 bg-green-50 rounded-lg border border-green-200 text-green-900 text-xl font-bold shadow-sm">
                <span>Total to Pay:</span>
                <span>₦{{ number_format($service['total']) }}</span>
            </div>
        </div>
        <form id="paymentForm" method="POST" action="#">
            @csrf
            <button type="button" id="paystackBtn" class="w-full py-4 bg-green-800 text-white text-lg font-bold rounded-lg shadow hover:bg-green-900 transition">Proceed to Paystack Payment</button>
        </form>
        <div class="mt-8 text-xs text-gray-500 text-center">This is a test payment. Use Flutterwave test card details.<br>For support, contact <a href="mailto:support@taraba.gov.ng" class="text-green-800 underline">support@taraba.gov.ng</a></div>
    </div>
</div>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
    document.getElementById('paystackBtn').onclick = function(e) {
        e.preventDefault();
        // Get user info
        @php
            $paystackUser = auth()->user()
                ? ['email' => auth()->user()->email, 'name' => auth()->user()->name, 'phone' => auth()->user()->phone ?? '']
                : ['email' => '', 'name' => '', 'phone' => ''];
        @endphp
        let user = @json($paystackUser);
        // Service info
        let services = @json(collect($service['services'])->pluck('name')->toArray() ?? collect($service['services'])->pluck('label')->toArray());
        let amount = {{ $service['total'] }} * 100; // Paystack expects kobo
        let metadata = {
            registration_service: services,
            registration_data: user
        };
        let handler = PaystackPop.setup({
            key: '{{ env('PAYSTACK_PUBLIC_KEY') }}',
            email: user.email || '',
            amount: amount,
            currency: 'NGN',
            ref: 'TARABA_' + Math.floor((Math.random() * 1000000000) + 1),
            metadata: metadata,
            callback: function(response) {
                @if(auth()->check())
                    window.location.href = '/user/payment/paystack/callback?reference=' + response.reference;
                @else
                    window.location.href = '/payment/paystack/callback?reference=' + response.reference;
                @endif
            },
            onClose: function() {
                alert('Payment window closed.');
            }
        });
        handler.openIframe();
    };
</script>
</x-guest-layout> 