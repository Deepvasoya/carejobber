@extends('layouts.app')

@section('content')
@include('includes.header')

<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title' => __('Promote Your Resume')])
<!-- Inner Page Title end -->

<div class="listpgWraper">
    <div class="container-fluid">
        @include('flash::message')

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Current Promotion Status -->
                @if($hasActivePromotion)
                <div class="alert alert-success mb-4" style="border-radius: 12px; border-left: 4px solid #28a745;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h5 style="margin: 0 0 8px 0; color: #155724; font-weight: 600;">
                                <i class="fas fa-star"></i> {{__('Your Resume is Currently Promoted')}}
                            </h5>
                            <p style="margin: 0; color: #155724;">
                                {{__('Active until')}}: <strong>{{ \Carbon\Carbon::parse($user->promotion_end_date)->format('M d, Y') }}</strong>
                            </p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center mb-4">
                    <h2 style="font-weight: 600; color: #333; margin-bottom: 15px;">{{__('Promote Your Resume')}}</h2>
                    <p style="color: #666; font-size: 16px;">{{__('Get noticed by employers! Promote your resume to appear at the top of search results.')}}</p>
                </div>
                @endif

                @if((bool)($siteSetting->is_stripe_active ?? false))
                <div class="mb-4">
                    @include('includes.package_coupon_resume_promotion')
                </div>
                @endif

                <!-- Packages -->
                <div class="row">
                    @foreach($packages as $package)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100" style="border-radius: 16px; border: 2px solid #e0e0e0; transition: all 0.3s; overflow: hidden;">
                            @if($package->duration_days == 365)
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 8px; text-align: center; font-weight: 600; font-size: 12px; text-transform: uppercase;">
                                {{__('Best Value')}}
                            </div>
                            @elseif($package->duration_days == 180)
                            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; padding: 8px; text-align: center; font-weight: 600; font-size: 12px; text-transform: uppercase;">
                                {{__('Popular')}}
                            </div>
                            @endif

                            <div class="card-body" style="padding: 30px; text-align: center;">
                                <div style="margin-bottom: 20px;">
                                    <i class="fas fa-star" style="font-size: 48px; color: #ffc107;"></i>
                                </div>

                                <h4 style="font-weight: 600; color: #333; margin-bottom: 10px;">
                                    {{ $package->name }}
                                </h4>

                                <div style="margin-bottom: 20px;">
                                    <span style="font-size: 48px; font-weight: 700; color: #007bff;">${{ number_format($package->price, 0) }}</span>
                                    <span style="color: #666; font-size: 16px;">{{ $package->currency }}</span>
                                </div>

                                <p style="color: #666; margin-bottom: 25px;">
                                    {{ $package->description }}
                                </p>

                                <ul style="list-style: none; padding: 0; margin-bottom: 25px; text-align: left;">
                                    <li style="padding: 8px 0; color: #666;">
                                        <i class="fas fa-check text-success"></i> {{__('Top of search results')}}
                                    </li>
                                    <li style="padding: 8px 0; color: #666;">
                                        <i class="fas fa-check text-success"></i> {{__('Increased visibility')}}
                                    </li>
                                    <li style="padding: 8px 0; color: #666;">
                                        <i class="fas fa-check text-success"></i> {{__('More employer views')}}
                                    </li>
                                    <li style="padding: 8px 0; color: #666;">
                                        <i class="fas fa-check text-success"></i>
                                        <strong>{{ $package->duration_days }} {{__('days')}}</strong> {{__('promotion')}}
                                    </li>
                                </ul>

                                @if($hasActivePromotion)
                                <button class="btn btn-secondary" disabled style="width: 100%; padding: 14px; border-radius: 8px; font-weight: 600;">
                                    {{__('Already Promoted')}}
                                </button>
                                @else
                                <a href="{{ route('resume.promotion.checkout', $package->id) }}" class="btn btn-primary" style="width: 100%; padding: 14px; border-radius: 8px; font-weight: 600; background: #007bff; border: none;">
                                    {{__('Select Package')}}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Benefits Section -->
                <div class="mt-5 p-4" style="background: #f8f9fa; border-radius: 12px;">
                    <h4 style="font-weight: 600; color: #333; margin-bottom: 20px; text-align: center;">
                        {{__('Why Promote Your Resume?')}}
                    </h4>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-eye" style="font-size: 36px; color: #007bff; margin-bottom: 15px;"></i>
                            <h5 style="font-weight: 600; color: #333;">{{__('Get Noticed')}}</h5>
                            <p style="color: #666; font-size: 14px;">{{__('Your resume appears at the top of employer searches')}}</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-chart-line" style="font-size: 36px; color: #28a745; margin-bottom: 15px;"></i>
                            <h5 style="font-weight: 600; color: #333;">{{__('More Views')}}</h5>
                            <p style="color: #666; font-size: 14px;">{{__('Increase your profile views by up to 10x')}}</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <i class="fas fa-briefcase" style="font-size: 36px; color: #ffc107; margin-bottom: 15px;"></i>
                            <h5 style="font-weight: 600; color: #333;">{{__('More Opportunities')}}</h5>
                            <p style="color: #666; font-size: 14px;">{{__('Get contacted by more employers for job opportunities')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection