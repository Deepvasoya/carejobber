@extends('layouts.app')

@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->

<div class="listpgWraper" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 80vh;">
    <div class="container-fluid py-5">
        @include('flash::message')

        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('user.profile', $user->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> {{__('Back to Profile')}}
            </a>
        </div>

        <!-- Main Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header Section -->
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">{{__('Unlock Full Resume Access')}}</h1>
                    <p class="lead text-muted">{{__('Get complete candidate information with one-time payment')}}</p>
                </div>

                <!-- Candidate Preview Card -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                {{ $user->printUserImage(100, 100) }}
                            </div>
                            <div class="col-md-10">
                                <h4 class="mb-2">
                                    <i class="fas fa-user-lock text-muted me-2"></i> {{__('Candidate')}}
                                </h4>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i> {{ $user->getCity('city') }}, {{ $user->getState('state') }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-briefcase me-2"></i> {{ $user->getFunctionalArea('functional_area') }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-chart-line me-2"></i> {{ $user->getCareerLevel('career_level') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Options -->
                <div class="row g-4">
                    <!-- Stripe Payment Option -->
                    <div class="col-md-6">
                        <div class="card shadow-lg border-0 h-100 position-relative" style="border-radius: 16px; border: 3px solid #667eea;">
                            <!-- Popular Badge -->
                            <div class="position-absolute top-0 start-50 translate-middle">
                                <span class="badge bg-primary" style="border-radius: 20px; padding: 8px 20px; font-size: 14px;">
                                    <i class="fas fa-star"></i> {{__('Most Popular')}}
                                </span>
                            </div>

                            <div class="card-body p-5 text-center">
                                <div class="mb-4 mt-3">
                                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                                    <h3 class="fw-bold mb-2">{{__('Pay with Stripe')}}</h3>
                                    <p class="text-muted">{{__('Secure one-time payment')}}</p>
                                </div>

                                <!-- Price -->
                                <div class="mb-4">
                                    <div class="display-3 fw-bold text-primary">
                                        {{ config('app.resume_unlock_currency', 'CAD') }}${{ number_format(config('app.resume_unlock_price', 10.00), 2) }}
                                    </div>
                                    <p class="text-muted">{{__('per resume')}}</p>
                                </div>

                                <!-- Features -->
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Instant access after payment')}}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Lifetime access (no expiration)')}}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Secure Stripe checkout')}}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Full refund within 24 hours')}}
                                    </li>
                                </ul>

                                <!-- CTA Button -->
                                <div class="d-grid">
                                    <a href="{{ route('resume.unlock.checkout', $user->id) }}" class="btn btn-primary btn-lg" style="border-radius: 8px; padding: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                        <i class="fas fa-credit-card me-2"></i> {{__('Proceed to Payment')}}
                                    </a>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt text-success me-1"></i> {{__('SSL Encrypted')}}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-lock text-success me-1"></i> {{__('PCI Compliant')}}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Credits Option -->
                    @if(Auth::guard('company')->user()->cvs_quota > Auth::guard('company')->user()->availed_cvs_quota)
                    <div class="col-md-6">
                        <div class="card shadow border-0 h-100" style="border-radius: 16px;">
                            <div class="card-body p-5 text-center">
                                <div class="mb-4 mt-3">
                                    <i class="fas fa-coins fa-3x text-warning mb-3"></i>
                                    <h3 class="fw-bold mb-2">{{__('Use Credits')}}</h3>
                                    <p class="text-muted">{{__('From your active package')}}</p>
                                </div>

                                <!-- Credits Available -->
                                <div class="mb-4">
                                    <div class="display-3 fw-bold text-warning">
                                        {{ Auth::guard('company')->user()->cvs_quota - Auth::guard('company')->user()->availed_cvs_quota }}
                                    </div>
                                    <p class="text-muted">{{__('credits available')}}</p>
                                </div>

                                <!-- Features -->
                                <ul class="list-unstyled text-start mb-4">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('No additional payment required')}}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Instant unlock')}}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Same full access as paid')}}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{__('Part of your package')}}
                                    </li>
                                </ul>

                                <!-- CTA Button -->
                                <div class="d-grid">
                                    <a href="{{ route('company.unlock', $user->id) }}" class="btn btn-warning btn-lg" style="border-radius: 8px; padding: 16px;">
                                        <i class="fas fa-coins me-2"></i> {{__('Use 1 Credit')}}
                                    </a>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> {{__('Credits expire with package')}}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- No Credits - Upgrade Package -->
                    <div class="col-md-6">
                        <div class="card shadow border-0 h-100" style="border-radius: 16px; background: #f8f9fa;">
                            <div class="card-body p-5 text-center d-flex flex-column justify-content-center">
                                <div class="mb-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h3 class="fw-bold mb-2">{{__('No Credits Available')}}</h3>
                                    <p class="text-muted">{{__('Upgrade your package to get credits')}}</p>
                                </div>

                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-lightbulb me-2"></i>
                                    {{__('With a package, unlock multiple resumes at a lower cost per resume!')}}
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('company.packages') }}" class="btn btn-outline-primary btn-lg" style="border-radius: 8px; padding: 16px;">
                                        <i class="fas fa-shopping-cart me-2"></i> {{__('View Packages')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- What You'll Get Section -->
                <div class="card shadow-sm border-0 mt-5" style="border-radius: 16px;">
                    <div class="card-body p-5">
                        <h3 class="text-center fw-bold mb-4">{{__('What You\'ll Get After Unlocking')}}</h3>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-address-card fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold">{{__('Full Contact Info')}}</h5>
                                    <p class="text-muted small">{{__('Full name, email, phone number, and complete address')}}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-file-alt fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold">{{__('Complete Resume')}}</h5>
                                    <p class="text-muted small">{{__('Full work history, education, skills, languages, and portfolio')}}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-download fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="fw-bold">{{__('CV Download')}}</h5>
                                    <p class="text-muted small">{{__('Download resume in original format (PDF, DOCX, etc.)')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="mt-5">
                    <h4 class="text-center fw-bold mb-4">{{__('Frequently Asked Questions')}}</h4>
                    <div class="accordion" id="unlockFAQ">
                        <div class="accordion-item border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    {{__('How long does access last?')}}
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#unlockFAQ">
                                <div class="accordion-body">
                                    {{__('Once unlocked, you have lifetime access to this candidate\'s profile. There is no expiration date.')}}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    {{__('What payment methods do you accept?')}}
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#unlockFAQ">
                                <div class="accordion-body">
                                    {{__('We accept all major credit cards (Visa, Mastercard, Amex) and debit cards through our secure Stripe payment gateway, or PayPal. You can also use package credits if available.')}}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    {{__('Can I get a refund?')}}
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#unlockFAQ">
                                <div class="accordion-body">
                                    {{__('No, once you made a purchase and/or viewed resume, we are not able to give refund. Contact our support team for assistance if you need help.')}}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0" style="border-radius: 12px; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    {{__('What\'s the difference between credits and payment?')}}
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#unlockFAQ">
                                <div class="accordion-body">
                                    {{__('Credits come with your package subscription and provide the same access as paid unlocks. If you have credits, we recommend using them first. Credits expire when your package expires, but paid unlocks never expire.')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection

@push('styles')
<style>
.listpgWraper .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.listpgWraper .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.accordion-button:not(.collapsed) {
    background-color: #667eea;
    color: white;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: #667eea;
}
</style>
@endpush
