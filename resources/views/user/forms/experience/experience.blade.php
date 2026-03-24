
<div class="newcvcard">
                <div class="cardhead">
                    <h4 onclick="showExperience();">{{__('Work History')}}</h4>
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#add_experience_modal" onclick="showProfileExperienceModal();"><i class="fas fa-plus"></i></a>
                </div>
                
               
                <div class="cardintbody">
                        <div class="" id="experience_div"></div>     
                </div>
             </div>









<div class="modal fade" id="add_experience_modal" tabindex="-1" aria-labelledby="addexpModalLabel" aria-hidden="true" role="dialog"></div>



@push('styles')



<style type="text/css">

    .datepicker>div {
        display: block;
    }

    .resume-experience-form .resume-exp-label {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
    }

    .resume-experience-form .resume-exp-input,
    .resume-experience-form .resume-exp-input:focus {
        background: #f4f7f9;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 0.55rem 0.85rem;
        box-shadow: none;
    }

    .resume-experience-form .resume-exp-input:focus {
        border-color: #c7d2fe;
        background: #fff;
    }

    .resume-experience-form .resume-exp-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .resume-experience-form .resume-exp-row {
        margin-left: 0;
        margin-right: 0;
    }

    @media (max-width: 575.98px) {
        .resume-experience-form .resume-exp-label {
            margin-bottom: 0.35rem;
        }
    }

</style>



@endpush



@push('scripts') 





<script type="text/javascript">



    /**************************************************/



    function showProfileExperienceModal(){

    $('#add_experience_modal').css('display','block');

    var myclosemodal = $('<div></div>');

    myclosemodal.addClass('modal-backdrop fade show');

    $('body').append(myclosemodal);



    $("#add_experience_modal").modal();



    loadProfileExperienceForm();



    }



    function loadProfileExperienceForm(){



    $.ajax({



    type: "POST",



            url: "{{ route('get.front.profile.experience.form', $user->id) }}",



            data: {"_token": "{{ csrf_token() }}"},



            datatype: 'json',



            success: function (json) {



            $("#add_experience_modal").html(json.html);



            initdatepicker();



            syncExperienceDateEndVisibility();



            }



    });



    }



    function showProfileExperienceEditModal(profile_experience_id){

    $('#add_experience_modal').css('display','block');

    var myclosemodal = $('<div></div>');

    myclosemodal.addClass('modal-backdrop fade show');

    $('body').append(myclosemodal);







    $("#add_experience_modal").modal();



    loadProfileExperienceEditForm(profile_experience_id);



    }



    function loadProfileExperienceEditForm(profile_experience_id){



    $.ajax({



    type: "POST",



            url: "{{ route('get.front.profile.experience.edit.form', $user->id) }}",



            data: {"profile_experience_id": profile_experience_id, "_token": "{{ csrf_token() }}"},



            datatype: 'json',



            success: function (json) {



            $("#add_experience_modal").html(json.html);



            initdatepicker();



            syncExperienceDateEndVisibility();



            }



    });



    }



    function submitProfileExperienceForm() {



    var form = $('#add_edit_profile_experience');



    $.ajax({



    url     : form.attr('action'),



            type    : form.attr('method'),



            data    : form.serialize(),



            dataType: 'json',



            success : function (json){



            $ ("#add_experience_modal").html(json.html);



            showExperience();



            },



            error: function(json){



            if (json.status === 422) {



            var resJSON = json.responseJSON;



            $('.help-block').html('');



            $.each(resJSON.errors, function (key, value) {



            $('.' + key + '-error').html('<strong>' + value + '</strong>');



            $('#div_' + key).addClass('has-error');



            });



            } else {



            // Error



            // Incorrect credentials



            // alert('Incorrect credentials. Please try again.')



            }



            }



    });



    }



    function delete_profile_experience(id) {



    var msg = "{{__('Are you sure! you want to delete?')}}";



    if (confirm(msg)) {



    $.post("{{ route('delete.front.profile.experience') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})



            .done(function (response) {



            if (response == 'ok')



            {



            $('#experience_' + id).remove();



            } else



            {



            alert('Request Failed!');



            }



            });



    }



    }



    function initdatepicker(){



    $(".datepicker").datepicker({



    autoclose: true,



            format:'yyyy-m-d'



    });



    }



    $(document).ready(function(){



    showExperience();



    initdatepicker();



    $(document).on('change', 'input[name="is_currently_working"]', function () {
        syncExperienceDateEndVisibility();
    });

    });



    function showExperience()



    {



    $.post("{{ route('show.front.profile.experience', $user->id) }}", {user_id: {{$user->id}}, _method: 'POST', _token: '{{ csrf_token() }}'})



            .done(function (response) {



            $('#experience_div').html(response);



            });



    }























    function syncExperienceDateEndVisibility() {
        var v = $('input[name="is_currently_working"]:checked').val();
        if (v === '1') {
            $('#div_date_end').hide();
        } else {
            $('#div_date_end').show();
        }
    }



</script> 



@endpush