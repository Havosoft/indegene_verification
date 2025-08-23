<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;

class CertificateController extends Controller
{
    public function downloadCertificate($id)
    {
        $payment = Payment::findOrFail($id);
        // Optionally, check if the user owns this payment
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }
        if (!$payment->file || !Storage::exists($payment->file)) {
            abort(404, 'Certificate file not found.');
        }
        $extension = pathinfo($payment->file, PATHINFO_EXTENSION);
        $filename = str_replace(' ', '_', $payment->service_name) . '.' . $extension;
        return Storage::download($payment->file, $filename);
    }
} 