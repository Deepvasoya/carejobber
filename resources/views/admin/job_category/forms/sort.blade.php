{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <h3>Drag and Drop to Sort Job Categories</h3>
    {!! Form::select('lang', ['' => 'Select Language']+$languages, config('default_lang'), array('class'=>'form-control', 'id'=>'lang', 'onchange'=>'refreshJobCategorySortData();')) !!}
    <div id="jobCategorySortDataDiv"></div>
</div>
@push('scripts') 
<script>
    $(document).ready(function () {
        refreshJobCategorySortData();
    });
    function refreshJobCategorySortData() {
        var language = $('#lang').val();
        $.ajax({
            type: "GET",
            url: "{{ route('job.category.sort.data') }}",
            data: {lang: language},
            success: function (responseData) {
                $("#jobCategorySortDataDiv").html('');
                $("#jobCategorySortDataDiv").html(responseData);
                /**************************/
                $('#sortable').sortable({
                    update: function (event, ui) {
                        var jobCategoryOrder = $(this).sortable('toArray').toString();
                        $.post("{{ route('job.category.sort.update') }}", {jobCategoryOrder: jobCategoryOrder, _method: 'PUT', _token: '{{ csrf_token() }}'})
                    }
                });
                $("#sortable").disableSelection();
                /***************************/
            }
        });
    }
</script> 
@endpush