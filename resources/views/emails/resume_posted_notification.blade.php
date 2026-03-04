@extends('admin.layouts.email_template')
@section('content')
<table border="0" cellpadding="0" cellspacing="0" class="force-row" style="width: 100%; border-bottom: solid 1px #ccc;">
    <tr>
        <td class="content-wrapper" style="padding-left:24px;padding-right:24px"><br>
            <div class="title" style="font-family: Helvetica, Arial, sans-serif; font-size: 18px;font-weight:600;color: #000;text-align: left; padding-top: 20px;">
                {{__('New Resume Available')}}
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
                                        {{__('Hello')}} <strong>{{ $company->name }}</strong>,<br><br>
                                        {{__('A new candidate has posted/updated their resume in your industry on')}} {{ $siteSetting->site_name }}.
                                    </p>
                                    
                                    <div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 4px;">
                                        <p style="margin: 0 0 15px 0; font-weight: 600; color: #333;">
                                            {{__('Candidate Information:')}}
                                        </p>
                                        <ul style="margin: 0; padding-left: 20px; color: #555;">
                                            <li style="margin-bottom: 8px;"><strong>{{__('Name')}}:</strong> {{ $user->getName() }}</li>
                                            @if($user->getFunctionalArea('functional_area'))
                                            <li style="margin-bottom: 8px;"><strong>{{__('Functional Area')}}:</strong> {{ $user->getFunctionalArea('functional_area') }}</li>
                                            @endif
                                            @if($user->getCareerLevel('career_level'))
                                            <li style="margin-bottom: 8px;"><strong>{{__('Career Level')}}:</strong> {{ $user->getCareerLevel('career_level') }}</li>
                                            @endif
                                            @if($user->getLocation())
                                            <li style="margin-bottom: 8px;"><strong>{{__('Location')}}:</strong> {{ $user->getLocation() }}</li>
                                            @endif
                                            @if((bool)$user->is_immediate_available)
                                            <li style="margin-bottom: 0;"><strong>{{__('Status')}}:</strong> <span style="color: #28a745;">{{__('Ready for Hire')}}</span></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding-top: 10px; padding-bottom: 30px; text-align: center;">
                                    <a href="{{ $profileUrl }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 6px; font-size: 16px; font-weight: 600; text-align: center;">
                                        {{__('View Resume')}}
                                    </a>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 22px;font-weight: 400;color: #333; padding-bottom: 30px;text-align: left;">
                                    <p style="margin: 0 0 10px 0; color: #666; font-size: 13px;">
                                        {{__('Or copy and paste this link in your browser:')}}
                                    </p>
                                    <p style="margin: 0 0 20px 0; word-break: break-all; color: #667eea;">
                                        {{ $profileUrl }}
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
