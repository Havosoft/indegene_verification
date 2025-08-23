<x-guest-layout>
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-xl bg-white rounded-lg shadow-lg p-10">
        <div class="flex justify-center mb-8">
            <img src="{{ asset('img/taraba-state-logo-nedc.png') }}" alt="Taraba State Logo" style="width: 64px; height: 64px; border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.10); border: 2px solid #007a3d; background: #fff; object-fit: contain;" />
        </div>
        <h2 class="text-2xl font-bold text-center mb-8">Choose Service(s)</h2>
        @if ($errors->has('services'))
            <div class="mb-6 p-3 bg-red-100 border-l-4 border-red-500 text-red-800 rounded">
                {{ $errors->first('services') }}
            </div>
        @endif
        <form method="POST" action="{{ url('/register/service') }}">
            @csrf
            <div class="space-y-10">
                <div class="border rounded-lg p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="lga_certificate" name="services[]" value="lga_certificate" class="mr-4 h-6 w-6 text-green-700 border-gray-300 rounded focus:ring-green-700" @if(is_array(old('services')) && in_array('lga_certificate', old('services'))) checked @endif>
                        <label for="lga_certificate" class="font-semibold text-lg">Get LGA Certificate</label>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xl text-green-700 mb-2">₦10,000</div>
                        <div class="text-gray-600 text-base">Official certificate from your Local Government Area</div>
                    </div>
                </div>
                <div class="border rounded-lg p-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="lga_id" name="services[]" value="lga_id" class="mr-4 h-6 w-6 text-blue-700 border-gray-300 rounded focus:ring-blue-700" @if(is_array(old('services')) && in_array('lga_id', old('services'))) checked @endif>
                        <label for="lga_id" class="font-semibold text-lg">Get LGA ID</label>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xl text-blue-700 mb-2">₦5,000</div>
                        <div class="text-gray-600 text-base">Obtain your official LGA identification</div>
                    </div>
                </div>
            </div>
            <button type="submit" class="mt-10 w-full py-4 bg-green-700 text-white text-lg font-semibold rounded hover:bg-green-800 transition">Continue to Payment</button>
        </form>
    </div>
</div>
</x-guest-layout> 