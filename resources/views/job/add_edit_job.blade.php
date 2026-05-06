@extends('layouts.app')
@section('content') 
<!-- Header start --> 
@include('includes.header') 
<!-- Header end --> 
<!-- Inner Page Title start --> 
@include('includes.inner_page_title', ['page_title'=>__('Job Details')]) 
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row">
            @include('includes.company_dashboard_menu')

            <div class="col-md-9 col-sm-8"> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="userccount">
                            <div class="formpanel mt-0"> @include('flash::message') 
                                <!-- Personal Information -->
                                @include('job.inc.job')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')

{{-- Verification Modal for Unverified Employers --}}
@if(Auth::guard('company')->check() && Auth::guard('company')->user()->getEmployerTrustStatus() === 'unverified' && !Auth::guard('company')->user()->isVerified())
<div class="modal fade" id="verificationModal" tabindex="-1" role="dialog" aria-labelledby="verificationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #f8f9fa; border-bottom: 2px solid #ffc107;">
                <h5 class="modal-title" id="verificationModalLabel">
                    <i class="fas fa-shield-alt text-warning"></i> 
                    {{ __('Get More Applications with Verification') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 30px;">
                <div class="text-center mb-3">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                </div>
                <h6 class="text-center mb-3" style="font-size: 18px; font-weight: 600;">
                    {{ __('Verified employers are trusted and get more applications.') }}
                </h6>
                <ul style="list-style: none; padding: 0; margin: 20px 0;">
                    <li style="padding: 8px 0;">
                        <i class="fas fa-check text-success"></i> 
                        {{ __('Build trust with healthcare professionals') }}
                    </li>
                    <li style="padding: 8px 0;">
                        <i class="fas fa-check text-success"></i> 
                        {{ __('Increase application rates by up to 3x') }}
                    </li>
                    <li style="padding: 8px 0;">
                        <i class="fas fa-check text-success"></i> 
                        {{ __('Access full resume database') }}
                    </li>
                    <li style="padding: 8px 0;">
                        <i class="fas fa-check text-success"></i> 
                        {{ __('Post unlimited jobs') }}
                    </li>
                </ul>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 15px 30px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="continueAnywayBtn">
                    {{ __('Continue Anyway') }}
                </button>
                <a href="{{ route('company.verification.upload') }}" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> {{ __('Request Review') }}
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show modal when page loads for unverified employers
    @if(!session('verification_modal_shown'))
        $('#verificationModal').modal('show');
        // Set session flag to not show again in this session
        $.post("{{ route('set.verification.modal.shown') }}", {
            _token: '{{ csrf_token() }}'
        });
    @endif
    
    // Ensure Continue Anyway button works properly - force close modal
    $('#continueAnywayBtn, #verificationModal .close').on('click', function(e) {
        e.preventDefault();
        $('#verificationModal').modal('hide');
        // Remove backdrop manually if it persists
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
});
</script>
@endpush
@endif

@endsection
@push('styles')
<style type="text/css">
    .userccount p{ text-align:left !important;}
</style>
@endpush