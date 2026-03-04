@component('mail::message')
# One last step!

Thank you for registering with us! We're excited to have you on board. To get started, please verify your email address by clicking the button below.

@component('mail::button', ['url' => route('email-verification.check', $user->verification_token) . '?email=' . urlencode($user->email) ])
Click here to verify your account
@endcomponent

<p style="margin-top:10px; font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #333;">                               
Warm regards, <br>
{{ $siteSetting->site_name }} Team 
</p>
@endcomponent
