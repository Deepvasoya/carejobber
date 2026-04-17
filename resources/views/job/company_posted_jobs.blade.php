@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Inner Page Title start -->
@include('includes.inner_page_title', ['page_title'=>__('Manage Jobs')])
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        <div class="row">
            @include('includes.company_dashboard_menu')

            <div class="col-lg-9">
                <div class="myads">

                    @include('flash::message')

                    <h3>{{__('Manage Jobs')}}</h3>
                    @php
                    $listTab = request('tab', 'active');
                    if (! in_array($listTab, ['active', 'pending', 'expired', 'drafts'], true)) {
                    $listTab = 'active';
                    }
                    @endphp

                    <!-- Tabs start -->
                    <ul class="nav nav-tabs mt-4" id="jobTabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $listTab === 'active' ? 'active' : '' }}" id="active-tab" data-toggle="tab" href="#active-jobs">{{__('Active Jobs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $listTab === 'pending' ? 'active' : '' }}" id="pending-tab" data-toggle="tab" href="#pending-jobs">{{__('Pending Jobs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $listTab === 'expired' ? 'active' : '' }}" id="expired-tab" data-toggle="tab" href="#expired-jobs">{{__('Expired Jobs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $listTab === 'drafts' ? 'active' : '' }}" id="drafts-tab" data-toggle="tab" href="#draft-jobs">{{__('Drafts')}}</a>
                        </li>
                    </ul>
                    <!-- Tabs end -->

                    <div class="tab-content">
                        <!-- Active Jobs start -->
                        <div class="tab-pane fade {{ $listTab === 'active' ? 'show active' : '' }}" id="active-jobs">
                            <ul class="featuredlist row">
                                @if(isset($jobs) && count($jobs))
                                @foreach($jobs as $job)
                                @php
                                $company = $job->getCompany();
                                $appliedUsersCount = $job->appliedUsers->count();
                                @endphp
                                @if(null !== $company && ! $job->is_draft && $job->expiry_date && $job->expiry_date >= now() && $job->is_active == 1)

                                <li class="col-lg-6 col-md-6" id="job_li_{{$job->id}}">
                                    <div class="jobint">

                                        <div class="d-flex">
                                            <div class="fticon"><i class="fas fa-briefcase"></i> {{$job->getJobType('job_type')}}</div>
                                        </div>
                                        <h4><a href="{{route('job.detail', [$job->slug])}}" title="{{$job->title}}">{!! \Illuminate\Support\Str::limit($job->title, $limit = 20, $end = '...') !!}</a>
                                        </h4>
                                        @if(!(bool)$job->hide_salary)
                                        <div class="salary mb-2">Salary: <strong>{{$job->salary_currency.''.$job->salary_from}} - {{$job->salary_currency.''.$job->salary_to}}/{{$job->getSalaryPeriod('salary_period')}}</strong></div>
                                        @endif
                                        <strong><i class="fas fa-map-marker-alt"></i> {{$job->getCity('city')}}</strong>
                                        <span>{{$job->created_at->format('M d, Y')}}</span>
                                        <div class="d-flex mt-3 compjobslinks">
                                            <a class="btn btn-primary me-2" href="{{route('list.applied.users', [$job->id])}}">{{__('Candidates')}}
                                                @if($appliedUsersCount > 0)
                                                <span class="badge bg-white text-dark">{{$appliedUsersCount}}</span>
                                                @else
                                                <span class="badge bg-white text-dark">0</span>
                                                @endif
                                            </a>
                                            <a class="btn btn-warning me-2" href="{{route('edit.front.job', [$job->id])}}"><i class="fas fa-edit"></i></a>
                                            <a class="btn btn-danger me-2" href="javascript:;" onclick="deleteJob({{$job->id}});"><i class="fas fa-trash"></i></a>
                                        </div>

                                        <!-- Job Stats Bar -->
                                        <div class="job-stats-bar">
                                            <div class="job-stat-item">
                                                <i class="fas fa-eye"></i>
                                                <span class="job-stat-label">{{__('Total Visitors')}}:</span>
                                                <span class="job-stat-value">{{$job->num_views ?? 0}}</span>
                                            </div>
                                            <div class="job-stat-item">
                                                <i class="fas fa-users"></i>
                                                <span class="job-stat-label">{{__('Applied Candidates')}}:</span>
                                                <span class="job-stat-value">{{$appliedUsersCount}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @endif
                                @endforeach
                                @else
                                <p>No Active Jobs</p>
                                @endif
                            </ul>
                        </div>
                        <!-- Active Jobs end -->

                        <!-- Pending Jobs start -->
                        <div class="tab-pane fade {{ $listTab === 'pending' ? 'show active' : '' }}" id="pending-jobs">
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> {{__('These jobs are awaiting admin approval before they become active.')}}
                            </div>
                            <ul class="featuredlist row">
                                @if(isset($jobs) && count($jobs))
                                @foreach($jobs as $job)
                                @php
                                $company = $job->getCompany();
                                @endphp
                                @if(null !== $company && ! $job->is_draft && $job->is_active == 0)

                                <li class="col-lg-6 col-md-6" id="job_li_{{$job->id}}">
                                    <div class="jobint" style="opacity: 0.8; border-left: 4px solid #ffc107;">

                                        <div class="d-flex">
                                            <div class="fticon" style="background-color: #ffc107;"><i class="fas fa-clock"></i> {{__('Pending Approval')}}</div>
                                        </div>
                                        <h4><a href="javascript:void(0);" title="{{$job->title}}">{!! \Illuminate\Support\Str::limit($job->title, $limit = 20, $end = '...') !!}</a>
                                        </h4>
                                        @if(!(bool)$job->hide_salary)
                                        <div class="salary mb-2">Salary: <strong>{{$job->salary_currency.''.$job->salary_from}} - {{$job->salary_currency.''.$job->salary_to}}/{{$job->getSalaryPeriod('salary_period')}}</strong></div>
                                        @endif
                                        <strong><i class="fas fa-map-marker-alt"></i> {{$job->getCity('city')}}</strong>
                                        <span>{{$job->created_at->format('M d, Y')}}</span>
                                        <div class="d-flex mt-3 compjobslinks">
                                            <a class="btn btn-warning me-2" href="{{route('edit.front.job', [$job->id])}}"><i class="fas fa-edit"></i> {{__('Edit')}}</a>
                                            <a class="btn btn-danger me-2" href="javascript:;" onclick="deleteJob({{$job->id}});"><i class="fas fa-trash"></i></a>
                                        </div>

                                        <!-- Job Stats Bar -->
                                        <div class="job-stats-bar">
                                            <div class="job-stat-item">
                                                <i class="fas fa-hourglass-half"></i>
                                                <span class="job-stat-label">{{__('Status')}}:</span>
                                                <span class="job-stat-value text-warning">{{__('Awaiting Approval')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                @endif
                                @endforeach
                                @else
                                <p>{{__('No Pending Jobs')}}</p>
                                @endif
                            </ul>
                        </div>
                        <!-- Pending Jobs end -->

                        <!-- Expired Jobs start -->
                        <div class="tab-pane fade {{ $listTab === 'expired' ? 'show active' : '' }}" id="expired-jobs">
                            <ul class="featuredlist row">
                                @if(isset($jobs) && count($jobs))
                                @foreach($jobs as $job)
                                @php
                                $company = $job->getCompany();
                                $appliedUsersCount = $job->appliedUsers->count();
                                @endphp
                                @if(null !== $company && ! $job->is_draft && $job->expiry_date && $job->expiry_date < now() && $job->is_active == 1)

                                    <li class="col-lg-6 col-md-6" id="job_li_{{$job->id}}">
                                        <div class="jobint">

                                            <div class="d-flex">
                                                <div class="fticon"><i class="fas fa-briefcase"></i> {{$job->getJobType('job_type')}}</div>
                                            </div>
                                            <h4><a href="{{route('job.detail', [$job->slug])}}" title="{{$job->title}}">{!! \Illuminate\Support\Str::limit($job->title, $limit = 20, $end = '...') !!}</a>
                                            </h4>
                                            @if(!(bool)$job->hide_salary)
                                            <div class="salary mb-2">Salary: <strong>{{$job->salary_currency.''.$job->salary_from}} - {{$job->salary_currency.''.$job->salary_to}}/{{$job->getSalaryPeriod('salary_period')}}</strong></div>
                                            @endif
                                            <strong><i class="fas fa-map-marker-alt"></i> {{$job->getCity('city')}}</strong>
                                            <span>{{$job->created_at->format('M d, Y')}}</span>
                                            <div class="d-flex mt-3 compjobslinks">
                                                <a class="btn btn-primary me-2" href="{{route('list.applied.users', [$job->id])}}">{{__('Candidates')}}
                                                    @if($appliedUsersCount > 0)
                                                    <span class="badge bg-white text-dark">{{$appliedUsersCount}}</span>
                                                    @else
                                                    <span class="badge bg-white text-dark">0</span>
                                                    @endif
                                                </a>
                                                <a class="btn btn-warning me-2" href="{{route('edit.front.job', [$job->id])}}">Repost</a>
                                                <a class="btn btn-danger me-2" href="javascript:;" onclick="deleteJob({{$job->id}});"><i class="fas fa-trash"></i></a>
                                            </div>

                                            <!-- Job Stats Bar -->
                                            <div class="job-stats-bar">
                                                <div class="job-stat-item">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="job-stat-label">{{__('Total Visitors')}}:</span>
                                                    <span class="job-stat-value">{{$job->num_views ?? 0}}</span>
                                                </div>
                                                <div class="job-stat-item">
                                                    <i class="fas fa-users"></i>
                                                    <span class="job-stat-label">{{__('Applied Candidates')}}:</span>
                                                    <span class="job-stat-value">{{$appliedUsersCount}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>







                                    @endif
                                    @endforeach
                                    @else
                                    <p>No Expired Jobs</p>
                                    @endif
                            </ul>
                        </div>
                        <!-- Expired Jobs end -->

                        <!-- Draft Jobs start -->
                        <div class="tab-pane fade {{ $listTab === 'drafts' ? 'show active' : '' }}" id="draft-jobs">
                            <ul class="featuredlist row">
                                @if(isset($jobs) && count($jobs))
                                @foreach($jobs as $job)
                                @php
                                $company = $job->getCompany();
                                @endphp
                                @if(null !== $company && $job->is_draft)
                                <li class="col-lg-6 col-md-6" id="job_li_{{$job->id}}">
                                    <div class="jobint">
                                        <div class="d-flex">
                                            <div class="fticon"><i class="fas fa-file-alt"></i> {{ __('Draft') }}</div>
                                        </div>
                                        <h4>
                                            <a href="{{ route('edit.front.job', [$job->id]) }}" title="{{ $job->title }}">{!! \Illuminate\Support\Str::limit($job->title ?: __('Untitled'), $limit = 40, $end = '...') !!}</a>
                                        </h4>
                                        <span class="text-muted d-block">{{ $job->updated_at ? $job->updated_at->format('M d, Y') : '' }}</span>
                                        <div class="d-flex mt-3 compjobslinks">
                                            <a class="btn btn-primary me-2" href="{{ route('edit.front.job', [$job->id]) }}">{{ __('Continue editing') }}</a>
                                            <a class="btn btn-danger me-2" href="javascript:;" onclick="deleteJob({{$job->id}});"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                @endforeach
                                @else
                                <p>{{ __('No drafts') }}</p>
                                @endif
                            </ul>
                        </div>
                        <!-- Draft Jobs end -->
                    </div>

                    <!-- Pagination Start -->
                    <div class="pagiWrap mt-4">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="showreslt">
                                    {{__('Showing Jobs')}} : {{ $jobs->firstItem() }} - {{ $jobs->lastItem() }} {{__('Total')}} {{ $jobs->total() }}
                                </div>
                            </div>
                            <div class="col-md-7 text-right">
                                @if(isset($jobs) && count($jobs))
                                {{ $jobs->appends(request()->query())->links() }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Pagination end -->

                </div>
            </div>
        </div>
    </div>
</div>
@include('includes.footer')
@endsection

@push('scripts')
<script type="text/javascript">
    function deleteJob(id) {
        var msg = 'Are you sure?';
        if (confirm(msg)) {
            $.post("{{ route('delete.front.job') }}", {
                    id: id,
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response == 'ok') {
                        $('#job_li_' + id).remove();
                    } else {
                        alert('Request Failed!');
                    }
                });
        }
    }

    $(document).ready(function() {
        // Initialize the tab functionality
        $('#jobTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
@endpush