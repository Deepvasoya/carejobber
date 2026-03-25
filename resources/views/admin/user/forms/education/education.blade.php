{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <div class="form-group mb-3">
        <button class="btn purple btn-outline sbold" onclick="showProfileEducationModal();"> Add Education </button>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption"> <i class=" icon-layers font-green"></i> <span class="caption-subject font-green bold uppercase">Education</span> </div>
                </div>
                <div class="portlet-body"><div class="row" id="education_div"></div></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-modal-lg" id="add_education_modal" tabindex="-1" role="dialog" aria-hidden="true"></div>
@push('css')
<style type="text/css">
    .datepicker>div {
        display: block;
    }
</style>
@endpush
@push('scripts') 
<script type="text/javascript">
    /**************************************************/
    function showProfileEducationModal(){
    $("#add_education_modal").modal();
    loadProfileEducationForm();
    }
    function loadProfileEducationForm(){
    $.ajax({
    type: "POST",
            url: "{{ route('get.profile.education.form', $user->id) }}",
            data: {"_token": "{{ csrf_token() }}"},
            datatype: 'json',
            success: function (json) {
            $("#add_education_modal").html(json.html);
            initdatepicker();
            }
    });
    }
    function showProfileEducationEditModal(education_id){
    $("#add_education_modal").modal();
    loadProfileEducationEditForm(education_id);
    }
    function loadProfileEducationEditForm(education_id){
    $.ajax({
    type: "POST",
            url: "{{ route('get.profile.education.edit.form', $user->id) }}",
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
    if (confirm('Are you sure! you want to delete?')) {
    $.post("{{ route('delete.profile.education') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
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
    function initdatepicker(){
    $(".datepicker").datepicker({
    autoclose: true,
            format:'yyyy-m-d'
    });
    /*****/
    $('.select2-multiple').select2({
    placeholder: "Select Major Subjects",
            allowClear: true
    });
    }
    $(document).ready(function(){
    showEducation();
    initdatepicker();
    });
    function showEducation()
    {
    $.post("{{ route('show.profile.education', $user->id) }}", {user_id: {{$user->id}}, _method: 'POST', _token: '{{ csrf_token() }}'})
            .done(function (response) {
            $('#education_div').html(response);
            });
    }


</script> 
@endpush