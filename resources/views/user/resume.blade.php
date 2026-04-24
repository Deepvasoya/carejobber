@extends('layouts.app')

@section('content')

@push('styles')
<style>
    /* Fixed layout width so screen + html2canvas/pdf match the 800px resume table */
    #printableArea {
        width: 800px;
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
        box-sizing: border-box;
        background: #fff;
    }

    #printableArea>table {
        width: 800px !important;
        max-width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .usercvimg img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }

    .experienceList li h4 {
        color: #000;
    }

    .exptitle {
        margin-top: 5px;
    }

    .experienceList li p {
        margin-top: 5px;
    }

    .educationList li h4,
    .educationList li .date {
        color: #000;
    }

    #language_div .text-success {
        color: #000 !important;
    }

    #printableArea table td strong {
        font-weight: bold;
    }

    #printableArea .profileskills {
        list-style: disc;
        margin-left: 20px
    }

    #printableArea .profileskills li.col-md-4 {
        width: 100%;
    }

    #printableArea .profileskills li .skillbox {
        border: none;
        padding: 3px 0;
        display: flex;
        justify-content: space-between;
    }

    #printableArea .profileskills li .skillbox span {
        margin-top: 0;
        color: #fff !important;
        display: none
    }

    #printableArea .experienceList li h4,
    #printableArea .educationList li h4,
    .educationList li .date,
    .educationList li h5 {
        font-weight: normal;
    }

    /* html2canvas handles block layout more reliably than flex for timeline-style lists */
    #printableArea .experienceList,
    #printableArea .educationList {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    #printableArea .experienceList li,
    #printableArea .educationList li {
        position: relative;
        display: block;
        margin: 0 0 14px 0;
        padding: 0 0 14px 0;
        border-bottom: 1px solid #e5e5e5;
    }

    #printableArea .expbox {
        display: block;
        width: 100%;
        box-sizing: border-box;
    }

    #printableArea .expcomp {
        display: block;
        margin-top: 4px;
        font-size: 14px;
        line-height: 1.45;
        color: #333;
    }
</style>
@endpush

<!-- Header start -->

@include('includes.header')
<!-- Header end -->

<!-- Inner Page Title start -->

@include('includes.inner_page_title', ['page_title'=>__('Print Resume')])

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
}

?>

<!-- Inner Page Title end -->

