<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $serviceName;

    public function __construct($user, $serviceName)
    {
        $this->user = $user;
        $this->serviceName = $serviceName;
    }

    public function build()
    {
        return $this->subject('Certificate Approved')
            ->view('emails.certificate-approved');
    }
} 