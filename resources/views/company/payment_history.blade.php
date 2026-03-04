@extends('layouts.app')

@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 



<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title' => __('Payment History')]) 
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container">
        <div class="row">
            @include('includes.company_dashboard_menu')
            <div class="col-md-9 col-sm-8"> 
                @include('flash::message')
                
                <!-- Payment History Header -->
                <div class="company-payment-history-header">
                    <h2>
                        <i class="fas fa-receipt"></i>
                        {{__('Package Purchase History')}}
                    </h2>
                    <p>{{__('View all your package purchases and transaction details')}}</p>
                </div>
                
                <!-- Payment Timeline -->
                <div class="company-payment-timeline">
                    @forelse ($companies as $company)
                        <div class="company-payment-card">
                            <div class="company-payment-card-header">
                                <div class="company-payment-package-info">
                                    <h4>
                                        <i class="fas fa-box"></i>
                                        {{ $company->package->package_title ?? 'N/A' }}
                                    </h4>
                                    @php
                                        $paymentMethod = !empty($company->payment_method) && $company->payment_method !== 'offline' 
                                            ? $company->payment_method 
                                            : 'offline';
                                        $badgeClass = 'company-payment-method-' . strtolower($paymentMethod);
                                    @endphp
                                    <span class="company-payment-method-badge {{ $badgeClass }}">
                                        @if($paymentMethod === 'paypal')
                                            <i class="fab fa-paypal"></i> PayPal
                                        @elseif($paymentMethod === 'stripe')
                                            <i class="fab fa-stripe"></i> Stripe
                                        @elseif($paymentMethod === 'razorpay')
                                            <i class="fas fa-credit-card"></i> Razorpay
                                        @elseif($paymentMethod === 'paystack')
                                            <i class="fas fa-credit-card"></i> Paystack
                                        @elseif($paymentMethod === 'paytm')
                                            <i class="fas fa-credit-card"></i> Paytm
                                        @elseif($paymentMethod === 'payu')
                                            <i class="fas fa-credit-card"></i> PayU
                                        @else
                                            <i class="fas fa-user-shield"></i> {{__('Offline (Added by Admin)')}}
                                        @endif
                                    </span>
                                </div>
                                <div class="company-payment-price-badge">
                                    <i class="fas fa-tag"></i> {{ $siteSetting->default_currency_code ?? '' }}{{ $company->package->package_price ?? 'N/A' }}
                                </div>
                            </div>
                            
                            <div class="company-payment-details-inline">
                                <!-- Jobs Quota -->
                                <div class="company-payment-detail-item-inline">
                                    <div class="company-payment-detail-icon-inline">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="company-payment-detail-text-inline">
                                        <span class="company-payment-detail-label-inline">{{__('Jobs')}}:</span>
                                        <span class="company-payment-detail-value-inline">{{ $company->jobs_quota ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                
                                <!-- Start Date -->
                                <div class="company-payment-detail-item-inline">
                                    <div class="company-payment-detail-icon-inline">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="company-payment-detail-text-inline">
                                        <span class="company-payment-detail-label-inline">{{__('Start')}}:</span>
                                        <span class="company-payment-detail-value-inline">
                                            {{ $company->package_start_date ? \Carbon\Carbon::parse($company->package_start_date)->format('d M, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- End Date -->
                                <div class="company-payment-detail-item-inline">
                                    <div class="company-payment-detail-icon-inline">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="company-payment-detail-text-inline">
                                        <span class="company-payment-detail-label-inline">{{__('Expires')}}:</span>
                                        <span class="company-payment-detail-value-inline">
                                            {{ $company->package_end_date ? \Carbon\Carbon::parse($company->package_end_date)->format('d M, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="company-payment-no-records">
                            <i class="fas fa-inbox"></i>
                            <h3>{{__('No Payment History Found')}}</h3>
                            <p>{{__('You haven\'t made any package purchases yet')}}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>



@include('includes.footer')
@endsection

@push('scripts')
<!-- jsPDF Library -->



@endpush
