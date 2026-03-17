@extends('layouts.app')
@section('content')
    <!-- Header start -->
    @include('includes.header')
    <!-- Header end -->


    @include('flash::message')
    <form action="{{ route('job.seeker.list') }}" method="get">
        <!-- Page Title start -->
        <div class="pageSearch">
            <div class="container">

                <div class="row justify-content-center">
                    <div class="col-lg-4">
                        <h3>{{ __('Find Candidates') }}</h3>
                    </div>
                    <div class="col-lg-8">
                        <div class="searchform">
                            <div class="input-group">
                                <input type="text" name="search" value="{{ Request::get('search', '') }}"
                                    class="form-control" placeholder="{{ __('Enter Skills or job seeker details') }}" />
                                {!! Form::select(
                                    'functional_area_id[]',
                                    ['' => __('Select Functional Area')] + $functionalAreas,
                                    Request::get('functional_area_id', null),
                                    ['class' => 'form-control', 'id' => 'functional_area_id'],
                                ) !!}

                                <button type="submit" class="btn"><i class="fa fa-search"
                                        aria-hidden="true"></i></button>

                            </div>
                        </div>




                    </div>
                </div>
            </div>
        </div>
        <!-- Page Title end -->
    </form>




    <div class="listpgWraper">
        <div class="container">

            <form action="{{ route('job.seeker.list') }}" method="get">


                <!-- Search Result and sidebar start -->
                <div class="row">
                    <div class="col-lg-3">

                        @include('includes.job_seeker_list_side_bar')

                    </div>

                    <div class="col-lg-9">
                        <!-- Search List -->
                        <ul class="userlisting row">
                            <!-- job start -->
                            @if (isset($jobSeekers) && count($jobSeekers))
                                @foreach ($jobSeekers as $jobSeeker)
                                    <li class="col-lg-12">
                                        @php
                                            $isPromoted = $jobSeeker->is_resume_promoted && 
                                                         $jobSeeker->promotion_end_date && 
                                                         \Carbon\Carbon::parse($jobSeeker->promotion_end_date)->isFuture();
                                        @endphp
                                        <div class="seekerbox"
                                            style="padding: 20px; margin-bottom: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background: #fff; @if($isPromoted) border: 2px solid #ffc107; background: linear-gradient(135deg, #fff9e6 0%, #fff 100%); @endif">
                                            @if($isPromoted)
                                                <div class="ribbon ribbon-top-left" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);"><span><i class="fas fa-rocket"></i>
                                                        Promoted</span></div>
                                            @elseif ($jobSeeker->is_featured)
                                                <div class="ribbon ribbon-top-left"><span><i class="fas fa-star"></i>
                                                        Featured</span></div>
                                            @endif

                                            @php
                                                $isUnlocked = false;
                                                if (Auth::guard('company')->check()) {
                                                    $isUnlocked = Gate::forUser(
                                                        Auth::guard('company')->user(),
                                                        'company',
                                                    )->allows('view-full-resume', $jobSeeker);
                                                }
                                            @endphp

                                            <div class="row align-items-center">
                                                {{-- Main Content Area --}}
                                                <div class="col-md-10">
                                                    @if (Auth::guard('company')->check() && !$isUnlocked)
                                                        {{-- Compact Resume Preview - At least 1 experience, 1 education, or skills --}}
                                                        @php
                                                            $experiences = $jobSeeker->profileExperience ?? collect();
                                                            $educations = $jobSeeker->profileEducation ?? collect();
                                                            $skills = $jobSeeker->profileSkills ?? collect();
                                                            $summaryText = optional($jobSeeker->profileSummary->first())->summary ?? $jobSeeker->getProfileSummary('summary') ?? '';
                                                            $hasPartial = $experiences->count() > 0 || $educations->count() > 0 || $skills->count() > 0 || !empty($summaryText);
                                                        @endphp

                                                        {{-- Name and Location --}}
                                                        <div class="mb-2">
                                                            <h5 class="mb-1" style="font-weight: 600; font-size: 16px; color: #2557a7;">
                                                                {{ __('No Name') }} • {{ $jobSeeker->getCity('city') }}, {{ $jobSeeker->getState('state') }}
                                                            </h5>
                                                        </div>

                                                        {{-- About Me (profile summary, ~50 words) --}}
                                                        @php
                                                            $summaryPreview = $summaryText ? \Illuminate\Support\Str::words(strip_tags($summaryText), 50) : '';
                                                        @endphp
                                                        @if ($summaryPreview)
                                                            <div class="mb-2">
                                                                <strong style="font-size: 14px; color: #2d2d2d;">{{ __('About Me') }}</strong>
                                                                <div style="color: #2d2d2d; font-size: 14px; margin-top: 3px; line-height: 1.5;">
                                                                    {{ $summaryPreview }}
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Relevant Work Experience (1 entry) --}}
                                                        @if ($experiences->count() > 0)
                                                            <div class="mb-2">
                                                                <strong style="font-size: 14px; color: #2d2d2d;">{{ __('Relevant Work Experience') }}</strong>
                                                                <ul style="margin: 3px 0 0 0; padding-left: 20px; list-style: disc;">
                                                                    @foreach ($experiences->take(1) as $exp)
                                                                        <li style="color: #2d2d2d; font-size: 14px; line-height: 1.5;">
                                                                            <strong>{{ $exp->title ?? __('Position') }}</strong>
                                                                            <br>
                                                                            <span style="color: #666;">
                                                                                {{ $exp->company ?? '' }}@if ($exp->date_start), {{ \Carbon\Carbon::parse($exp->date_start)->format('M Y') }} - @if ($exp->is_currently_working ?? false){{ __('Present') }}@elseif(!empty($exp->date_end)){{ \Carbon\Carbon::parse($exp->date_end)->format('M Y') }}@endif @endif
                                                                            </span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif

                                                        {{-- Education (1 entry) - degree_level from relationship --}}
                                                        @if ($educations->count() > 0)
                                                            <div class="mb-2">
                                                                <strong style="font-size: 14px; color: #2d2d2d;">{{ __('Education') }}</strong>
                                                                <div style="color: #2d2d2d; font-size: 14px; margin-top: 3px; line-height: 1.5;">
                                                                    @foreach ($educations->take(1) as $edu)
                                                                        @php
                                                                            $parts = array_filter([
                                                                                $edu->getDegreeLevel('degree_level'),
                                                                                $edu->degree_title,
                                                                                $edu->institution,
                                                                            ]);
                                                                        @endphp
                                                                        {{ implode(', ', $parts) }}
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Licences and Certifications / Skills - from JobSkill relationship --}}
                                                        @if ($skills->count() > 0)
                                                            <div class="mb-0">
                                                                <strong style="font-size: 14px; color: #2d2d2d;">{{ __('Licences and certifications') }}</strong>
                                                                <div style="margin-top: 3px;">
                                                                    @foreach ($skills->take(3) as $cert)
                                                                        @php $skillName = $cert->jobSkill->job_skill ?? $cert->getJobSkill('job_skill') ?? ''; @endphp
                                                                        @if($skillName)
                                                                            <span style="color: #2d2d2d; font-size: 14px;">{{ $skillName }}</span>@if (!$loop->last), @endif
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Fallback: show functional area & career level when no experience/education/skills --}}
                                                        @if (!$hasPartial)
                                                            <div class="mb-0">
                                                                <div style="color: #2d2d2d; font-size: 14px;">
                                                                    {{ $jobSeeker->getFunctionalArea('functional_area') ?: '' }}{{ ($jobSeeker->getFunctionalArea('functional_area') && $jobSeeker->getCareerLevel('career_level')) ? ' • ' : '' }}{{ $jobSeeker->getCareerLevel('career_level') ?? '' }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        {{-- Show normal info for unlocked or non-company users --}}
                                                        <div class="hmcate justify-content-center" title="Job Title">
                                                            {{ $jobSeeker->getFunctionalArea('functional_area') }}</div>
                                                        <div class="hmcate justify-content-center" title="Career Level"><i
                                                                class="fas fa-chart-line"></i>
                                                            {{ $jobSeeker->getCareerLevel('career_level') }}</div>
                                                        <div class="hmcate justify-content-center"><i
                                                                class="fas fa-map-marker-alt"></i>
                                                            {{ $jobSeeker->getCity('city') }}</div>
                                                    @endif
                                                </div>

                                                {{-- Right side: View Details Button --}}
                                                <div class="col-md-2 text-end d-flex align-items-center justify-content-center">
                                                    @if (Auth::user() || (!Auth::user() && !Auth::guard('company')->user()))
                                                        <a href="javascript:void();" data-bs-toggle="modal"
                                                            data-bs-target="#hireme"
                                                            class="btn btn-outline-primary">{{ __('View Profile') }}</a>
                                                    @else
                                                        @if (!$isUnlocked)
                                                            <a href="{{ route('company.packages') }}"
                                                                class="btn btn-primary"
                                                                style="background: #2557a7; border: none; padding: 10px 20px; border-radius: 6px; white-space: nowrap; font-size: 14px;">
                                                                <i class="fas fa-lock me-1"></i>
                                                                {{ __('View all details') }}
                                                            </a>
                                                        @else
                                                            <a href="{{ route('user.profile', $jobSeeker->id) }}"
                                                                class="btn btn-success">
                                                                <i class="fas fa-eye me-2"></i> {{ __('View Profile') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                        </ul>

                        <!-- Pagination Start -->
                        <div class="pagiWrap">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="showreslt">
                                        {{ __('Showing Pages') }} : {{ $jobSeekers->firstItem() }} -
                                        {{ $jobSeekers->lastItem() }} {{ __('Total') }} {{ $jobSeekers->total() }}
                                    </div>
                                </div>
                                <div class="col-md-7 text-right">
                                    @if (isset($jobSeekers) && count($jobSeekers))
                                        {{ $jobSeekers->appends(request()->query())->links() }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Pagination end -->
                        <div class=""><br />{!! $siteSetting->listing_page_horizontal_ad !!}</div>
                    </div>
                </div>

        </div>
        </form>
    </div>
    </div>



    <!-- Hire Candidate -->
    <div class="modal fade mypremodal" id="hireme" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="modal-body">
                    <div class="preuserinfo">
                        <h3>{{ __('Our users rely on us to keep their information secure.') }}</h3>
                        <p>{{ __('Log in or register as an employer to access candidate details.') }}</p>
                        <a href="{{ url('company-login') }}" class="btn btn-yellow mt-3">{{ __('Login') }}</a>
                        <a href="{{ url('company-register') }}" class="btn btn-dark mt-3">{{ __('Register') }}</a>

                    </div>
                </div>

            </div>
        </div>
    </div>



    @include('includes.footer')
@endsection
@push('styles')
    <style type="text/css">
        .searchList li .jobimg {
            min-height: 80px;
        }

        .hide_vm_ul {
            height: 100px;
            overflow: hidden;
        }

        .hide_vm {
            display: none !important;
        }

        .view_more {
            cursor: pointer;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function($) {
            $("form").submit(function() {
                $(this).find(":input").filter(function() {
                    return !this.value;
                }).attr("disabled", "disabled");
                return true;
            });
            $("form").find(":input").prop("disabled", false);

            $(".view_more_ul").each(function() {
                if ($(this).height() > 100) {
                    $(this).addClass('hide_vm_ul');
                    $(this).next().removeClass('hide_vm');
                }
            });
            $('.view_more').on('click', function(e) {
                e.preventDefault();
                $(this).prev().removeClass('hide_vm_ul');
                $(this).addClass('hide_vm');
            });

        });
    </script>
    @include('includes.country_state_city_js')
@endpush
