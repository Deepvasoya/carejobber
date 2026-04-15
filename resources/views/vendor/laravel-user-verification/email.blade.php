@extends('admin.layouts.email_template')
@section('content')
@php
if(auth('company')->check()){
$link = route('company.email-verification.check', $user->verification_token);
}elseif(auth()->check()){
$link = route('email-verification.check', $user->verification_token);
}
@endphp
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef2f7; padding:40px 0; font-family: Arial, sans-serif;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table width="700" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">

                <!-- Card (Content Area) -->
                <tr>
                    <td style="background:#ffffff; border-radius:10px; padding:40px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">

                        <!-- Logo -->
                        <div style="text-align:center; margin-bottom:25px;">
                            <img src="https://yourdomain.com/logo.png" alt="Medojob" style="max-width:160px;">
                        </div>

                        <!-- Content -->
                        <p style="font-size:16px;">Hello <strong>{{ $user->name }}</strong>,</p>

                        <p style="font-size:16px; line-height:1.6;">
                            Thank you for registering with <strong>{{ $siteSetting->site_name }}</strong>. 
                            We're excited to have you on board.
                        </p>

                        <p style="font-size:16px; line-height:1.6;">
                            To complete your registration and activate your account, please verify your email address by clicking the button below.
                        </p>

                        <!-- Button -->
                        <div style="text-align:center; margin:35px 0;">
                            <a href="{{ $link . '?email=' . urlencode($user->email) }}" 
                               style="background: linear-gradient(135deg, #0b3d91, #0f9d58); color:#ffffff; padding:14px 32px; text-decoration:none; border-radius:6px; font-weight:bold; display:inline-block;">
                                Verify Your Account
                            </a>
                        </div>

                        <!-- Fallback -->
                        <p style="font-size:14px; color:#666;">
                            If the button above does not work, copy and paste this link into your browser:
                        </p>

                        <p style="font-size:14px; word-break:break-all; color:#0b3d91;">
                            {{ $link . '?email=' . urlencode($user->email) }}
                        </p>

                        <p style="margin-top:30px;">
                            Warm regards,<br>
                            <strong>{{ $siteSetting->site_name }} Team</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer (Separate Background) -->
                <tr>
                    <td style="background:#f7f9fc; padding:30px; border-radius:0 0 10px 10px; text-align:center;">

                        <p style="max-width:520px; margin:10px auto; font-size:13px; color:#666; line-height:1.6;">
                            This email has been sent to you as a registered user of <strong>Medojob.com</strong> and is part of our automated communication process.
                        </p>

                        <p style="max-width:520px; margin:10px auto; font-size:13px; color:#666; line-height:1.6;">
                            <strong>Disclaimer:</strong> Medojob connects healthcare professionals and employers. We do not guarantee job placements or hiring outcomes.
                        </p>

                        <p style="max-width:520px; margin:10px auto; font-size:13px; color:#666;">
                            Report suspicious activity: 
                            <a href="mailto:support@medojob.com" style="color:#0b3d91;">support@medojob.com</a>
                        </p>

                        <!-- Address -->
                        <p style="font-size:12px; color:#777; margin-top:15px;">
                            Medojob Inc.<br>
                            16004 - 54 Street NW, Edmonton, AB, Canada T5Y 0R1<br>
                            1 (888) 338 2332
                        </p>

                        <!-- Links -->
                        <p style="margin:15px 0;">
                            <a href="https://medojob.com" style="margin:0 6px; color:#0b3d91;">Home</a> |
                            <a href="https://medojob.com/search-jobs" style="margin:0 6px; color:#0b3d91;">Jobs</a> |
                            <a href="https://medojob.com/faq" style="margin:0 6px; color:#0b3d91;">FAQs</a> |
                            <a href="https://medojob.com/blog" style="margin:0 6px; color:#0b3d91;">Blog</a>
                        </p>

                        <!-- Social -->
                        <div style="margin:20px 0;">
                            <a href="#"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" width="20"></a>
                            <a href="#"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/twitter.svg" width="20"></a>
                            <a href="#"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/linkedin.svg" width="20"></a>
                            <a href="#"><img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/pinterest.svg" width="20"></a>
                        </div>

                        <p style="font-size:12px; color:#999;">
                            © 2026 Medojob Inc. All rights reserved.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
        <!--[if mso]>
               </td>
            </tr>
         </table>
         <![endif]--></td>
    </tr>
</table>
@endsection
