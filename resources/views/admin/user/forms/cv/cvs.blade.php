{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body"> 
    <div class="form-group mb-3"> <button class="btn purple btn-outline sbold" onclick="showProfileCvModal();"> Add Cv </button> </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit bordered">
                <div class="portlet-title">
                    <div class="caption"> <i class=" icon-layers font-green"></i> <span class="caption-subject font-green bold uppercase">Cvs</span> </div>
                </div>
                <div class="portlet-body">
                    <div class="mt-element-card mt-element-overlay">
                        <div class="row" id="cvs_div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-modal-lg" id="add_cv_modal" tabindex="-1" role="dialog" aria-hidden="true"></div>
@push('css')
<style type="text/css">
    .datepicker>div {
        display: block;
    }
</style>
@endpush
@push('scripts') 
<script type="text/javascript">
    $(document).ready(function(){
    showCvs();
    });
    /**************************************************/
    function showProfileCvModal(){
    $("#add_cv_modal").modal();
    loadProfileCvForm();
    }
    function loadProfileCvForm(){
    $.ajax({
    type: "POST",
            url: "{{ route('get.profile.cv.form', $user->id) }}",
            data: {"_token": "{{ csrf_token() }}"},
            datatype: 'json',
            success: function (json) {
            $("#add_cv_modal").html(json.html);
            if (typeof window.initCustomFieldMultiselect === 'function') {
                window.initCustomFieldMultiselect($('#add_cv_modal'));
            }
            }
    });
    }
    function submitProfileCvForm() {
    var formEl = document.getElementById('add_edit_profile_cv');
    if (!formEl) {
        return;
    }
    var formData = new FormData(formEl);
    $.ajax({
    url     : $(formEl).attr('action'),
            type    : 'POST',
            data    : formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success : function (json){
            $ ("#add_cv_modal").html(json.html);
            if (typeof window.initCustomFieldMultiselect === 'function') {
                window.initCustomFieldMultiselect($('#add_cv_modal'));
            }
            showCvs();
            },
            error: function(xhr){
            if (xhr.status === 422) {
            var resJSON = xhr.responseJSON;
            $('#add_edit_profile_cv .help-block').html('');
            $.each(resJSON.errors, function (key, value) {
            var msg = Array.isArray(value) ? value[0] : value;
            var parts = key.split('.');
            if (parts[0] === 'custom_fields' && parts[1]) {
                $('.cf-err-' + parts[1]).html('<strong>' + msg + '</strong>');
            } else {
                $('.' + key.replace(/\./g, '-') + '-error').html('<strong>' + msg + '</strong>');
                $('.' + key.replace(/\./g, '_') + '-error').html('<strong>' + msg + '</strong>');
                $('#div_' + key.replace(/\./g, '_')).addClass('has-error');
            }
            });
            }
            }
    });
    }
    /*****************************************/
    function showProfileCvEditModal(cv_id){
    $("#add_cv_modal").modal();
    loadProfileCvEditForm(cv_id);
    }
    function loadProfileCvEditForm(cv_id){
    $.ajax({
    type: "POST",
            url: "{{ route('get.profile.cv.edit.form', $user->id) }}",
            data: {"cv_id": cv_id, "_token": "{{ csrf_token() }}"},
            datatype: 'json',
            success: function (json) {
            $("#add_cv_modal").html(json.html);
            if (typeof window.initCustomFieldMultiselect === 'function') {
                window.initCustomFieldMultiselect($('#add_cv_modal'));
            }
            }
    });
    }
    /*****************************************/
    function showCvs()
    {
    $.post("{{ route('show.profile.cvs', $user->id) }}", {user_id: {{$user->id}}, _method: 'POST', _token: '{{ csrf_token() }}'})
            .done(function (response) {
            $('#cvs_div').html(response);
            });
    }
    function delete_profile_cv(id) {
    if (confirm('Are you sure! you want to delete?')) {
    $.post("{{ route('delete.profile.cv') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
            .done(function (response) {
            if (response == 'ok')
            {
            $('#cv_' + id).remove();
            } else
            {
            alert('Request Failed!');
            }
            });
    }
    }
</script>
@endpush
