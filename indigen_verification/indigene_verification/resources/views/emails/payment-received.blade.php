<p>Hello {{ $user->name }},</p>
<p>We have received your payment for the following service(s):</p>
<ul>
    @foreach($serviceNames as $service)
        <li>{{ $service }}</li>
    @endforeach
</ul>
<p>Your request is being reviewed. You will be notified by email once your payment is approved and your certificate is available for download.</p>
<p>Best regards,<br>Taraba State Indigene Verification Team</p> 