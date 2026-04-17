@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('My Profile')])
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row">
            @include('includes.user_dashboard_menu')

            <div class="col-md-9 col-sm-8">
                <!-- Resume Promotion Card -->
                @php
                $hasActivePromotion = $user->is_resume_promoted &&
                $user->promotion_end_date &&
                \Carbon\Carbon::parse($user->promotion_end_date)->isFuture();
                @endphp

                @if($hasActivePromotion)
                <div class="alert alert-success mb-4" style="border-radius: 12px; border-left: 4px solid #28a745; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <h5 style="margin: 0 0 8px 0; color: #155724; font-weight: 600;">
                                <i class="fas fa-star"></i> {{__('Your Resume is Promoted')}}
                            </h5>
                            <p style="margin: 0; color: #155724;">
                                {{__('Active until')}}: <strong>{{ \Carbon\Carbon::parse($user->promotion_end_date)->format('M d, Y') }}</strong>
                            </p>
                        </div>
                        <a href="{{ route('resume.promotion.packages') }}" class="btn btn-sm" style="background: #28a745; color: #fff; border-radius: 8px; padding: 8px 20px;">
                            <i class="fas fa-arrow-up"></i> {{__('Extend')}}
                        </a>
                    </div>
                </div>
                @else
                <div class="card mb-4" style="border-radius: 12px; border: 2px solid #007bff; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                    <div class="card-body" style="padding: 25px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="flex: 1;">
                                <h5 style="margin: 0 0 10px 0; color: #0d47a1; font-weight: 600;">
                                    <i class="fas fa-rocket"></i> {{__('Boost Your Career!')}}
                                </h5>
                                <p style="margin: 0; color: #1565c0; font-size: 14px;">
                                    {{__('Promote your resume to appear at the top of employer searches. Get noticed by more employers!')}}
                                </p>
                            </div>
                            <div style="margin-left: 20px;">
                                <a href="{{ route('resume.promotion.packages') }}" class="btn" style="background: #007bff; color: #fff; border-radius: 8px; padding: 12px 24px; font-weight: 600; white-space: nowrap;">
                                    <i class="fas fa-star"></i> {{__('Promote Resume')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="userccount">
                    <div class="formpanel mt0">
                        @include('flash::message')
                        @if(request()->get('profile_updated') == 1)
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {{ __('You have updated your profile successfully') }}
                        </div>
                        @endif
                        <!-- Personal Information -->
                        @include('user.inc.profile')
                    </div>
                </div>

                <div class="userccount">
                    <div class="formpanel mt0">
                        @include('user.inc.summary')
                    </div>
                </div>

                @include('user.inc.build_resume_embed')

            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
@push('styles')
<style type="text/css">
    .userccount p {
        text-align: left !important;
    }
</style>
@endpush
@push('scripts')
@include('includes.immediate_available_btn')

<script>
    $(document).on('click', '.btn-close', function() {
        $('.modal').css('display', 'none');
        $('.modal-backdrop').remove();
        $('.modal').removeAttr('style');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').removeAttr('style');
    });
</script>

@endpush