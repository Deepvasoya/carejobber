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
        @include('includes.user_dashboard_menu')
            <div class="col-md-9 col-sm-8"> 
    @include('flash::message') 
                
                <!-- Payment History Header -->
                <div class="user-payment-history-header">
                    <h2>
                        <i class="fas fa-receipt"></i>
                        {{__('Package Purchase History')}}
                    </h2>
                    <p>{{__('View all your package purchases and transaction details')}}</p>
                </div>
                
                <!-- Payment Timeline -->
                <div class="user-payment-timeline">
        @forelse ($candidatePayments as $payment)
                        @if ($payment->package || !empty($payment->package_title))
                        <div class="user-payment-card">
                            <div class="user-payment-card-header">
                                <div class="user-payment-package-info">
                                    <h4>
                                        <i class="fas fa-box"></i>
                                        {{ $payment->package_title ?? optional($payment->package)->package_title }}
                                    </h4>
                                    @php
                                        $paymentMethod = !empty($payment->payment_method) && $payment->payment_method !== 'offline' 
                                            ? $payment->payment_method 
                                            : 'Admin Assign';
                                        $badgeClass = 'user-payment-method-' . strtolower(str_replace(' ', '-', $paymentMethod));
                                    @endphp
                                    <span class="user-payment-method-badge {{ $badgeClass }}">
                                        @if(stripos($paymentMethod, 'paypal') !== false)
                                            <i class="fab fa-paypal"></i> PayPal
                                        @elseif(stripos($paymentMethod, 'stripe') !== false)
                                            <i class="fab fa-stripe"></i> Stripe
                                        @elseif(stripos($paymentMethod, 'razorpay') !== false)
                                            <i class="fas fa-credit-card"></i> Razorpay
                                        @elseif(stripos($paymentMethod, 'paystack') !== false)
                                            <i class="fas fa-credit-card"></i> Paystack
                                        @elseif(stripos($paymentMethod, 'paytm') !== false)
                                            <i class="fas fa-credit-card"></i> Paytm
                                        @elseif(stripos($paymentMethod, 'payu') !== false)
                                            <i class="fas fa-credit-card"></i> PayU
                                        @else
                                            <i class="fas fa-user-shield"></i> {{__('Admin Assigned')}}
                                        @endif
                                    </span>
                                </div>
                                <div class="user-payment-price-badge">
                                    <i class="fas fa-tag"></i> {{ $siteSetting->default_currency_code }}{{ $payment->package->package_price }}
                                </div>
                            </div>
                            
                            <div class="user-payment-details-inline">
                                @if(isset($payment->jobs_quota) && $payment->jobs_quota > 0 && $payment->package_type != 'featured_profile')
                                <!-- Applications Quota -->
                                <div class="user-payment-detail-item-inline">
                                    <div class="user-payment-detail-icon-inline">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="user-payment-detail-text-inline">
                                        <span class="user-payment-detail-label-inline">{{__('Applications')}}:</span>
                                        <span class="user-payment-detail-value-inline">{{ $payment->jobs_quota }}</span>
                                    </div>
                                </div>
                                @endif
                                
                                @if(isset($payment->package_type) && $payment->package_type == 'featured_profile')
                                <!-- Featured Badge -->
                                <div class="user-payment-detail-item-inline">
                                    <div class="user-payment-detail-icon-inline">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="user-payment-detail-text-inline">
                                        <span class="user-payment-detail-label-inline">{{__('Type')}}:</span>
                                        <span class="user-payment-detail-value-inline">{{__('Featured Profile')}}</span>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Duration -->
                                @if($payment->package)
                                <div class="user-payment-detail-item-inline">
                                    <div class="user-payment-detail-icon-inline">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="user-payment-detail-text-inline">
                                        <span class="user-payment-detail-label-inline">{{__('Duration')}}:</span>
                                        <span class="user-payment-detail-value-inline">{{ $payment->package->package_num_days }} {{__('Days')}}</span>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Start Date -->
                                <div class="user-payment-detail-item-inline">
                                    <div class="user-payment-detail-icon-inline">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="user-payment-detail-text-inline">
                                        <span class="user-payment-detail-label-inline">{{__('Started')}}:</span>
                                        <span class="user-payment-detail-value-inline">
                                            {{ $payment->package_start_date ? \Carbon\Carbon::parse($payment->package_start_date)->format('d M, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- End Date -->
                                <div class="user-payment-detail-item-inline">
                                    <div class="user-payment-detail-icon-inline">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="user-payment-detail-text-inline">
                                        <span class="user-payment-detail-label-inline">{{__('Expires')}}:</span>
                                        <span class="user-payment-detail-value-inline">
                                            @if($payment->package_end_date)
                                                @php
                                                    $endDate = \Carbon\Carbon::parse($payment->package_end_date);
                                                    $isExpired = $endDate->isPast();
                                                @endphp
                                                <span class="{{ $isExpired ? 'text-danger' : 'text-success' }}">
                                                    {{ $endDate->format('d M, Y') }}
                                                </span>
                                                @if($isExpired)
                                                    <span class="badge bg-danger ms-2">{{__('Expired')}}</span>
                                                @else
                                                    <span class="badge bg-success ms-2">{{__('Active')}}</span>
                                                @endif
                @else
                                                N/A
                @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
    @endif
@empty
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{__('No payment history found')}}
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
<!-- Additional scripts if needed -->
@endpush
