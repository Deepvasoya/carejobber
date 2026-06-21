@extends('layouts.app')

@section('content')

<!-- Header start -->

@include('includes.header')

<!-- Header end -->



<div class="listpgWraper mt-5">

    <div class="container-fluid">

        @include('flash::message')

        <!-- Job Header start -->
         <div class="row">
            <div class="col-lg-7">
            
         

        <div class="job-header">
            <div class="jobinfo">
                        <!-- Candidate Info -->

                        <div class="candidateinfo">

                            <div class="userPic"><a href="{{route('company.detail',$company->slug)}}">{{$company->printCompanyImage()}}</a>

                            </div>

                            <div class="title">{{$company->name}} @include('components.verified-badge', ['company' => $company])</div>

                            <div class="desi">{{$company->getIndustry('industry')}}</div>

                            <div class="loctext"><i class="fa fa-history" aria-hidden="true"></i>

                                {{__('Member Since')}}, {{$company->created_at->format('M d, Y')}}</div>

                            <div class="loctext"><i class="fa fa-map-marker" aria-hidden="true"></i>

                                {{$company->location}}</div>

                            <div class="clearfix"></div>

                        </div>
                        
                        @if($company->created_by_admin && !$company->is_claimed)
                        <!-- Unclaimed Profile Alert -->
                        <div class="alert alert-warning mt-3" role="alert">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fa fa-info-circle"></i>
                                    <strong>{{ __('This employer profile has not yet been claimed.') }}</strong>
                                    <p class="mb-0 mt-1 small">{{ __('Are you the owner of this company? You can claim this profile.') }}</p>
                                </div>
                            </div>
                        </div>
                        @elseif($company->created_by_admin && $company->is_claimed && $company->claimedByUser)
                        <!-- Claimed Profile Alert -->
                        <div class="alert alert-success mt-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-check-circle mr-2"></i>
                                <div>
                                    <strong>{{ __('This employer profile has been claimed.') }}</strong>
                                    <p class="mb-0 mt-1 small">
                                        {{ __('Claimed by') }} <strong>{{ $company->claimedByUser->name }}</strong>
                                        @if($company->claimed_at)
                                            {{ __('on') }} {{ $company->claimed_at->format('M d, Y') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif


            </div>

            <!-- Buttons -->
            <div class="jobButtons"> 
            @if($company->created_by_admin && !$company->is_claimed && !$hasPendingClaim)
                <a href="javascript:void(0);" onclick="openClaimModal()" class="btn btn-warning">
                    <i class="fa fa-hand-pointer-o" aria-hidden="true"></i> {{ __('Claim this employer profile') }}
                </a>
            @elseif($hasPendingClaim)
                <a href="javascript:void(0);" class="btn btn-secondary disabled">
                    <i class="fa fa-clock-o" aria-hidden="true"></i> {{ __('Pending Review') }}
                </a>
            @endif
            
            @if(Auth::guard('web')->check() && Auth::guard('web')->user()->isFavouriteCompany($company->slug))
    <a href="{{ route('remove.from.favourite.company', $company->slug) }}" class="btn">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> {{ __('Remove from Favourite') }}
    </a>
@else
    <a href="{{ route('add.to.favourite.company', $company->slug) }}" class="btn">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> {{ __('Add to Favourite') }}
    </a>
@endif

                <a href="{{ route('report.abuse.company', $company->slug) }}" class="btn report">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ __('Report Abuse') }}
                </a> 
    
            </div>
        </div>

        <!-- About Employee start -->

        <div class="job-header">

            <div class="contentbox">

                <h3>{{__('About Company')}}</h3>

                <p>{!! $company->description !!}</p>

            </div>

        </div>


        </div>

        <div class="col-lg-5">
         <!-- Company Detail start -->

         <div class="job-header">

            <div class="jobdetail">
                <h3>{{__('Company Detail')}}</h3>
                    <ul class="jbdetail row">
                                <li class="col-lg-4 col-md-6 col-6">
                                    <div class="jbitlist">
                                    <span class="material-symbols-outlined">verified</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Verified')}}</strong>
                                        <span>{{((bool)$company->verified)? 'Yes':'No'}}</span>
                                    </div>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-6">
                                    <div class="jbitlist">
                                    <span class="material-symbols-outlined">group</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Company Size')}}</strong>
                                        <span>{{$company->no_of_employees}}</span>
                                    </div>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-6">
                                    <div class="jbitlist">
                                    <span class="material-symbols-outlined">cake</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Founded In')}}</strong>
                                        <span>{{$company->established_in}}</span>
                                    </div>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-6">
                                    <div class="jbitlist">
                                    <span class="material-symbols-outlined">corporate_fare</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Organization Type')}}</strong>
                                        <span>{{$company->getOwnershipType('ownership_type')}}</span>
                                    </div>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-6">
                                    <div class="jbitlist">
                                    <span class="material-symbols-outlined">corporate_fare</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Total Offices')}}</strong>
                                        <span>{{$company->no_of_offices}}</span>
                                    </div>
                                    </div>
                                </li>
                                <li class="col-lg-4 col-md-6 col-6">
                                    <div class="jbitlist">
                                    <span class="material-symbols-outlined">cases</span>
                                    <div class="jbitdata">
                                        <strong>{{__('Opend Jobs')}}</strong>
                                        <span>{{$company->countNumJobs('company_id',$company->id)}}</span>
                                    </div>
                                    </div>
                                </li>
                                @include('includes.custom_fields_public_grid', ['record' => $company, 'context' => \App\Models\CustomField::CONTEXT_COMPANY_PROFILE])


                            
                    </ul>
            </div>

         </div>


         <div class="job-header">

                    <div class="jobdetail">
                        <iframe src="https://maps.google.it/maps?q={{urlencode(strip_tags($company->map))}}&output=embed" allowfullscreen></iframe>
                    </div>

                </div>


        </div>

        </div>









  <!-- Opening Jobs start -->

  <div class="relatedJobs">

