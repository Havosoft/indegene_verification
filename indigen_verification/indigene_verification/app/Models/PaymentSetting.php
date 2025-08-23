<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'key',
        'pay_before_data_save'
    ];

    protected $casts = [
        'pay_before_data_save' => 'boolean'
    ];

    /**
     * Get the payment setting value
     */
    public static function getPaymentSetting($key = 'registration_payment')
    {
        return static::where('key', $key)->first();
    }

    /**
     * Check if payment is required before data save
     */
    public static function isPaymentBeforeDataSave($key = 'registration_payment')
    {
        $setting = static::getPaymentSetting($key);
        return $setting ? $setting->pay_before_data_save : true;
    }
}
