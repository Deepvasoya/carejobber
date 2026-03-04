<!-- Resume Unlock Modal -->
<div class="modal fade" id="resumeUnlockModal" tabindex="-1" aria-labelledby="resumeUnlockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-5">
                <!-- Title -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-2">{{__('Unlock Full Resume')}}</h2>
                    <p class="text-muted">{{__('Get complete access to candidate profile and contact details')}}</p>
                </div>

                <!-- Pricing Card -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0 position-relative" style="border-radius: 16px; overflow: hidden;">
                            <!-- Popular Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary" style="border-radius: 20px; padding: 6px 16px;">
                                    <i class="fas fa-star"></i> {{__('One-Time Payment')}}
                                </span>
                            </div>

                            <div class="card-body p-4">
                                <!-- Price -->
                                <div class="text-center mb-4 mt-3">
                                    <div class="display-4 fw-bold text-primary">
                                        {{ config('app.resume_unlock_currency', 'CAD') }}${{ number_format(config('app.resume_unlock_price', 10.00), 2) }}
                                    </div>
                                    <p class="text-muted">{{__('per resume')}}</p>
                                </div>

                                <!-- Features -->
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>{{__('Full Contact Details')}}</strong>
                                        <div class="ms-4 text-muted small">{{__('Email, phone number, and location')}}</div>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>{{__('Complete Work History')}}</strong>
                                        <div class="ms-4 text-muted small">{{__('All positions, responsibilities, and achievements')}}</div>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>{{__('Full Education Details')}}</strong>
                                        <div class="ms-4 text-muted small">{{__('Degrees, institutions, and major subjects')}}</div>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>{{__('Skills & Languages')}}</strong>
                                        <div class="ms-4 text-muted small">{{__('Complete skill set and language proficiency')}}</div>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>{{__('CV Download')}}</strong>
                                        <div class="ms-4 text-muted small">{{__('Download resume in original format')}}</div>
                                    </li>
                                    <li class="mb-3">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>{{__('Lifetime Access')}}</strong>
                                        <div class="ms-4 text-muted small">{{__('No expiration, view anytime')}}</div>
                                    </li>
                                </ul>

                                <!-- CTA Buttons -->
                                <div class="d-grid gap-2 mt-4">
                                    <a href="{{ route('resume.unlock.checkout', $userId) }}" class="btn btn-primary btn-lg" style="border-radius: 8px; padding: 14px;">
                                        <i class="fas fa-credit-card me-2"></i> {{__('Pay with Stripe')}}
                                    </a>
                                    
                                    @if(Auth::guard('company')->check() && Auth::guard('company')->user()->cvs_quota > Auth::guard('company')->user()->availed_cvs_quota)
                                    <a href="{{ route('company.unlock', $userId) }}" class="btn btn-outline-primary btn-lg" style="border-radius: 8px; padding: 14px;">
                                        <i class="fas fa-coins me-2"></i> {{__('Use 1 Credit')}} 
                                        <small class="ms-2">({{ Auth::guard('company')->user()->cvs_quota - Auth::guard('company')->user()->availed_cvs_quota }} {{__('remaining')}})</small>
                                    </a>
                                    @endif
                                </div>

                                <!-- Trust Badges -->
                                <div class="text-center mt-4">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt text-success me-1"></i> {{__('Secure payment via Stripe')}}
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-lock text-success me-1"></i> {{__('SSL Encrypted')}}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Alternative: Use Credits -->
                        @if(Auth::guard('company')->check() && Auth::guard('company')->user()->cvs_quota > Auth::guard('company')->user()->availed_cvs_quota)
                        <div class="text-center mt-3">
                            <p class="text-muted mb-2">{{__('Or use your package credits')}}</p>
                            <small class="text-success">
                                <i class="fas fa-coins me-1"></i> 
                                {{__('You have')}} <strong>{{ Auth::guard('company')->user()->cvs_quota - Auth::guard('company')->user()->availed_cvs_quota }}</strong> {{__('credits remaining')}}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#resumeUnlockModal .modal-content {
    border-radius: 20px;
    border: none;
}

#resumeUnlockModal .card {
    transition: transform 0.2s ease;
}

#resumeUnlockModal .card:hover {
    transform: translateY(-4px);
}

#resumeUnlockModal .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

#resumeUnlockModal .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

#resumeUnlockModal .list-unstyled li {
    padding: 8px 0;
}
</style>
