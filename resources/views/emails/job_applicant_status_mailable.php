@extends('admin.layouts.email_template')
@section('content')
<table border="0" cellpadding="0" cellspacing="0" class="force-row" style="width: 100%;    border-bottom: solid 1px #ccc;">
    <tr>
        <td class="content-wrapper" style="padding-left:24px;padding-right:24px"><br>
            <div class="title" style="font-family: Helvetica, Arial, sans-serif; font-size: 18px;font-weight:400;color: #000;text-align: left;
                 padding-top: 20px;">Dear {{$user_name}} ,</div></td>
    </tr>
    <tr>
        <td class="cols-wrapper" style="padding-left:12px;padding-right:12px"><!--[if mso]>
         <table border="0" width="576" cellpadding="0" cellspacing="0" style="width: 576px;">
            <tr>
               <td width="192" style="width: 192px;" valign="top">
                  <![endif]-->      
            <table border="0" cellpadding="0" cellspacing="0" align="left" class="force-row" style="width: 100%;">
                <tr>
                    <td class="row" valign="top" style="padding-left:12px;padding-right:12px;padding-top:18px;padding-bottom:12px"><table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                            <tr>
                                <td class="subtitle" style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;font-weight:400;color:#333;padding-bottom:30px; text-align: left;">
                                    
                                    @if($status == 'Declined' || $status == 'rejected')
                                    {{-- Rejected Status --}}
                                    <div style="background: #fff3f3; border-left: 4px solid #e74c3c; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                        <h3 style="color: #e74c3c; margin: 0 0 10px 0; font-size: 18px;">
                                            <i class="fas fa-times-circle"></i> Application Status: Declined
                                        </h3>
                                    </div>
                                    <p>Dear {{$user_name}},</p>
                                    <p>Thank you for applying for the <strong>{{$job_title}}</strong> position at <strong>{{ $company_name }}</strong>.</p>
                                    <p>After careful review of your resume/CV, we regret to inform you that we will not be moving forward with your application at this time. This decision was made after thorough consideration of all applications we received.</p>
                                    <p>We appreciate your interest in joining our team and encourage you to apply for future opportunities that match your skills and experience.</p>
                                    <p>You can:</p>
                                    <ul style="margin: 15px 0; padding-left: 20px;">
                                        <li><a href="{{$job_link}}" style="color: #3498db;">View the job details</a></li>
                                        <li><a href="{{$company_link}}" style="color: #3498db;">Visit employer's profile for other opportunities</a></li>
                                    </ul>
                                    
                                    @elseif($status == 'Approved' || $status == 'hired')
                                    {{-- Hired/Approved Status --}}
                                    <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                        <h3 style="color: #22c55e; margin: 0 0 10px 0; font-size: 18px;">
                                            <i class="fas fa-check-circle"></i> Congratulations! You're Hired
                                        </h3>
                                    </div>
                                    <p>Dear {{$user_name}},</p>
                                    <p><strong>Congratulations!</strong> We are delighted to inform you that <strong>{{ $company_name }}</strong> has approved your application for the <strong>{{$job_title}}</strong> position.</p>
                                    <p>After reviewing your qualifications and experience, we believe you would be an excellent fit for our team. We are excited to move forward with your candidacy.</p>
                                    <p><strong>Next Steps:</strong></p>
                                    <ul style="margin: 15px 0; padding-left: 20px;">
                                        <li>The employer will contact you shortly with further details</li>
                                        <li>Please keep an eye on your email and phone for communication</li>
                                        <li>Prepare any necessary documents for the next stage</li>
                                    </ul>
                                    <p>You can:</p>
                                    <ul style="margin: 15px 0; padding-left: 20px;">
                                        <li><a href="{{$job_link}}" style="color: #3498db;">Review the job details</a></li>
                                        <li><a href="{{$company_link}}" style="color: #3498db;">Visit employer's profile</a></li>
                                    </ul>
                                    
                                    @elseif($status == 'Short List' || $status == 'shortlist')
                                    {{-- Shortlisted Status --}}
                                    <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                        <h3 style="color: #f59e0b; margin: 0 0 10px 0; font-size: 18px;">
                                            <i class="fas fa-star"></i> Great News! You've Been Shortlisted
                                        </h3>
                                    </div>
                                    <p>Dear {{$user_name}},</p>
                                    <p><strong>Excellent news!</strong> Your application for the <strong>{{$job_title}}</strong> position at <strong>{{ $company_name }}</strong> has been shortlisted.</p>
                                    <p>After reviewing the applications we received, we are impressed with your qualifications and would like to proceed to the next stage of our hiring process.</p>
                                    <p><strong>What This Means:</strong></p>
                                    <ul style="margin: 15px 0; padding-left: 20px;">
                                        <li>Your profile has been selected for further consideration</li>
                                        <li>The employer may contact you for an interview</li>
                                        <li>Please ensure your contact information is up to date</li>
                                        <li>Be prepared to discuss your qualifications and experience</li>
                                    </ul>
                                    <p>You can:</p>
                                    <ul style="margin: 15px 0; padding-left: 20px;">
                                        <li><a href="{{$job_link}}" style="color: #3498db;">Review the job details</a></li>
                                        <li><a href="{{$company_link}}" style="color: #3498db;">Visit employer's profile</a></li>
                                    </ul>
                                    
                                    @else
                                    {{-- Applied/Pending Status (Default) --}}
                                    <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                        <h3 style="color: #3b82f6; margin: 0 0 10px 0; font-size: 18px;">
                                            <i class="fas fa-info-circle"></i> Application Status Update
                                        </h3>
                                    </div>
                                    <p>Dear {{$user_name}},</p>
                                    <p>This is to confirm that your application for the <strong>{{$job_title}}</strong> position at <strong>{{ $company_name }}</strong> is being reviewed.</p>
                                    <p>Our team is carefully reviewing all applications, and we will update you on the status of your application soon.</p>
                                    <p><strong>Current Status:</strong> Under Review</p>
                                    <p>You can:</p>
                                    <ul style="margin: 15px 0; padding-left: 20px;">
                                        <li><a href="{{$job_link}}" style="color: #3498db;">View the job details</a></li>
                                        <li><a href="{{$company_link}}" style="color: #3498db;">Visit employer's profile</a></li>
                                    </ul>
@endif
                                   
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 22px;font-weight: 400;color: #333; padding-bottom: 30px;text-align: left;">
                                <p style="margin-top:10px; font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #333;">                               
                               
Warm regards, <br>
{{ $siteSetting->site_name }} Team 
</p>
                                </td>
                            </tr>
                        </table>
                        <br></td>
                </tr>
            </table>      
            <!--[if mso]>
               </td>
            </tr>
         </table>
         <![endif]--></td>
    </tr>
</table>
@endsection