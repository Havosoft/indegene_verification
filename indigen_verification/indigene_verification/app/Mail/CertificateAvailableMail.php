<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Payment;

class CertificateAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $payment;

    public function __construct(User $user, Payment $payment)
    {
        $this->user = $user;
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject('Your Certificate is Now Available for Download')
            ->view('emails.certificate-available');
    }
} 