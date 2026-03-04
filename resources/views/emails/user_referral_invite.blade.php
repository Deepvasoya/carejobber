@extends('admin.layouts.email_template')
@section('content')
<table border="0" cellpadding="0" cellspacing="0" class="force-row" style="width: 100%; border-bottom: solid 1px #ccc;">
    <tr>
        <td class="content-wrapper" style="padding-left:24px;padding-right:24px"><br>
            <div class="title" style="font-family: Helvetica, Arial, sans-serif; font-size: 18px;font-weight:600;color: #000;text-align: left; padding-top: 20px;">
                {{__('You Have Been Invited!')}}
            </div>
        </td>
    </tr>
    <tr>
        <td class="cols-wrapper" style="padding-left:12px;padding-right:12px">
            <table border="0" cellpadding="0" cellspacing="0" align="left" class="force-row" style="width: 100%;">
                <tr>
                    <td class="row" valign="top" style="padding-left:12px;padding-right:12px;padding-top:18px;padding-bottom:12px">
                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                            <tr>
                                <td class="subtitle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;font-weight:400;color:#333;padding-bottom:20px; text-align: left;">
                                    <p style="margin: 0 0 20px 0;">
                                        {{__('Hello!')}}<br><br>
                                        <strong>{{ $referrerUser->getName() }}</strong> {{__('has invited you to join')}} {{ $siteSetting->site_name }}.
                                    </p>
                                    
                                    <p style="margin: 0 0 20px 0;">
                                        {{__('Join our platform to find your dream job and connect with top employers.')}}
                                    </p>
                                    
                                    <div style="background-color: #f8f9fa; border-left: 4px solid #11998e; padding: 20px; margin: 20px 0; border-radius: 4px;">
                                        <p style="margin: 0 0 15px 0; font-weight: 600; color: #333;">
                                            {{__('Benefits of joining:')}}
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #555;">
                                            <li style="margin-bottom: 8px;">{{__('Access thousands of job opportunities')}}</li>
                                            <li style="margin-bottom: 8px;">{{__('Build your professional resume')}}</li>
                                            <li style="margin-bottom: 8px;">{{__('Get noticed by top employers')}}</li>
                                            <li style="margin-bottom: 0;">{{__('Receive job alerts matching your skills')}}</li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 30px; text-align: center;">
                                    <a href="{{ $referralLink }}" style="display: inline-block; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 6px; font-size: 16px; font-weight: 600; text-align: center;">
                                        {{__('Register Now')}}
                                    </a>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 22px;font-weight: 400;color: #333; padding-bottom: 30px;text-align: left;">
                                    <p style="margin: 0 0 10px 0; color: #666; font-size: 13px;">
                                        {{__('Or copy and paste this link in your browser:')}}
                                    </p>
                                    <p style="margin: 0 0 20px 0; word-break: break-all; color: #11998e;">
                                        {{ $referralLink }}
                                    </p>
                                    
                                    <p style="margin-top:20px; font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #333;">                               
                                        {{__('Warm regards')}}, <br>
                                        {{ $siteSetting->site_name }} {{__('Team')}}
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <br>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
