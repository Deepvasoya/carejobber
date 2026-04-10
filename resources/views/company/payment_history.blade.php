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
                    @forelse ($payments as $payment)
                        @php
                            $pkgTitle = $payment->package_title ?? optional($payment->package)->package_title ?? 'N/A';
                            $pkgPrice = $payment->package_price ?? optional($payment->package)->package_price;
                            $pmRaw = $payment->payment_method ?? '';
                            $paymentMethod = !empty($pmRaw) && strtolower($pmRaw) !== 'offline'
                                ? $pmRaw
                                : 'offline';
                            $badgeClass = 'company-payment-method-' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $paymentMethod));
                        @endphp
                        <div class="company-payment-card">
                            <div class="company-payment-card-header">
                                <div class="company-payment-package-info">
                                    <h4>
                                        <i class="fas fa-box"></i>
                                        {{ $pkgTitle }}
                                    </h4>
                                    <span class="company-payment-method-badge {{ $badgeClass }}">
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
                                            <i class="fas fa-user-shield"></i> {{__('Offline (Added by Admin)')}}
                                        @endif
                                    </span>
                                </div>
                                <div class="company-payment-price-badge text-end">
                                    <div><i class="fas fa-tag"></i> {{ $siteSetting->default_currency_code ?? '' }}{{ $pkgPrice !== null ? number_format((float) $pkgPrice, 2) : 'N/A' }}</div>
                                    @if(isset($payment->package_list_price) && (float) $payment->package_list_price > (float) ($pkgPrice ?? 0) + 0.001)
                                        <div class="small text-muted mt-1">{{ __('Regular price') }}: {{ $siteSetting->default_currency_code ?? '' }}{{ number_format((float) $payment->package_list_price, 2) }}</div>
                                    @endif
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
                                        <span class="company-payment-detail-value-inline">{{ isset($payment->jobs_quota) ? $payment->jobs_quota : 'N/A' }}</span>
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
                                            {{ $payment->package_start_date ? \Carbon\Carbon::parse($payment->package_start_date)->format('d M, Y') : 'N/A' }}
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
                                            {{ $payment->package_end_date ? \Carbon\Carbon::parse($payment->package_end_date)->format('d M, Y') : 'N/A' }}
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
