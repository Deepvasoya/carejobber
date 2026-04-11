<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Candidate Match</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">New Candidate Match!</h1>
    </div>
    
    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px;">
        <p style="font-size: 16px; margin-bottom: 20px;">Hello <strong>{{ $company->name }}</strong>,</p>
        
        <p style="font-size: 14px; margin-bottom: 20px;">Great news! A new candidate has registered on {{ config('app.name') }} whose profile matches your job posting.</p>
        
        <div style="background-color: #fff; border-left: 4px solid #11998e; padding: 20px; margin: 20px 0; border-radius: 5px;">
            <h2 style="color: #11998e; margin-top: 0; font-size: 18px;">Candidate Profile</h2>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 150px;">Name:</td>
                    <td style="padding: 8px 0;">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Functional Area:</td>
                    <td style="padding: 8px 0;">{{ $user->getFunctionalArea ? $user->getFunctionalArea->functional_area : 'Not specified' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Career Level:</td>
                    <td style="padding: 8px 0;">{{ $user->getCareerLevel ? $user->getCareerLevel->career_level : 'Not specified' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Location:</td>
                    <td style="padding: 8px 0;">{{ $userLocation }}</td>
                </tr>
            </table>
            
            <h3 style="color: #11998e; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">Matching Job</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 150px;">Position:</td>
                    <td style="padding: 8px 0;">{{ $job->title }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Posted:</td>
                    <td style="padding: 8px 0;">{{ $job->created_at->format('M d, Y') }}</td>
                </tr>
            </table>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('user.profile', $user->id) }}" style="display: inline-block; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; padding: 15px 40px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">View Candidate Profile</a>
        </div>
        
        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p style="margin: 0; font-size: 13px; color: #856404;">
                <strong>Note:</strong> Contact details are only available after the candidate applies to your job or you unlock their profile. This recommendation is based on matching functional area and career level.
            </p>
        </div>
        
        <p style="font-size: 14px; margin-top: 20px;">
            Warm regards,<br>
            <strong>{{ config('app.name') }} Team</strong>
        </p>
    </div>
</body>
</html>
