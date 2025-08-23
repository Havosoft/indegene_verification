<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $serviceNames;

    public function __construct($user, $serviceNames)
    {
        $this->user = $user;
        $this->serviceNames = $serviceNames;
    }

    public function build()
    {
        return $this->subject('Payment Received - Awaiting Approval')
            ->view('emails.payment-received');
    }
} 