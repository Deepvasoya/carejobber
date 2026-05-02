@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Inner Page Title start -->
<!-- @include('includes.inner_page_title', ['page_title' => __($user->name . "'s Profile")]) -->

<?php $true = FALSE; ?>

<?php
if (Auth::guard('company')->user()) {
    $package = Auth::guard('company')->user();
    if (null !== ($package)) {
        $array_ids = explode(',', $package->availed_cvs_ids);
        if (in_array($user->id, $array_ids)) {
            $true = TRUE;
        }
    }
} elseif (auth()->check() && auth()->user()->id == $user->id) {
    $true = TRUE;
}
?>
<!-- Inner Page Title end -->
<div class="listpgWraper">
    <div class="container-fluid">
        @include('flash::message')










        <!-- Job Detail start -->
        <div class="row">
            <div class="col-md-8">
                <div class="usercoverimg">

                    {{$user->printUserCoverImage()}}

                </div>

                <div class="userMaininfo">
                    <div class="userPic">{{$user->printUserImage()}} </div>
                    <div class="title">
                        @if($true == TRUE)
                        <h3>{{$user->getName()}} <span>({{$user->getFunctionalArea('functional_area')}})</span></h3>
                        @else
                        <h3>{{__('Candidate')}} <span>({{$user->getFunctionalArea('functional_area')}})</span></h3>
                        @endif
                        <div class="redyto">
                            @if((bool)$user->is_immediate_available)
                            <span><i class="fas fa-laptop"></i> {{__('Ready for Hire')}}</span>
                            @endif
                        </div>
                        <div class="desi"><i class="fa fa-map-marker" aria-hidden="true"></i> {{$user->getLocation()}}</div>

                        <div class="membersinc"><i class="fa fa-history" aria-hidden="true"></i> {{__('Member Since')}}, {{$user->created_at->format('M d, Y')}}</div>

                    </div>



                </div>


                <?php
                $true = FALSE;
                $companyUser = Auth::guard('company')->user();

                // Check if the user's profile is complete
                $isProfileComplete = !empty(optional($profileCv)->cv_file) && !empty($user->email) && !empty($user->phone);

                if ($companyUser) {
                    // Check via Gate (includes new ResumeUnlock + old credits + CV package quota)
                    $true = Gate::forUser($companyUser, 'company')->allows('view-full-resume', $user);
                } elseif (auth()->check() && !Auth::guard('company')->check()) {
                    // Regular user viewing their own profile
                    if (auth()->user()->id == $user->id) {
                        $true = TRUE;
                    }
                }
                ?>

                <!-- Buttons -->
                <div class="userlinkstp">
                    <?php if ($true == TRUE) { ?>

                        @if(isset($job) && isset($company))
                        @if(Auth::guard('company')->check() && Auth::guard('company')->user()->isHiredApplicant($user->id, $job->id, $company->id))
                        <a href="{{route('remove.hire.from.favourite.applicant', [$job_application->id, $user->id, $job->id, $company->id])}}" class="btn">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> {{__('Remove From Hired List')}}
                        </a>
                        @else
                        @if(Auth::guard('company')->check() && Auth::guard('company')->user()->isFavouriteApplicant($user->id, $job->id, $company->id))
                        <a href="{{route('remove.from.favourite.applicant', [$job_application->id, $user->id, $job->id, $company->id])}}" class="btn">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> {{__('Shortlisted')}}
                        </a>
                        @if(isset($is_applicant))
                        <a style="color:#fff" href="{{route('reject.applicant.profile', [$job_application->id])}}" class="btn btn-warning">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> {{__('Reject')}}
                        </a>
                        @endif
                        @else
                        <a href="{{route('add.to.favourite.applicant', [$job_application->id, $user->id, $job->id, $company->id])}}" class="btn">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> {{__('Shortlist')}}
                        </a>
                        @endif

                        <a href="{{route('hire.from.favourite.applicant', [$job_application->id, $user->id, $job->id, $company->id])}}" class="btn">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> {{__('Hire This Candidate')}}
                        </a>
                        @endif
                        @endif

                        @if(null !== $profileCv)
                        <a href="{{ asset('cvs/'.$profileCv->cv_file) }}"
                            download="{{ $profileCv->title . '.' . pathinfo($profileCv->cv_file, PATHINFO_EXTENSION) }}"
                            class="btn">
                            <i class="fa fa-download" aria-hidden="true"></i> {{__('Download CV')}}
                        </a>
                        @endif
                    <?php } ?>

                    {{-- Start Chat button - Only for unlocked profiles --}}
                    <?php if ($true == TRUE) { ?>
                        @if(Auth::guard('company')->check())
                        <button class="btn btn-primary start-chat-btn" data-user-id="{{$user->id}}" data-user-name="{{$user->getName()}}" title="{{__('Start Chat')}}">
                            <i class="fas fa-comments"></i> {{__('Start Chat')}}
                        </button>
                        @endif
                    <?php } ?>



                    @if(Auth::guard('company')->user())
                    @if($true == FALSE)
                    @if(!$isProfileComplete)
                    <p style="color: red;">{{ __('Candidate profile is not completed, so you can\'t unlock it.') }}</p>
                    @else
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#resumeUnlockModal">
                            <i class="fa fa-unlock-alt me-2" aria-hidden="true"></i> {{__('Unlock Full Profile')}}
                        </button>
                        <a href="{{ route('resume.unlock.page', $user->id) }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-info-circle me-2"></i> {{__('View Pricing')}}
                        </a>
                    </div>
                    <div class="mt-2">
                        <span class="text-muted d-block">{{ __('One-time payment • Lifetime access • Secure checkout') }}</span>
                        <p class="small text-muted mt-1 mb-0">{{ __('For CV search packages, apply your coupon on') }} <a href="{{ route('company.packages') }}">{{ __('CV search packages') }}</a> {{ __('before Stripe checkout.') }}</p>
                    </div>
                    @endif
                    @endif
                    @endif
                </div>










                <!-- Unlock Pricing Card (Inline) -->
                @if(Auth::guard('company')->user() && $true == FALSE && $isProfileComplete)
                <div class="card shadow-lg border-0 mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-5 text-white">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h3 class="fw-bold mb-3">
                                    <i class="fas fa-unlock-alt me-2"></i> {{__('Unlock This Resume')}}
                                </h3>
                                <p class="mb-3 opacity-90">{{__('Get instant access to complete candidate profile including:')}}</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><i class="fas fa-check-circle me-2"></i> {{__('Full contact details (email, phone)')}}</li>
                                    <li class="mb-2"><i class="fas fa-check-circle me-2"></i> {{__('Complete work experience & education')}}</li>
                                    <li class="mb-2"><i class="fas fa-check-circle me-2"></i> {{__('All skills, languages & certifications')}}</li>
                                    <li class="mb-2"><i class="fas fa-check-circle me-2"></i> {{__('CV download & lifetime access')}}</li>
                                </ul>
                            </div>
                            <div class="col-md-5 text-center">
                                <div class="bg-white text-dark p-4 rounded-3 shadow">
                                    <div class="display-5 fw-bold text-primary mb-2">
                                        {{ config('app.resume_unlock_currency', 'CAD') }}${{ number_format(config('app.resume_unlock_price', 10.00), 2) }}
                                    </div>
                                    <p class="text-muted small mb-3">{{__('One-time payment')}}</p>
                                    <button type="button" class="btn btn-primary btn-lg w-100 mb-2" data-bs-toggle="modal" data-bs-target="#resumeUnlockModal" style="border-radius: 8px;">
                                        <i class="fas fa-unlock-alt me-2"></i> {{__('Unlock Now')}}
                                    </button>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-shield-alt me-1"></i> {{__('Secure payment via Stripe')}}
                                    </small>

                                    @if(Auth::guard('company')->user()->getRemainingCvsQuota() > 0)
                                    <div class="mt-3 pt-3 border-top">
                                        <a href="{{ route('company.unlock', $user->id) }}" class="text-success text-decoration-none d-block">
                                            <small>
                                                <i class="fas fa-coins me-1"></i>
                                                {{__('Or use 1 of your')}} <strong>{{ Auth::guard('company')->user()->getRemainingCvsQuota() }}</strong> {{__('credits')}}
                                            </small>
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- About Employee start -->
                @if($true == TRUE)
                <div class="userdetailbox">
                    <h3>{{__('About me')}}</h3>
                    <p>{{$user->getProfileSummary('summary')}}</p>
                </div>
                @endif

                <div class="userdetailbox">
                    <h3>{{__('Skills')}}</h3>
                    <div id="skill_div"></div>
                </div>

                <div class="userdetailbox">
                    <h3>{{__('Languages')}}</h3>
                    <div id="language_div"></div>
                </div>

                <!-- Experience start (Always visible - partial data) -->
                <div class="userdetailbox">
                    <h3>{{__('Experience')}}</h3>
                    @if($true == FALSE)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{__('Partial preview only. Unlock to view full details.')}}
                    </div>
                    @endif
                    <div class="" id="experience_div"></div>
                </div>

                <!-- Education start (Always visible - partial data) -->
                <div class="userdetailbox">
                    <h3>{{__('Education')}}</h3>
                    @if($true == FALSE)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{__('Partial preview only. Unlock to view full details.')}}
                    </div>
                    @endif
                    <div class="" id="education_div"></div>
                </div>

                <!-- Portfolio start (Only for unlocked) -->
                @if($true == TRUE)
                <div class="userdetailbox profileproject">
                    <h3>{{__('Portfolio')}}</h3>
                    <div class="" id="projects_div"></div>
                </div>
                @endif





            </div>
            <div class="col-md-4">
                <?php if ($true == TRUE) { ?>
                    <!-- Candidate Contact -->
                    <div class="job-header">
                        <div class="jobdetail">
                            <h3>{{__('Contact Information')}}</h3>
                            <div class="candidateinfo">
                                @if(!empty($user->phone))
                                <div class="loctext"><i class="fa fa-phone" aria-hidden="true"></i> <a href="tel:{{$user->phone}}">{{$user->phone}}</a></div>
                                @endif
                                @if(!empty($user->mobile_num))
                                <div class="loctext"><i class="fa fa-mobile" aria-hidden="true"></i> <a href="tel:{{$user->mobile_num}}">{{$user->mobile_num}}</a></div>
                                @endif
                                @if(!empty($user->email))
                                <div class="loctext"><i class="fa fa-envelope" aria-hidden="true"></i> <a href="mailto:{{$user->email}}">{{$user->email}}</a></div>
                                @endif
                                <div class="loctext"><i class="fa fa-map-marker" aria-hidden="true"></i> {{$user->street_address}}</div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Candidate Detail start -->
                <div class="job-header">
                    <div class="jobdetail">
                        <h3>{{__('Candidate Details')}}</h3>
                        <ul class="jbdetail row">
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">verified</span>
                                    <div class="jbitdata">
                                        <strong>Verified</strong>
                                        <span>{{((bool)$user->verified)? 'Yes':'No'}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">handshake</span>
                                    <div class="jbitdata">
                                        <strong>Ready for Hire</strong>
                                        <span>{{((bool)$user->is_immediate_available)? 'Yes':'No'}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">cake</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Age')}}</strong>
                                        <span>{{$user->getAge()}} Years</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">account_circle</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Gender')}}</strong>
                                        <span>{{$user->getGender('gender')}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">content_paste</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Marital Status')}}</strong>
                                        <span>{{$user->getMaritalStatus('marital_status')}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">business_center</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Experience')}}</strong>
                                        <span>{{$user->getJobExperience('job_experience')}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">schema</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Career Level')}}</strong>
                                        <span>{{$user->getCareerLevel('career_level')}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">location_on</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Location')}}</strong>
                                        <span>{{$user->getLocation()}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">paid</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Current Salary')}}</strong>
                                        <span>{{$user->salary_currency}}{{$user->current_salary}}</span>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-6 col-md-6 col-6">
                                <div class="jbitlist">
                                    <span class="material-symbols-outlined">payments</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Expected Salary')}}</strong>
                                        <span>{{$user->salary_currency}}{{$user->expected_salary}}</span>
                                    </div>
                                </div>
                            </li>
                            @include('includes.custom_fields_public_grid', ['record' => $user, 'context' => \App\Models\CustomField::CONTEXT_PROFILE])
                        </ul>

                        @if($user->certifications->count() > 0)
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                            <h5 style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">
                                <span class="material-symbols-outlined" style="font-size:16px; vertical-align:middle; margin-right:4px;">workspace_premium</span>
                                {{__('Certifications')}}
                            </h5>
                            <ul style="font-size: 14px; color: #555; margin: 0; padding-left: 20px;">
                                @foreach($user->certifications as $cert)
                                <li>{{ $cert->lang_name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>



                <!-- Profile Video start -->
                @if($user->video_link !=='' && null!==($user->video_link))
                <div class="userdetailbox profileproject">
                    <h3>{{__('Video Profile')}}</h3>
                    <iframe src="{{$user->video_link}}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                @endif




            </div>
        </div>








    </div>
</div>
<div class="modal fade" id="sendmessage" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="" id="send-form">
                @csrf
                <input type="hidden" name="seeker_id" id="seeker_id" value="{{$user->id}}">
                <div class="modal-header">
                    <h4 class="modal-title">Send Message</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <textarea class="form-control" name="message" id="message" cols="10" rows="7"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>

    </div>
</div>




@include('includes.footer')





@endsection
@push('styles')
<style type="text/css">
    .formrow iframe {
        height: 78px;
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript">


    $(document).ready(function() {
        $(document).on('click', '#send_applicant_message', function() {
            var postData = $('#send-applicant-message-form').serialize();
            $.ajax({
                type: 'POST',
                url: "{{ route('contact.applicant.message.send') }}",
                data: postData,
                //dataType: 'json',
                success: function(data) {
                    response = JSON.parse(data);
                    var res = response.success;
                    if (res == 'success') {
                        var errorString = '<div role="alert" class="alert alert-success">' + response.message + '</div>';
                        $('#alert_messages').html(errorString);
                        $('#send-applicant-message-form').hide('slow');
                        $(document).scrollTo('.alert', 2000);
                    } else {
                        var errorString = '<div class="alert alert-danger" role="alert"><ul>';
                        response = JSON.parse(data);
                        $.each(response, function(index, value) {
                            errorString += '<li>' + value + '</li>';
                        });
                        errorString += '</ul></div>';
                        $('#alert_messages').html(errorString);
                        $(document).scrollTo('.alert', 2000);
                    }
                },
            });
        });
        showEducation();
        showProjects();
        showExperience();
        showSkills();
        showLanguages();
    });

    function showProjects() {
        $.post("{{ route('show.applicant.profile.projects', $user->id) }}", {
                user_id: {{ $user->id }},
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                $('#projects_div').html(response);
            });
    }

    function showExperience() {
        $.post("{{ route('show.applicant.profile.experience', $user->id) }}", {
                user_id: {{ $user->id }},
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                $('#experience_div').html(response);
            });
    }

    function showEducation() {
        $.post("{{ route('show.applicant.profile.education', $user->id) }}", {
                user_id: {{ $user->id }},
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                $('#education_div').html(response);
            });
    }

    function showLanguages() {
        $.post("{{ route('show.applicant.profile.languages', $user->id) }}", {
                user_id: {{ $user->id }},
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                $('#language_div').html(response);
            });
    }

    function showSkills() {
        $.post("{{ route('show.applicant.profile.skills', $user->id) }}", {
                user_id: {{ $user->id }},
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })
            .done(function(response) {
                $('#skill_div').html(response);
            });
    }

    function send_message() {
        const el = document.createElement('div')
        el.innerHTML = "Please <a class='btn' href='{{route('login')}}' onclick='set_session()'>log in</a> as a Employer and try again."
        @if(null !== (Auth::guard('company')->user()))
        $('#sendmessage').modal('show');
        @else
        swal({
            title: "You are not Loged in",
            content: el,
            icon: "error",
            button: "OK",
        });
        @endif
    }
    if ($("#send-form").length > 0) {
        $("#send-form").validate({
            validateHiddenInputs: true,
            ignore: "",

            rules: {
                message: {
                    required: true,
                    maxlength: 5000
                },
            },
            messages: {

                message: {
                    required: "Message is required",
                }

            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                @if(null !== (Auth::guard('company')->user()))
                $.ajax({
                    url: "{{route('submit-message-seeker')}}",
                    type: "POST",
                    data: $('#send-form').serialize(),
                    success: function(response) {
                        $("#send-form").trigger("reset");
                        $('#sendmessage').modal('hide');
                        swal({
                            title: "Success",
                            text: response["msg"],
                            icon: "success",
                            button: "OK",
                        });
                    }
                });
                @endif
            }
        })
    }

    // Handle Start Chat button clicks - Navigate to full-page chat
    document.addEventListener('click', function(e) {
        if (e.target.closest('.start-chat-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.start-chat-btn');
            const userId = btn.getAttribute('data-user-id');
            const baseUrl = window.CHAT_BASE_URL || '';

            console.log('Start chat clicked for user:', userId);

            // Start conversation first
            fetch(baseUrl + '/chat/start/' + userId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        const conversationId = data.data.conversation_id;
                        // Navigate to full-page chat with conversation ID
                        window.location.href = baseUrl + '/chat?conversation=' + conversationId;
                    } else {
                        alert(data.message || 'Failed to start conversation');
                    }
                })
                .catch(error => {
                    console.error('Error starting chat:', error);
                    alert('Failed to start conversation. Please try again.');
                });
        }
    });
</script>
@endpush

@include('company.resume_unlock_modal', ['userId' => $user->id])