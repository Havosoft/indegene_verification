<div style="font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto;">
    <h2 style="color: #166534;">Your Certificate is Now Available</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Your certificate for the service <strong>{{ $payment->service_name }}</strong> has been approved and is now available for download.</p>
    <p>
        <a href="{{ url('/certificates') }}" style="display: inline-block; background: #166534; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Download Certificate</a>
    </p>
    <p>If you have any questions or need assistance, please contact support.</p>
    <p>Thank you,<br>Taraba State Indigene Verification Team</p>
</div> 