<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Opportunity</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">New Job Opportunity!</h1>
    </div>
    
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">Hello <strong>{{ $user->name }}</strong>,</p>
        
        <p style="font-size: 14px; margin-bottom: 20px;">We found a new job opportunity that matches your profile on {{ config('app.name') }}!</p>
        
        <div style="background-color: #fff; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 5px;">
            <h2 style="color: #667eea; margin-top: 0; font-size: 20px;">Job Details</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 120px;">Position:</td>
                    <td style="padding: 8px 0;">{{ $job->title }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Company:</td>
                    <td style="padding: 8px 0;">{{ $company->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Location:</td>
                    <td style="padding: 8px 0;">{{ $job->getCity('city') }}, {{ $job->getCountry('country') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Job Type:</td>
                    <td style="padding: 8px 0;">{{ $job->getJobType('job_type') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Salary:</td>
                    <td style="padding: 8px 0;">{{ $salaryRange }}</td>
                </tr>
            </table>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('job.detail', [$job->slug]) }}" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">View Job Details</a>
        </div>
        
        <p style="font-size: 12px; color: #666; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <em>This job matches your profile based on your job category and career level. If you're not interested in receiving these recommendations, you can update your job alert preferences in your account settings.</em>
        </p>
        
        <p style="font-size: 14px; margin-top: 20px;">
            Warm regards,<br>
            <strong>{{ config('app.name') }} Team</strong>
        </p>
    </div>
</body>
</html>
