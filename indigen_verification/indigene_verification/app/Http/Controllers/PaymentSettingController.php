<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    /**
     * Display payment settings
     */
    public function index()
    {
        $paymentSetting = PaymentSetting::getPaymentSetting('registration_payment');
        return view('admin.payment-settings', compact('paymentSetting'));
    }

    /**
     * Update payment setting
     */
    public function update(Request $request)
    {
        $request->validate([
            'pay_before_data_save' => 'required|boolean'
        ]);

        PaymentSetting::updateOrCreate(
            ['key' => 'registration_payment'],
            ['pay_before_data_save' => $request->boolean('pay_before_data_save')]
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment setting updated successfully']);
        }

        return redirect()->back()->with('success', 'Payment setting updated successfully');
    }

    /**
     * Toggle payment setting
     */
    public function toggle(Request $request)
    {
        $paymentSetting = PaymentSetting::getPaymentSetting('registration_payment');
        $newValue = !($paymentSetting ? $paymentSetting->pay_before_data_save : true);

        PaymentSetting::updateOrCreate(
            ['key' => 'registration_payment'],
            ['pay_before_data_save' => $newValue]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'pay_before_data_save' => $newValue,
                'message' => 'Payment setting toggled successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Payment setting toggled successfully');
    }
}
