@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Applied Jobs')])
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row">
            @include('includes.user_dashboard_menu')

            <div class="col-md-9 col-sm-8">
                <div class="myads">

                    @if(isset($appliedJobs) && count($appliedJobs) > 0)
                    <ul class="featuredlist row">
                        @foreach($appliedJobs as $appliedjob)
                        @php
                        $job = $appliedjob->job;
                        $company = $job ? $job->company : null;
                        @endphp
                        @if($job && $company)
                        <li class="col-lg-4 col-md-6 @if($job->isPromotionFeaturedActive()) featured @endif">
                            <div class="jobint mt-0 mb-3">
                                @if($job->isPromotionFeaturedActive())
                                <span class="promotepof-badge"><i class="fa fa-bolt" title="{{__('Featured Job')}}"></i></span>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fticon"><i class="fas fa-briefcase"></i> {{$job->getJobType('job_type')}}</div>
                                    @php
                                    $statusClass = 'secondary';
                                    if ($appliedjob->status == 'pending') {
                                    $statusClass = 'warning';
                                    } elseif (in_array($appliedjob->status, ['approved', 'hire', 'hired'])) {
                                    $statusClass = 'success';
                                    } elseif ($appliedjob->status == 'rejected') {
                                    $statusClass = 'danger';
                                    }
                                    @endphp
                                    <strong class="badge bg-{{ $statusClass }}">
                                        {{ucfirst($appliedjob->status)}}
                                    </strong>
                                </div>
                                <h4>
                                    <a href="{{route('job.detail', [$job->slug])}}" title="{{$job->title}}">
                                        {!! \Illuminate\Support\Str::limit($job->title, 25, '...') !!}
                                    </a>
                                </h4>
                                @if(!(bool)$job->hide_salary)
                                <div class="salary mb-2">{{__('Salary')}}:
                                    <strong>{{$job->salary_currency.''.$job->salary_from}} - {{$job->salary_currency.''.$job->salary_to}}/{{$job->getSalaryPeriod('salary_period')}}</strong>
                                </div>
                                @endif
                                <strong><i class="fas fa-map-marker-alt"></i> {{$job->getCity('city')}}</strong>
                                <div class="jobcompany">
                                    <div class="ftjobcomp">
                                        <span>{{__('Applied')}}: {{$appliedjob->created_at->format('M d, Y')}}</span>
                                        <a href="{{route('company.detail', $company->slug)}}" title="{{$company->name}}">{{$company->name}}</a>
                                        @include('components.verified-badge', ['company' => $company])
                                    </div>
                                    <a href="{{route('company.detail', $company->slug)}}" class="company-logo" title="{{$company->name}}">{{$company->printCompanyImage()}}</a>
                                </div>
                            </div>
                        </li>
                        @endif
                        @endforeach
                    </ul>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $appliedJobs->links() }}
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{__('No applied jobs found')}}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection
@push('scripts')
@include('includes.immediate_available_btn')
@endpush
