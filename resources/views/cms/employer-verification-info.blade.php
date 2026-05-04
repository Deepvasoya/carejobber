@extends('layouts.app')

@section('content')
@include('includes.header')

@include('includes.inner_page_title', ['page_title' => __('Employer Verification')])

<div class="listpgWraper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="content-section" style="background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    
                    <h2 style="color: #1a2332; margin-bottom: 20px;">{{ __('What is Employer Verification?') }}</h2>
                    
                    <p style="font-size: 16px; line-height: 1.8; color: #4a5568; margin-bottom: 30px;">
                        {{ __('Employer verification is a trust badge that shows healthcare professionals that your organization is legitimate and trustworthy. Verified employers receive significantly more applications and have access to our full resume database.') }}
                    </p>

                    <div class="verification-levels" style="margin: 40px 0;">
                        <h3 style="color: #1a2332; margin-bottom: 25px;">{{ __('Verification Status Levels') }}</h3>
                        
                        <div class="status-card" style="border-left: 4px solid #dc3545; background: #fff5f5; padding: 20px; margin-bottom: 20px; border-radius: 4px;">
                            <h4 style="color: #dc3545; margin-bottom: 10px;">
                                🔴 {{ __('Unverified') }}
                            </h4>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li>{{ __('Maximum 2 active job postings') }}</li>
                                <li>{{ __('No access to resume database') }}</li>
                                <li>{{ __('Lower application rates') }}</li>
                            </ul>
                        </div>

                        <div class="status-card" style="border-left: 4px solid #ffc107; background: #fffbf0; padding: 20px; margin-bottom: 20px; border-radius: 4px;">
                            <h4 style="color: #856404; margin-bottom: 10px;">
                                🟡 {{ __('Reviewed') }}
                            </h4>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li>{{ __('Maximum 3 active job postings') }}</li>
                                <li>{{ __('No access to resume database') }}</li>
                                <li>{{ __('Moderate application rates') }}</li>
                            </ul>
                            <p style="margin-top: 10px; font-style: italic; color: #856404;">
                                {{ __('Note: Private/Individual employers are automatically set to Reviewed status.') }}
                            </p>
                        </div>

                        <div class="status-card" style="border-left: 4px solid #28a745; background: #f0fff4; padding: 20px; margin-bottom: 20px; border-radius: 4px;">
                            <h4 style="color: #28a745; margin-bottom: 10px;">
                                🟢 {{ __('Verified') }}
                            </h4>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li>{{ __('Unlimited job postings (with active package)') }}</li>
                                <li>{{ __('Full access to resume database (with CV package)') }}</li>
                                <li>{{ __('Up to 3x more applications') }}</li>
                                <li>{{ __('Trusted badge on all job listings') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="how-to-verify" style="margin: 40px 0;">
                        <h3 style="color: #1a2332; margin-bottom: 25px;">{{ __('How to Get Verified') }}</h3>
                        
                        <div class="steps" style="margin: 20px 0;">
                            <div class="step" style="display: flex; gap: 20px; margin-bottom: 25px;">
                                <div class="step-number" style="flex-shrink: 0; width: 40px; height: 40px; background: #0056b3; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">
                                    1
                                </div>
                                <div class="step-content">
                                    <h5 style="margin-bottom: 8px; color: #1a2332;">{{ __('Upload Business Documents') }}</h5>
                                    <p style="color: #4a5568; margin: 0;">
                                        {{ __('Submit your business registration, tax documents, or establishment photos through your employer dashboard.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="step" style="display: flex; gap: 20px; margin-bottom: 25px;">
                                <div class="step-number" style="flex-shrink: 0; width: 40px; height: 40px; background: #0056b3; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">
                                    2
                                </div>
                                <div class="step-content">
                                    <h5 style="margin-bottom: 8px; color: #1a2332;">{{ __('Admin Review') }}</h5>
                                    <p style="color: #4a5568; margin: 0;">
                                        {{ __('Our team will review your documents within 1-3 business days to verify your organization\'s legitimacy.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="step" style="display: flex; gap: 20px; margin-bottom: 25px;">
                                <div class="step-number" style="flex-shrink: 0; width: 40px; height: 40px; background: #0056b3; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">
                                    3
                                </div>
                                <div class="step-content">
                                    <h5 style="margin-bottom: 8px; color: #1a2332;">{{ __('Get Verified') }}</h5>
                                    <p style="color: #4a5568; margin: 0;">
                                        {{ __('Once approved, you\'ll receive the verified badge and unlock all premium features immediately.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="benefits" style="margin: 40px 0; background: #f8f9fa; padding: 30px; border-radius: 8px;">
                        <h3 style="color: #1a2332; margin-bottom: 25px;">{{ __('Benefits of Verification') }}</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="benefit-item" style="margin-bottom: 20px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                                    <strong>{{ __('Build Trust:') }}</strong> {{ __('Show candidates you\'re a legitimate employer') }}
                                </div>
                                <div class="benefit-item" style="margin-bottom: 20px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                                    <strong>{{ __('More Applications:') }}</strong> {{ __('Verified employers receive 3x more applications') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item" style="margin-bottom: 20px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                                    <strong>{{ __('Resume Access:') }}</strong> {{ __('Search and contact candidates directly') }}
                                </div>
                                <div class="benefit-item" style="margin-bottom: 20px;">
                                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 10px;"></i>
                                    <strong>{{ __('Unlimited Posting:') }}</strong> {{ __('Post as many jobs as you need') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cta-section" style="text-align: center; margin-top: 40px;">
                        @auth('company')
                            <a href="{{ route('company.verification.upload') }}" class="btn btn-primary btn-lg" style="padding: 15px 40px; font-size: 18px;">
                                <i class="fas fa-check-circle"></i> {{ __('Get Verified Now') }}
                            </a>
                        @else
                            <a href="{{ route('company.register') }}" class="btn btn-primary btn-lg" style="padding: 15px 40px; font-size: 18px;">
                                <i class="fas fa-user-plus"></i> {{ __('Register as Employer') }}
                            </a>
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection
