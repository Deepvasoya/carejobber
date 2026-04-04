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
                                                {{-- <div class="ribbon ribbon-top-left"><span><i class="fas fa-star"></i>
                                                        Featured</span></div> --}}
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
                                                        @php
                                                            $experiences = $jobSeeker->profileExperience ?? collect();
                                                            $educations = $jobSeeker->profileEducation ?? collect();
                                                            $skills = $jobSeeker->profileSkills ?? collect();
                                                            // Latest profile summary (eager: orderByDesc id, limit 1)
                                                            $summaryRow = $jobSeeker->profileSummary->first();
                                                            $summaryText = ($summaryRow && trim(strip_tags((string) $summaryRow->summary)) !== '')
                                                                ? (string) $summaryRow->summary
                                                                : '';
                                                            $summaryPreview = trim($summaryText) !== ''
                                                                ? \Illuminate\Support\Str::words(strip_tags($summaryText), 50, '…')
                                                                : '';
                                                            $expFirst = $experiences->first();
                                                            $eduFirst = $educations->first();

                                                            $fn = trim((string) ($jobSeeker->first_name ?? ''));
                                                            $ln = trim((string) ($jobSeeker->last_name ?? ''));
                                                            $nameParts = array_values(array_filter([$fn, $ln], fn ($p) => $p !== ''));
                                                            if (count($nameParts) === 0 && ! empty($jobSeeker->name)) {
                                                                $nameParts = array_slice(preg_split('/\s+/', trim((string) $jobSeeker->name)) ?: [], 0, 2);
                                                            }
                                                            $maskedNameLabels = [];
                                                            $avatarLetters = '';
                                                            foreach ($nameParts as $p) {
                                                                if ($p === '') {
                                                                    continue;
                                                                }
                                                                $c = mb_strtoupper(mb_substr($p, 0, 1));
                                                                $maskedNameLabels[] = $c . '.';
                                                                $avatarLetters .= $c;
                                                            }
                                                            $maskedName = count($maskedNameLabels) ? implode(' ', $maskedNameLabels) : __('Candidate');
                                                            $avatarLetters = mb_substr($avatarLetters, 0, 2) ?: '?';
                                                        @endphp

                                                        <div class="resume-preview-card">
                                                            <div class="resume-preview-card__header">
                                                                <div class="resume-preview-card__avatar" aria-hidden="true">{{ $avatarLetters }}</div>
                                                                <div class="resume-preview-card__identity">
                                                                    <div class="resume-preview-card__name">{{ $maskedName }}</div>
                                                                    <div class="resume-preview-card__meta">
                                                                        @if ($jobSeeker->getCity('city') || $jobSeeker->getState('state'))
                                                                            <span class="resume-preview-card__pill"><i class="fas fa-map-marker-alt me-1"></i>{{ trim(implode(', ', array_filter([$jobSeeker->getCity('city'), $jobSeeker->getState('state')]))) }}</span>
                                                                        @endif
                                                                        @if ($jobSeeker->getFunctionalArea('functional_area'))
                                                                            <span class="resume-preview-card__pill">{{ $jobSeeker->getFunctionalArea('functional_area') }}</span>
                                                                        @endif
                                                                        @if ($jobSeeker->getCareerLevel('career_level'))
                                                                            <span class="resume-preview-card__pill">{{ $jobSeeker->getCareerLevel('career_level') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="resume-preview-card__section">
                                                                <div class="resume-preview-card__label">{{ __('About me') }}</div>
                                                                <p class="resume-preview-card__text @if($summaryPreview === '') text-muted fst-italic @endif">
                                                                    {{ $summaryPreview !== '' ? $summaryPreview : __('No summary added yet. Unlock full profile to read more.') }}
                                                                </p>
                                                            </div>

                                                            <div class="resume-preview-card__section">
                                                                <div class="resume-preview-card__label">{{ __('Experience') }}</div>
                                                                @if ($expFirst)
                                                                    @php
                                                                        $exp = $expFirst;
                                                                        $expDesc = trim(strip_tags((string) ($exp->description ?? '')));
                                                                        $expDescPreview = $expDesc !== '' ? \Illuminate\Support\Str::words($expDesc, 25, '…') : '';
                                                                    @endphp
                                                                    <div class="resume-preview-card__text">
                                                                        <span class="resume-preview-card__strong">{{ $exp->title ?? __('Position') }}</span>
                                                                        @if (! empty($exp->company))
                                                                            <span class="text-muted"> · {{ $exp->company }}</span>
                                                                        @endif
                                                                    </div>
                                                                    @if ($exp->date_start)
                                                                        <div class="resume-preview-card__sub text-muted small">
                                                                            {{ \Carbon\Carbon::parse($exp->date_start)->format('M Y') }}
                                                                            —
                                                                            @if ($exp->is_currently_working ?? false)
                                                                                {{ __('Present') }}
                                                                            @elseif (! empty($exp->date_end))
                                                                                {{ \Carbon\Carbon::parse($exp->date_end)->format('M Y') }}
                                                                            @else
                                                                                {{ __('Present') }}
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                    @if ($expDescPreview !== '')
                                                                        <p class="resume-preview-card__text resume-preview-card__text--muted small mb-0 mt-1">{{ $expDescPreview }}</p>
                                                                    @endif
                                                                @else
                                                                    <p class="resume-preview-card__text text-muted fst-italic mb-0">{{ __('No work history in preview. Unlock full profile to see experience.') }}</p>
                                                                @endif
                                                            </div>

                                                            <div class="resume-preview-card__section">
                                                                <div class="resume-preview-card__label">{{ __('Education') }}</div>
                                                                @if ($eduFirst)
                                                                    @php
                                                                        $edu = $eduFirst;
                                                                        $eduLine = implode(', ', array_filter([
                                                                            $edu->getDegreeLevel('degree_level'),
                                                                            $edu->degree_title,
                                                                            $edu->institution,
                                                                        ]));
                                                                    @endphp
                                                                    <p class="resume-preview-card__text mb-0">{{ $eduLine !== '' ? $eduLine : __('Education details on file') }}</p>
                                                                @else
                                                                    <p class="resume-preview-card__text text-muted fst-italic mb-0">{{ __('No education in preview. Unlock full profile to see qualifications.') }}</p>
                                                                @endif
                                                            </div>

                                                            <div class="resume-preview-card__section mb-0">
                                                                <div class="resume-preview-card__label">{{ __('Skills & certifications') }}</div>
                                                                @if ($skills->count() > 0)
                                                                    <div class="resume-preview-card__chips">
                                                                        @foreach ($skills->take(6) as $cert)
                                                                            @php $skillName = $cert->jobSkill->job_skill ?? $cert->getJobSkill('job_skill') ?? ''; @endphp
                                                                            @if ($skillName)
                                                                                <span class="resume-preview-card__chip">{{ $skillName }}</span>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <p class="resume-preview-card__text text-muted fst-italic mb-0">{{ __('No skills listed in preview. Unlock full profile to see skills.') }}</p>
                                                                @endif
                                                            </div>

                                                            <p class="resume-preview-card__text text-muted small mb-0 mt-2 pt-2 border-top" style="border-color: #eef1f5 !important;">
                                                                <i class="fas fa-lock me-1" aria-hidden="true"></i>{{ __('Unlock full profile for complete resume, documents, and contact details.') }}
                                                            </p>
                                                        </div>
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
                                                            <a href="{{ route('resume.unlock.page', $jobSeeker->id) }}"
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

        /* Locked resume preview — job-seekers listing */
        .resume-preview-card {
            border: 1px solid #e8ecf1;
            border-radius: 12px;
            background: linear-gradient(180deg, #fafbfd 0%, #fff 48px);
            padding: 1rem 1.25rem 1.1rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .resume-preview-card__header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eef1f5;
        }
        .resume-preview-card__avatar {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2557a7 0%, #1a4480 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(37, 87, 167, 0.25);
        }
        .resume-preview-card__identity { min-width: 0; flex: 1; }
        .resume-preview-card__name {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.25;
        }
        .resume-preview-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem 0.5rem;
            margin-top: 0.5rem;
        }
        .resume-preview-card__pill {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            color: #475569;
            background: #f1f5f9;
            border-radius: 999px;
            padding: 0.2rem 0.65rem;
            max-width: 100%;
        }
        .resume-preview-card__section {
            margin-bottom: 0.85rem;
        }
        .resume-preview-card__label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 0.35rem;
        }
        .resume-preview-card__text {
            font-size: 0.9375rem;
            line-height: 1.55;
            color: #334155;
            margin-bottom: 0;
        }
        .resume-preview-card__text--muted { color: #64748b; }
        .resume-preview-card__strong { font-weight: 600; color: #0f172a; }
        .resume-preview-card__sub { margin-top: 0.15rem; }
        .resume-preview-card__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }
        .resume-preview-card__chip {
            font-size: 0.8125rem;
            color: #1e3a5f;
            background: #e8f0fa;
            border: 1px solid #d3e4f7;
            border-radius: 6px;
            padding: 0.2rem 0.55rem;
            font-weight: 500;
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
