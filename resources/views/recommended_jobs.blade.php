@extends('layouts.app')
@section('content')
@include('includes.header')
@include('includes.inner_page_title', ['page_title' => __('Recommended Jobs')])
<div class="listpgWraper">
    <div class="container-fluid">@include('flash::message')
        <div class="row">
            @include('includes.user_dashboard_menu')
            <div class="col-md-9 col-sm-8">
                <div class="myads">
                    <p class="text-muted mb-3">{{ __('Jobs matched to your profile (up to 20). Refine your CV and job preferences on your dashboard for better matches.') }}</p>
                    @if(isset($matchingJobs) && $matchingJobs->count() > 0)
                        <ul class="featuredlist row">
                            @foreach($matchingJobs as $match)
                                @include('includes.job_seeker_dashboard_job_card', ['job' => $match])
                            @endforeach
                        </ul>
                        <p class="mt-3 mb-0">
                            <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> {{ __('Back to dashboard') }}</a>
                        </p>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ __('No matching jobs found') }}
                        </div>
                        <p class="mb-0">
                            <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> {{ __('Back to dashboard') }}</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