<div class="listpgWraper">

    <div class="container-fluid">
        @include('flash::message')
        <div class="row">

            @include('includes.user_dashboard_menu')

            <div class="col-md-9 col-sm-8">

                @if(count(auth()->user()->getProfileCvsArray())==0 || count(auth()->user()->profileExperience()->get()) == 0 || count(auth()->user()->profileEducation()->get()) == 0 || count(auth()->user()->profileSkills()->get()) == 0)
                @else
                <div class="downloadbtn text-end mb-3">
                    <button type="button" onclick="downloadPDF()" class="btn btn-primary"><i class="fas fa-download"></i> Download Resume</button>
                </div>
                @endif



                <div class="" id="printableArea">
                    <table width="800" align="center" style="margin:0 auto;">
                        <tr>
                            <td width="300" bgcolor="#1e5395" style="font-family:Arial, Helvetica, sans-serif; color: #fff;">
                                <table width="270" align="center" style="margin:0 auto;">
                                    <tr>
                                        <td width="270">
                                            <div class="usercvimg" style="border:2px solid #fff; width: 150px; height: 150px; border-radius: 50%; overflow: hidden; margin: 0 auto; margin-top: 20px; background: #fff;">
                                                {{$user->printUserImage()}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h2 style="color: #fff;margin-bottom: 5px;font-size:20px; padding:10px 0; border-bottom: 1px solid rgba(255,255,255,0.3);">Contact Details</h2>


                                            @if(!empty($user->phone))
                                            <div style="color: #fff; font-size: 16px; margin-top: 0; display: inline-block; color: #fff; padding: 10px; text-align: center; border-radius: 40px; font-weight: 700;"><i class="fa fa-phone" aria-hidden="true"></i> {{$user->phone}}</div>
                                            @endif

                                            @if(!empty($user->mobile_num))
                                            <div style="color: #fff; font-size: 16px; margin-top: 0; display: inline-block; color: #fff; padding: 10px; text-align: center; border-radius: 40px; font-weight: 700;"><i class="fas fa-mobile-alt"></i> {{$user->mobile_num}}</div>
                                            @endif

                                            @if(!empty($user->email))
                                            <div style="color: #fff; font-size: 16px; margin-top: 10px; padding-left: 10px;"><i class="fas fa-envelope" aria-hidden="true"></i> {{$user->email}}</div>
                                            @endif



                                            <p style="color: #fff; font-size: 14px; margin-top: 10px; padding-left: 10px;"><i class="fas fa-globe"></i> {{$user->getLocation()}}</p>

                                            <p style="color: #fff; font-size: 14px; margin-top: 10px; padding-left: 10px; padding-bottom:20px; border-bottom: 1px solid rgba(255,255,255,0.3);"><i class="fas fa-map-marker-alt"></i> {{$user->street_address}}</p>


                                            <div style="color: #fff; font-size: 30px; text-align: center; text-transform: uppercase; border: 1px solid #fff; padding: 10px; font-weight: bold; margin-top: 20px;">{{$user->getJobExperience('job_experience')}}</div>
                                            <p style="color: #fff; font-size: 16px; text-align: center; margin-top: 5px; letter-spacing: 3px; margin-bottom: 0; text-transform: uppercase;">Of experience</p>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <h2 style="color: #fff;margin-bottom: 5px;font-size:20px; padding:10px 0; border-bottom: 1px solid rgba(255,255,255,0.3);">Personal Details</h2>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <table width="270" align="center" style="font-size: 14px;">

                                                <tr>
                                                    <td style="padding: 10px 0;"><strong>Gender</strong></td>
                                                    <td style="padding: 10px 0;">{{$user->getGender('gender')}}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 10px 0;"><strong>Marital Status</strong></td>
                                                    <td style="padding: 10px 0;">{{$user->getMaritalStatus('marital_status')}}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 10px 0;"><strong>Job Title</strong></td>
                                                    <td style="padding: 10px 0;">{{$user->getFunctionalArea('functional_area')}} </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding: 10px 0;"><strong>Nationality</strong> </td>
                                                    <td style="padding: 10px 0;">{{$user->getNationality('nationality')}}</td>
                                                </tr>
                                                @include('includes.custom_fields_resume_print_rows', ['profileCv' => $profileCv ?? null])

                                                <tr>
                                                    <td style="padding: 7px 0;"></td>
                                                    <td style="padding: 7px 0;"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td height="30">
                                            <h2 style="color: #fff;margin-bottom:15px;font-size:20px; padding:10px 0; border-bottom: 1px solid rgba(255,255,255,0.3);">Key Skills</h2>

                                            <div id="skill_div"></div>
                                        </td>
                                    </tr>



                                    <tr>
                                        <td>
                                            <div style="color: #fff000; font-size: 16px; text-align: center; margin-top: 10px; font-style: italic; font-weight: bold; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 20px;">

                                                @if((bool)$user->is_immediate_available)
                                                Immediate Available For Work
                                                @endif
                                            </div>



                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="30">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="500" valign="top" style="font-family:Arial, Helvetica, sans-serif; vertical-align: top;">


                                <table width="500" align="center" style="font-size: 14px; margin:0 auto;">
                                    <tr>
                                        <td bgcolor="#0981c5" style="background:#0981c5;">
                                            <h2 style="color: #fff;margin-bottom: 5px;text-align: center;font-size: 32px; padding:30px 0;">{{$user->getName()}}</h2>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td style="padding-left:20px; padding-right:15px;">
                                            <h2 style="font-size: 22px; color: #000; border-bottom: 2px solid #000; margin-top: 0; padding-top:15px;"><span style="display:inline-block;color:#44546c; padding:10px 0">Objective</span></h2>
                                            <p style="font-size: 16px; line-height: 22px; color: #555;">{{$user->getProfileSummary('summary')}}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:20px; padding-right:15px;">
                                            <h2 style="font-size: 22px; color: #000; border-bottom: 2px solid #000; margin-top: 0;">
                                                <span style="display:inline-block; color:#44546c; padding:10px 0">Work History</span>
                                            </h2>
                                            <div class="" id="experience_div"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:20px; padding-right:15px;">
                                            <h2 style="font-size: 22px; color: #000; border-bottom: 2px solid #000; margin-top: 0;">
                                                <span style="display:inline-block; color:#44546c; padding:10px 0">Education</span>
                                            </h2>
                                            <div class="" id="education_div"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:20px; padding-right:15px;">
                                            <h2 style="font-size: 22px; color: #000; border-bottom: 2px solid #000; margin-top: 0;">
                                                <span style="display:inline-block; color:#44546c; padding:10px 0">Languages</span>
                                            </h2>
                                            <div id="language_div"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                    </tr>
                                </table>





                            </td>
                        </tr>
                    </table>

                </div>

                <div class="text-center mt-5 mb-5">

                    @if(count(auth()->user()->getProfileCvsArray())==0 || count(auth()->user()->profileExperience()->get()) == 0 || count(auth()->user()->profileEducation()->get()) == 0 || count(auth()->user()->profileSkills()->get()) == 0)
                    <div class="userprofilealert">
                        <h5><i class="fas fa-exclamation-triangle"></i> Your profile is incomplete please update to Download Resume.</h5>
                        <div class="editbtbn"><a href="{{ route('my.profile') }}"><i class="fas fa-user-edit"></i> Edit Profile </a></div>
                    </div>
                    @else
                    <div class="downloadbtn">
                        <button type="button" onclick="downloadPDF()" class="btn btn-primary"><i class="fas fa-download"></i> Download Resume</button>
                    </div>
                    @endif

                </div>
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

                success: function(data)

                {

                    response = JSON.parse(data);

                    var res = response.success;

                    if (res == 'success')

                    {

                        var errorString = '<div role="alert" class="alert alert-success">' + response.message + '</div>';

                        $('#alert_messages').html(errorString);

                        $('#send-applicant-message-form').hide('slow');

                        $(document).scrollTo('.alert', 2000);

                    } else

                    {

                        var errorString = '<div class="alert alert-danger" role="alert"><ul>';

                        response = JSON.parse(data);

                        $.each(response, function(index, value)

                            {

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

    function showProjects()

    {

        $.post("{{ route('show.applicant.profile.projects', $user->id) }}", {
                user_id: {
                    {
                        $user - > id
                    }
                },
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })

            .done(function(response) {

                $('#projects_div').html(response);

            });

    }

    function showExperience()

    {

        $.post("{{ route('show.applicant.profile.experience', $user->id) }}", {
                user_id: {
                    {
                        $user - > id
                    }
                },
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })

            .done(function(response) {

                $('#experience_div').html(response);

            });

    }


    function showEducation()

    {

        $.post("{{ route('show.applicant.profile.education', $user->id) }}", {
                user_id: {
                    {
                        $user - > id
                    }
                },
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })

            .done(function(response) {

                $('#education_div').html(response);

            });

    }

    function showLanguages()

    {

        $.post("{{ route('show.applicant.profile.languages', $user->id) }}", {
                user_id: {
                    {
                        $user - > id
                    }
                },
                _method: 'POST',
                _token: '{{ csrf_token() }}'
            })

            .done(function(response) {

                $('#language_div').html(response);

            });

    }

    function showSkills()

    {

        $.post("{{ route('show.applicant.profile.skills', $user->id) }}", {
                user_id: {
                    {
                        $user - > id
                    }
                },
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
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
    function downloadPDF() {
        var source = document.getElementById('printableArea');
        if (!source) {
            return;
        }

        var resumeWidth = 800;

        /* Clone off-screen at full resume width so PDF is not squeezed by Bootstrap column / viewport */
        var holder = document.createElement('div');
        holder.setAttribute('aria-hidden', 'true');
        holder.style.cssText = 'position:fixed;left:-9999px;top:0;width:' + resumeWidth + 'px;margin:0;padding:0;background:#fff;overflow:visible;';
        var clone = source.cloneNode(true);
        clone.removeAttribute('id');
        clone.style.width = resumeWidth + 'px';
        clone.style.maxWidth = resumeWidth + 'px';
        clone.style.margin = '0';
        clone.style.boxSizing = 'border-box';
        clone.style.background = '#fff';
        holder.appendChild(clone);
        document.body.appendChild(holder);

        var opt = {
            margin: [0, 0, 0, 0],
            image: {
                type: 'jpeg',
                quality: 0.95
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                allowTaint: true,
                logging: false,
                backgroundColor: '#ffffff',
                letterRendering: true,
                scrollX: 0,
                scrollY: 0,
                width: resumeWidth,
                windowWidth: resumeWidth
            },
            /* was invalid: jsPDF used "pt: 'in'" (ignored) — caused wrong scaling vs A4 */
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            },
            pagebreak: {
                mode: ['css', 'legacy']
            }
        };

        var cleanup = function() {
            if (holder.parentNode) {
                holder.parentNode.removeChild(holder);
            }
        };

        var fontsReady = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();

        fontsReady
            .then(function() {
                return html2pdf().set(opt).from(clone).outputPdf('blob');
            })
            .then(function(pdfBlob) {
                var url = URL.createObjectURL(pdfBlob);
                window.open(url);
                setTimeout(function() {
                    try {
                        URL.revokeObjectURL(url);
                    } catch (e) {}
                }, 120000);
            })
            .catch(function(err) {
                console.error('Resume PDF error:', err);
            })
            .finally(cleanup);
    }
</script>




@endpush