<h3>{{__('Current Openings')}}</h3>

<ul class="featuredlist row">
    @if(isset($company->jobs) && count($company->jobs))

    @foreach($company->jobs as $companyJob)

    <!--Job start-->
         <li class="col-lg-3 col-md-6">
                <div class="jobint">

                    <div class="d-flex">
                        <div class="fticon"><i class="fas fa-briefcase"></i> {{$companyJob->getJobType('job_type')}}</div>                        
                    </div>

                    <h4><a href="{{route('job.detail', [$companyJob->slug])}}" title="{{$companyJob->title}}">{!! \Illuminate\Support\Str::limit($companyJob->title, $limit = 20, $end = '...') !!}</a>
                    
                    
                </h4>
                @if(!(bool)$companyJob->hide_salary)                    
                    <div class="salary mb-2">Salary: <strong>{{$companyJob->salary_currency.''.$companyJob->salary_from}} - {{$companyJob->salary_currency.''.$companyJob->salary_to}}/{{$companyJob->getSalaryPeriod('salary_period')}}</strong></div>
                    @endif 


                    <strong><i class="fas fa-map-marker-alt"></i> {{$companyJob->getCity('city')}}</strong> 
                    
                    <div class="jobcompany">
                     <div class="ftjobcomp">
                        <span>{{$companyJob->created_at->format('M d, Y')}}</span>
                        <a href="{{route('company.detail', $company->slug)}}" title="{{$company->name}}">{{$company->name}}</a>
                     </div>
                    <a href="{{route('company.detail', $company->slug)}}" class="company-logo" title="{{$company->name}}">{{$company->printCompanyImage()}} </a>
                    </div>
                </div>
            </li>

    <!--Job end-->

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

<!-- Modal -->

<div class="modal fade" id="sendmessage" role="dialog">

    <div class="modal-dialog">



        <!-- Modal content-->

        <div class="modal-content">

            <form action="" id="send-form">

                @csrf

                <input type="hidden" name="company_id" id="company_id" value="{{$company->id}}">

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

<!-- Claim Company Modal -->
<div class="modal fade" id="claimCompanyModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" id="claim-company-form">
                @csrf
                <input type="hidden" name="company_id" value="{{$company->id}}">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-hand-pointer-o"></i> Claim This Employer Profile</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Please provide your details to verify your association with this company.</p>
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i> <strong>You must include your company email address</strong> in the request so we can verify your association with this organization.
                    </div>
                    <div class="form-group mb-3">
                        <label for="claimant_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="claimant_name" id="claimant_name" 
                               placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="claimant_email">Work Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="claimant_email" id="claimant_email" 
                               placeholder="Your company email address (e.g. you@company.com)" required>
                        <small class="form-text text-muted">Please use your company email for verification purposes.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="claimant_job_title">Job Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="claimant_job_title" id="claimant_job_title" 
                               placeholder="Your position at this company" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="claim_message">Brief Explanation <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="message" id="claim_message" rows="4" 
                                  placeholder="Explain your role at this company and why you are requesting to claim this profile..." required></textarea>
                        <small class="form-text text-muted">Mention your responsibilities and how you are authorized to represent this company.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Your claim request will be reviewed by our admin team. You will be notified via email once it's processed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Claim Request</button>
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

