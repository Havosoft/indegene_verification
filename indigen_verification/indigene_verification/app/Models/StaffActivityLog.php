<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'user_id', 'payment_id', 'action', 'details'
    ];

    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function payment() {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
} 