@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->


@include('includes.inner_top_search')
@include('flash::message')

	@php
	$company = $job->getCompany();
	$applyType = $job->getEffectiveApplyType();
	$applyActionUrl = $job->getApplyActionUrl();
	$isExternalApply = $applyType !== 'internal' && $applyActionUrl;
    $phoneApplyNumber = $applyType === 'phone' && $applyActionUrl ? preg_replace('/^tel:/i', '', $applyActionUrl) : null;
    $salaryCurrency = trim((string) $job->salary_currency);
    $salaryPrefix = $salaryCurrency === 'CAD' ? 'CAD $' : ($salaryCurrency !== '' ? $salaryCurrency . ' ' : '$');
	@endphp






<div class="listpgWraper">
    <div class="container">
        @include('flash::message')


        <div class="row jobPagetitle">
            <div class="col-lg-8">
                <div class="jobinfo @if($job->isPromotionHighlightedActive()) job-detail-highlight-wrap @endif">
                    <h2>{{$job->title}}</h2>
                    @if($job->isPromotionUrgentActive() || $job->isPromotionFeaturedActive() || $job->isPromotionHighlightedActive())
                    <div class="mb-2">
                        @if($job->isPromotionUrgentActive())<span class="badge bg-danger me-1"><i class="fas fa-fire"></i> {{__('Urgent')}}</span>@endif
                        @if($job->isPromotionFeaturedActive())<span class="badge bg-warning text-dark me-1"><i class="fas fa-bolt"></i> {{__('Featured')}}</span>@endif
                        @if($job->isPromotionHighlightedActive())<span class="badge bg-info text-dark">{{__('Highlighted')}}</span>@endif
                    </div>
                    @endif
                    <div class="ptext">{{__('Date Posted')}}: {{$job->created_at->format('M d, Y')}}</div>

	                    @if(!(bool)$job->hide_salary)
	                    <div class="salary">{{$job->getSalaryPeriod('salary_period')}}: <strong>{{$salaryPrefix.$job->salary_from}} - {{$salaryPrefix.$job->salary_to}}</strong></div>
	                    @endif
                </div>
            </div>
            <div class="col-lg-4">

                <div class="jobButtons applybox">
	                    @if($job->isJobExpired())
	                    <span class="jbexpire"><i class="fas fa-paper-plane" aria-hidden="true"></i> {{__('Job is expired')}}</span>
	                    @elseif($phoneApplyNumber)
	                    <span class="btn apply"><i class="fas fa-phone" aria-hidden="true"></i> {{ $phoneApplyNumber }}</span>
	                    @elseif($isExternalApply)
                    <a href="{{ $applyActionUrl }}" class="btn apply" @if($applyType === 'external') target="_blank" rel="noopener noreferrer" @endif>
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> {{__('Apply Now')}}
                    </a>
                    @elseif(Auth::check() && Auth::user()->isAppliedOnJob($job->id))
                    <a href="javascript:;" class="btn apply applied"><i class="fas fa-paper-plane" aria-hidden="true"></i> {{__('Already Applied')}}</a>
                    @else
                    @if(!Auth::check())
                    <a href="{{route('login')}}" class="btn apply"><i class="fas fa-paper-plane" aria-hidden="true"></i> {{__('Apply Now')}}</a>
                    @else
                    @php
                    $user = Auth::user();
                    // Check if user has already applied for this job
                    $hasApplied = \App\JobApply::where('job_id', $job->id)
                    ->where('user_id', $user->id)
                    ->exists();
                    @endphp

                    @if($hasApplied)
                    <button type="button" class="btn apply" disabled style="background: #6c757d; cursor: not-allowed;">
                        <i class="fas fa-check-circle" aria-hidden="true"></i> {{__('Already Applied')}}
                    </button>
                    @else
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#applyJobModal" class="btn apply">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> {{__('Apply Now')}}
                    </a>
                    @endif
                    @endif
                    @endif

                </div>

            </div>
        </div>




        <!-- Job Detail start -->
        <div class="row">
            <div class="col-lg-7">

                <!-- Job Header start -->
                <div class="job-header">


                    <!-- Job Detail start -->
                    <div class="jobmainreq">
                        <div class="jobdetail">
                            <h3><i class="fa fa-align-left" aria-hidden="true"></i> {{__('Job Detail')}}</h3>


                            <ul class="jbdetail row">
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">location_on</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Location')}}</strong>
                                            @if((bool)$job->is_freelance)
                                            <span>Freelance</span>
                                            @else
                                            <span>{{$job->getLocation()}}</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>

                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">desktop_windows</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Job Type')}}:</strong>
                                            <span>{{$job->getJobType('job_type')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">domain</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Facility Type')}}:</strong>
                                            <span>{{$job->getIndustry('industry')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">schedule</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Shift')}}:</strong>
                                            <span>{{$job->getJobShift('job_shift')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">analytics</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Career Level')}}:</strong>
                                            <span>{{$job->getCareerLevel('career_level')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">group</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Positions')}}:</strong>
                                            <span>{{$job->num_of_positions}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">calendar_view_day</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Experience')}}:</strong>
                                            <span>{{$job->getJobExperience('job_experience')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">male</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Gender')}}:</strong>
                                            <span>{{$job->getGender('gender')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">school</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Degree')}}:</strong>
                                            <span>{{$job->getDegreeLevel('degree_level')}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="col-lg-3 col-md-6 col-6">
                                    <div class="jbitlist">
                                        <span class="material-symbols-outlined">calendar_month</span>
                                        <div class="jbitdata">
                                            <strong>{{__('Application Deadline')}}:</strong>
                                            <span>{{ \Carbon\Carbon::parse($job->expiry_date)->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </li>

                            </ul>



                        </div>
                    </div>


                    <div class="jobButtons">
                        <a href="{{route('email.to.friend', $job->slug)}}" class="btn"><i class="fas fa-envelope" aria-hidden="true"></i> {{__('Email to Friend')}}</a>
                        @if(Auth::check() && Auth::user()->isFavouriteJob($job->slug)) <a href="{{route('remove.from.favourite', $job->slug)}}" class="btn"><i class="fas fa-floppy" aria-hidden="true"></i> {{__('Remove From Favourite Job')}} <i class="fas fa-times"></i></a> @else <a href="{{route('add.to.favourite', $job->slug)}}" class="btn"><i class="fas fa-floppy"></i> {{__('Add to Favourite')}}</a> @endif
                        <a href="{{route('report.abuse', $job->slug)}}" class="btn report"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> {{__('Report Abuse')}}</a>
                    </div>
                </div>



                <!-- Job Description start -->
                <div class="job-header">
                    <div class="contentbox">
                        <h3><i class="fas fa-file-text" aria-hidden="true"></i> {{__('Job Description')}}</h3>
                        <p>{!! $job->description !!}</p>
                    </div>
                </div>

                @if (!empty($job->benefits))
                <div class="job-header benefits">
                    <div class="contentbox">
                        <h3><i class="fa fa-file-text" aria-hidden="true"></i> {{__('Benefits')}}</h3>
                        <p>{!! $job->benefits !!}</p>
                    </div>
                </div>
                @endif

                <div class="job-header">
                    <div class="contentbox">
                        <h3><i class="fas fa-puzzle-piece" aria-hidden="true"></i> {{__('Skills Required')}}</h3>
                        <ul class="skillslist">
                            {!!$job->getJobSkillsList()!!}
                        </ul>
                    </div>
                </div>


                <!-- Job Description end -->


            </div>
            <!-- related jobs end -->

            <div class="col-lg-5">



                <div class="companyinfo">
                    <h3><i class="fas fa-building" aria-hidden="true"></i> {{__('Company Overview')}}</h3>
                    <div class="companylogo"><a href="{{route('company.detail',$company->slug)}}">{{$company->printCompanyImage()}}</a></div>
                    <div class="title"><a href="{{route('company.detail',$company->slug)}}">{{$company->name}}</a> @include('components.verified-badge', ['company' => $company])</div>
                    <div class="ptext">{{$company->getLocation()}}</div>
                    <div class="opening">
                        <a href="{{route('company.detail',$company->slug)}}">
                            {{App\Company::countNumJobs('company_id', $company->id)}} {{__('Current Jobs Openings')}}
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="companyoverview">

                        <p>{{\Illuminate\Support\Str::limit(strip_tags($company->description), 250, '...')}} <a href="{{route('company.detail',$company->slug)}}">Read More</a></p>
                    </div>
                </div>

                <!-- Google Map start -->
                <div class="job-header">
                    <div class="jobdetail">
                        <h3><i class="fas fa-map-marker" aria-hidden="true"></i> {{__('Google Map')}}</h3>
                        <div class="gmap">
                            <iframe src="https://maps.google.it/maps?q={{urlencode(strip_tags($company->map))}}&output=embed" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>




            </div>
        </div>




        <!-- related jobs start -->
        <div class="relatedJobs">
            <h3 class="mb-0">{{__('Related Jobs')}}</h3>
            <ul class="featuredlist row">
                @if(isset($relatedJobs) && count($relatedJobs))
                @foreach($relatedJobs as $relatedJob)
                <?php $relatedJobCompany = $relatedJob->getCompany(); ?>
                @if(null !== $relatedJobCompany)
                <!--Job start-->
                <li class="col-lg-3 col-md-6 @if($relatedJob->isPromotionFeaturedActive()) featured @endif">
                    <div class="jobint @if($relatedJob->isPromotionHighlightedActive()) job-card-highlighted @endif">
                        @if($relatedJob->isPromotionUrgentActive())
                        <span class="promotepof-badge-left" title="{{__('Urgent')}}"><i class="fas fa-fire"></i></span>
                        @endif
                        @if($relatedJob->isPromotionFeaturedActive())
                        <span class="promotepof-badge"><i class="fa fa-bolt" title="{{__('Featured')}}"></i></span>
                        @endif

                        <div class="d-flex">
                            <div class="fticon"><i class="fas fa-briefcase"></i> {{$relatedJob->getJobType('job_type')}}</div>
                        </div>
                        <h4><a href="{{route('job.detail', [$relatedJob->slug])}}" title="{{$relatedJob->title}}">{!! \Illuminate\Support\Str::limit($relatedJob->title, $limit = 20, $end = '...') !!}</a>
                        </h4>
                        @if(!(bool)$relatedJob->hide_salary)
                        <div class="salary mb-2">Salary: <strong>{{$relatedJob->salary_currency.''.$relatedJob->salary_from}} - {{$relatedJob->salary_currency.''.$relatedJob->salary_to}}/{{$relatedJob->getSalaryPeriod('salary_period')}}</strong></div>
                        @endif
                        <strong><i class="fas fa-map-marker-alt"></i> {{$relatedJob->getCity('city')}}</strong>
                        <div class="jobcompany">
                            <div class="ftjobcomp">
                                <span>{{$relatedJob->created_at->format('M d, Y')}}</span>
                                <a href="{{route('company.detail', $relatedJobCompany->slug)}}" title="{{$relatedJobCompany->name}}">{{$relatedJobCompany->name}}</a>
                                @include('components.verified-badge', ['company' => $relatedJobCompany])
                            </div>
                            <a href="{{route('company.detail', $relatedJobCompany->slug)}}" class="company-logo" title="{{$relatedJobCompany->name}}">{{$relatedJobCompany->printCompanyImage()}} </a>
                        </div>
                    </div>
                </li>

                <!--Job end-->
                @endif
                @endforeach
                @else
                <div class="nodatabox">
                    <h4>{{__('There are currently no open positions available.')}}</h4>
                    <div class="viewallbtn mt-2"><a href="{{route('job.list')}}">{{__('Search Jobs')}}</a></div>
                </div>
                @endif

                <!-- Job end -->
            </ul>
        </div>






    </div>



</div>

<!-- Apply Job Modal -->
<div class="modal fade" id="applyJobModal" tabindex="-1" aria-labelledby="applyJobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #17d27c; color: white; border-radius: 12px 12px 0 0; padding: 20px 30px;">
                <h5 class="modal-title" id="applyJobModalLabel" style="font-weight: 600; font-size: 20px;">
                    <i class="fas fa-paper-plane me-2"></i> {{__('Apply for')}} {{$job->title}}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{route('post.job.apply', $job->slug)}}" method="POST" enctype="multipart/form-data" id="applyJobForm">
                @csrf
                <div class="modal-body" style="padding: 30px;">

                    @if(Auth::check())
                    @php
                    $user = Auth::user();
                    $userCvs = $user->profileCvs;
                    @endphp

                    <!-- CV Selection Section -->
                    <div class="mb-4">
                        <h6 class="mb-3" style="font-weight: 600; color: #333; font-size: 16px;">
                            <i class="fas fa-file-alt text-primary me-2"></i>{{__('Select Your Resume')}}
                        </h6>

                        @if($userCvs && $userCvs->count() > 0)
                        <div class="row g-3 mb-3">
                            @foreach($userCvs as $cv)
                            <div class="col-md-6">
                                <div class="cv-card" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 15px; cursor: pointer; transition: all 0.3s;"
                                    onclick="selectCv({{$cv->id}}, this)">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="cv_id" value="{{$cv->id}}" id="cv_{{$cv->id}}" style="margin-right: 12px; width: 18px; height: 18px;">
                                        <div class="flex-grow-1">
                                            <div style="font-weight: 600; color: #333; font-size: 15px;">{{$cv->title ?? 'Resume'}}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-file-pdf text-danger"></i> {{__('Uploaded')}} {{$cv->created_at->diffForHumans()}}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Upload New CV Option -->
                        <div class="upload-new-cv">
                            <label class="d-flex align-items-center" style="cursor: pointer; padding: 15px; border: 2px dashed #ccc; border-radius: 10px; background: #f8f9fa;">
                                <input type="radio" name="cv_option" value="upload" id="upload_new_cv" style="margin-right: 12px; width: 18px; height: 18px;" onchange="toggleCvUpload()">
                                <div>
                                    <strong style="color: #333; font-size: 15px;">{{__('Upload a new resume')}}</strong>
                                    <br><small class="text-muted">{{__('PDF, DOC, DOCX (Max 5MB)')}}</small>
                                </div>
                            </label>
                            <div id="cv_upload_field" style="display: none; margin-top: 15px;">
                                <input type="file" name="cv" id="cv_file" class="form-control" accept=".pdf,.doc,.docx" onchange="showFileName(this)">
                                <div id="file_name_display" class="mt-2 text-muted small"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Cover Letter Section -->
                    <div class="mb-4">
                        <label for="cover_letter" class="form-label" style="font-weight: 600; color: #333; font-size: 16px;">
                            <i class="fas fa-envelope text-success me-2"></i>{{__('Cover Letter')}} <span class="text-muted">({{__('Optional')}})</span>
                        </label>
                        <textarea name="cover_letter" id="cover_letter" class="form-control" rows="6"
                            placeholder="{{__('Tell the employer why you are a great fit for this position...')}}"
                            maxlength="2000"
                            style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; font-size: 14px;"></textarea>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">{{__('Maximum 2000 characters')}}</small>
                            <small class="text-muted"><span id="char_count">0</span> / 2000</small>
                        </div>
                    </div>

                    <!-- Additional Questions -->
                    @if($job->jobQuestions && $job->jobQuestions->count() > 0)
                    <div class="mb-4">
                        <h6 class="mb-3" style="font-weight: 600; color: #333; font-size: 16px;">
                            <i class="fas fa-question-circle text-warning me-2"></i>{{__('Additional Questions')}}
                        </h6>
                        @foreach($job->jobQuestions as $question)
                        <div class="mb-3">
                            <label class="form-label" style="font-weight: 500; color: #444; font-size: 14px;">
                                {{ $loop->iteration }}. {{ $question->question_title }}
                            </label>
                            <textarea name="question_answers[{{ $question->id }}]"
                                      class="form-control"
                                      rows="2"
                                      maxlength="1000"
                                      style="border: 1px solid #ddd; border-radius: 8px; padding: 10px; font-size: 14px;"
                                      placeholder="{{__('Your answer...')}}"></textarea>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Terms and Conditions -->
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required style="width: 18px; height: 18px;">
                        <label class="form-check-label ms-2" for="terms" style="color: #666; font-size: 14px;">
                            {{__('I agree to the')}} <a href="{{ route('cms', ['slug' => 'terms-of-use']) }}" target="_blank">{{__('Terms and Conditions')}}</a>
                        </label>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{__('Please')}} <a href="{{route('login')}}">{{__('login')}}</a> {{__('to apply for this job')}}
                    </div>
                    @endif
                </div>

                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 20px 30px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 6px;">
                        {{__('Cancel')}}
                    </button>
                    @if(Auth::check())
                    <button type="submit" class="btn btn-primary" style="padding: 10px 30px; border-radius: 6px; background: #17d27c; border: none;">
                        <i class="fas fa-paper-plane me-2"></i>{{__('Submit Application')}}
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection
@push('styles')
<style type="text/css">
    .view_more {
        display: none !important;
    }

    .job-detail-highlight-wrap {
        background: linear-gradient(135deg, #fffbeb 0%, #fff 40%);
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #fcd34d;
    }

    .job-card-highlighted {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
        border: 1px solid #f59e0b !important;
        box-shadow: 0 2px 12px rgba(245, 158, 11, 0.12);
    }

    .promotepof-badge.job-urgent-badge {
        background: #dc2626 !important;
        left: 10px;
        right: auto;
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
                $(this).css('height', 100);
                $(this).css('overflow', 'hidden');
                //alert($( this ).next());
                $(this).next().removeClass('view_more');
            }
        });
    });

    // CV Selection
    function selectCv(cvId, element) {
        // Remove active state from all cards
        document.querySelectorAll('.cv-card').forEach(card => {
            card.style.borderColor = '#e0e0e0';
            card.style.background = '#fff';
        });

        // Add active state to selected card
        element.style.borderColor = '#667eea';
        element.style.background = '#f8f9ff';

        // Check the radio button
        document.getElementById('cv_' + cvId).checked = true;

        // Uncheck upload option
        document.getElementById('upload_new_cv').checked = false;
        document.getElementById('cv_upload_field').style.display = 'none';
    }

    // Toggle CV Upload Field
    function toggleCvUpload() {
        const uploadField = document.getElementById('cv_upload_field');
        const isChecked = document.getElementById('upload_new_cv').checked;

        if (isChecked) {
            uploadField.style.display = 'block';
            // Uncheck all CV selections
            document.querySelectorAll('input[name="cv_id"]').forEach(radio => {
                radio.checked = false;
            });
            document.querySelectorAll('.cv-card').forEach(card => {
                card.style.borderColor = '#e0e0e0';
                card.style.background = '#fff';
            });
        } else {
            uploadField.style.display = 'none';
        }
    }

    // Show selected file name
    function showFileName(input) {
        const display = document.getElementById('file_name_display');
        if (input.files && input.files[0]) {
            display.innerHTML = '<i class="fas fa-file-pdf text-danger me-2"></i>' + input.files[0].name;
        }
    }

    // Character counter for cover letter
    document.addEventListener('DOMContentLoaded', function() {
        const coverLetter = document.getElementById('cover_letter');
        const charCount = document.getElementById('char_count');

        if (coverLetter && charCount) {
            coverLetter.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
    });
</script>

@endpush
