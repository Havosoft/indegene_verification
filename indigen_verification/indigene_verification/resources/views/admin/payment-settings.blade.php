@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-green-900">Payment Settings</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Registration Payment Flow</h2>
            <p class="text-gray-600 mb-4">
                Control when users are required to make payment during the registration process.
            </p>
        </div>

        <div class="border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-md font-medium text-gray-800">Payment Before Data Save</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($paymentSetting && $paymentSetting->pay_before_data_save)
                            <span class="text-green-600">✓ Enabled:</span> Users must select and pay for services before their data is saved.
                        @else
                            <span class="text-blue-600">✓ Disabled:</span> Users can register first, then pay for services from their dashboard.
                        @endif
                    </p>
                </div>
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="paymentToggle" class="sr-only peer" 
                               {{ $paymentSetting && $paymentSetting->pay_before_data_save ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900">
                            {{ $paymentSetting && $paymentSetting->pay_before_data_save ? 'ON' : 'OFF' }}
                        </span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-800 mb-2">How it works:</h4>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-start">
                    <span class="font-medium mr-2">When ON:</span>
                    <span>Registration → Service Selection → Payment → Data Save → Dashboard</span>
                </div>
                <div class="flex items-start">
                    <span class="font-medium mr-2">When OFF:</span>
                    <span>Registration → Data Save → Dashboard → Service Selection → Payment</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('paymentToggle').addEventListener('change', async function() {
    const isChecked = this.checked;
    const statusSpan = this.parentElement.querySelector('span');
    
    try {
        const response = await fetch('/admin/payment-settings/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            statusSpan.textContent = data.pay_before_data_save ? 'ON' : 'OFF';
            
            // Show success message
            const message = data.pay_before_data_save ? 
                'Payment before data save is now enabled' : 
                'Payment before data save is now disabled';
            
            // You can add a toast notification here
            alert(message);
        } else {
            // Revert the toggle if there was an error
            this.checked = !isChecked;
            alert('Error updating payment setting. Please try again.');
        }
    } catch (error) {
        // Revert the toggle if there was an error
        this.checked = !isChecked;
        alert('Error updating payment setting. Please try again.');
    }
});
</script>
@endsection 