#claimCompanyModal .modal-body {
    font-size: 13px;
}
#claimCompanyModal .modal-body label {
    font-size: 13px;
    margin-bottom: 2px;
}
#claimCompanyModal .modal-body .form-control {
    font-size: 13px;
}
#claimCompanyModal .modal-body .form-control::placeholder {
    font-size: 12px;
}
#claimCompanyModal .modal-body small {
    font-size: 11px;
}
#claimCompanyModal .modal-body .alert {
    font-size: 12px;
    padding: 8px 12px;
    margin-bottom: 12px;
}
#claimCompanyModal .modal-body .form-group {
    margin-bottom: 10px !important;
}
#claimCompanyModal .modal-title {
    font-size: 16px;
}

</style>

@endpush

@push('scripts')

<script type="text/javascript">

function openClaimModal() {
    @if(Auth::guard('web')->check() || Auth::guard('company')->check())
        $('#claimCompanyModal').modal('show');
    @else
        const el = document.createElement('div');
        el.innerHTML = "Please <a class='btn' href='{{route('login')}}'>log in</a> to claim this profile.";
        swal({
            title: "Login Required",
            content: el,
            icon: "info",
            button: "OK",
        });
    @endif
}

$(document).ready(function() {
    // Claim company form handler
    if ($("#claim-company-form").length > 0) {
        $("#claim-company-form").validate({
            rules: {
                claimant_name: {
                    required: true,
                    maxlength: 191
                },
                claimant_email: {
                    required: true,
                    email: true,
                    maxlength: 191
                },
                claimant_job_title: {
                    required: true,
                    maxlength: 191
                },
                message: {
                    required: true,
                    minlength: 20,
                    maxlength: 2000
                },
            },
            messages: {
                claimant_name: {
                    required: "Please enter your full name"
                },
                claimant_email: {
                    required: "Please enter your work email",
                    email: "Please enter a valid email address"
                },
                claimant_job_title: {
                    required: "Please enter your job title"
                },
                message: {
                    required: "Please provide an explanation",
                    minlength: "Please provide at least 20 characters",
                    maxlength: "Message cannot exceed 2000 characters"
                }
            },
            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                @if(Auth::guard('web')->check() || Auth::guard('company')->check())
                $.ajax({
                    url: "{{ route('submit.company.claim.request') }}",
                    type: "POST",
                    data: $('#claim-company-form').serialize(),
                    success: function(response) {
                        $("#claim-company-form").trigger("reset");
                        $('#claimCompanyModal').modal('hide');
                        swal({
                            title: "Success",
                            text: response.message || "Your claim request has been submitted successfully. Our team will review it shortly.",
                            icon: "success",
                            button: "OK",
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = "An error occurred. Please try again.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        swal({
                            title: "Error",
                            text: errorMsg,
                            icon: "error",
                            button: "OK",
                        });
                    }
                });
                @endif
            }
        });
    }

    $(document).on('click', '#send_company_message', function() {

        var postData = $('#send-company-message-form').serialize();

        $.ajax({

            type: 'POST',

            url: "{{ route('contact.company.message.send') }}",

            data: postData,

            //dataType: 'json',

            success: function(data) {

                response = JSON.parse(data);

                var res = response.success;

                if (res == 'success') {

                    var errorString = '<div role="alert" class="alert alert-success popmessage">' +

                        response.message + '</div>';

                    $('#alert_messages').html(errorString);

                    $('#send-company-message-form').hide('slow');

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

});



function send_message() {

    const el = document.createElement('div')

    el.innerHTML =

        "Please <a class='btn' href='{{route('login')}}' onclick='set_session()'>log in</a> as a Canidate and try again."

    @if(Auth::check())

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

            @if(null !== (Auth::user()))

            $.ajax({

                url: "{{route('submit-message')}}",

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

</script>

@endpush
