
<div class="newcvcard">
                <div class="cardhead">
                    <h4 onclick="showEducation();">{{__('Education')}}</h4>
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#add_education_modal" onclick="showProfileEducationModal();"><i class="fas fa-plus"></i></a>
                </div>


                <div class="cardintbody">
                    <div class="" id="education_div"></div>  
                </div>
             </div>



<div class="modal fade" id="add_education_modal" tabindex="-1" aria-labelledby="addexpModalLabel" aria-hidden="true" role="dialog"></div>



@push('styles')



<style type="text/css">

    .datepicker>div {
        display: block;
    }

    /* Candidate resume — education modal (label left, field right) */
    .resume-education-form .resume-edu-label {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
        padding-bottom: 0;
    }

    .resume-education-form .resume-edu-input,
    .resume-education-form .resume-edu-input:focus {
        background: #f4f7f9;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 0.55rem 0.85rem;
        box-shadow: none;
    }

    .resume-education-form .resume-edu-input:focus {
        border-color: #c7d2fe;
        background: #fff;
    }

    .resume-education-form .resume-edu-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .resume-education-form .resume-edu-row {
        margin-left: 0;
        margin-right: 0;
    }

    @media (max-width: 575.98px) {
        .resume-education-form .resume-edu-label {
            margin-bottom: 0.35rem;
        }
    }

</style>



@endpush



@push('scripts') 



<script type="text/javascript">



    /**************************************************/



    function showProfileEducationModal(){

    $('#add_education_modal').css('display','block');

    var myclosemodal = $('<div></div>');

    myclosemodal.addClass('modal-backdrop fade show');

    $('body').append(myclosemodal);





    $("#add_education_modal").modal();



    loadProfileEducationForm();



    }



    function loadProfileEducationForm(){



    $.ajax({



    type: "POST",



            url: "{{ route('get.front.profile.education.form', $user->id) }}",



            data: {"_token": "{{ csrf_token() }}"},



            datatype: 'json',



            success: function (json) {



            $("#add_education_modal").html(json.html);



            initdatepicker();

            }




    });



    }



    function showProfileEducationEditModal(education_id){

    $('#add_education_modal').css('display','block');

    var myclosemodal = $('<div></div>');

    myclosemodal.addClass('modal-backdrop fade show');

    $('body').append(myclosemodal);

    $("#add_education_modal").modal();

    loadProfileEducationEditForm(education_id);

    }

    function loadProfileEducationEditForm(education_id){

    $.ajax({

    type: "POST",

            url: "{{ route('get.front.profile.education.edit.form', $user->id) }}",

            data: {"education_id": education_id, "_token": "{{ csrf_token() }}"},

            datatype: 'json',

            success: function (json) {

            $("#add_education_modal").html(json.html);

            initdatepicker();

            }

    });

    }



    function submitProfileEducationForm() {



    var form = $('#add_edit_profile_education');



    $.ajax({



    url     : form.attr('action'),



            type    : form.attr('method'),



            data    : form.serialize(),



            dataType: 'json',



            success : function (json){



            $ ("#add_education_modal").html(json.html);



            showEducation();



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



    function delete_profile_education(id) {



    var msg = "{{__('Are you sure! you want to delete?')}}";



    if (confirm(msg)) {



    $.post("{{ route('delete.front.profile.education') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})



            .done(function (response) {



            if (response == 'ok')



            {



            $('#education_' + id).remove();



            } else



            {



            alert('Request Failed!');



            }



            });



    }



    }



    function initdatepicker() {

    $(".datepicker").datepicker({

        autoclose: true,

        format: 'yyyy-m-d'

    });



    // Reinitialize Select2

    $('.select2-multiple').each(function () {

        if (!$(this).hasClass("select2-hidden-accessible")) {

            $(this).select2({

                placeholder: "{{__('Select Major Subjects')}}",

                allowClear: true

            });

        }

    });

}



    $(document).ready(function(){



    showEducation();



    initdatepicker();



    });



    function showEducation()



    {



    $.post("{{ route('show.front.profile.education', $user->id) }}", {user_id: {{$user->id}}, _method: 'POST', _token: '{{ csrf_token() }}'})



            .done(function (response) {



            $('#education_div').html(response);



            });



    }













</script> 



@endpush