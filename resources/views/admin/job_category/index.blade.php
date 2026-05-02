@extends('admin.layouts.admin_layout')
@section('content')
<style type="text/css">
    .table td, .table th {
        font-size: 12px;
        line-height: 2.42857 !important;
    }	
</style>
<div class="page-content-wrapper"> 
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content"> 
        <!-- BEGIN PAGE HEADER--> 
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Job Categories</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Manage Job Categories <small>Job Categories</small> </h3>
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <!-- Begin: life time stats -->
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption"> <i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">Job Categories</span> </div>
                        <div class="actions"> 
                            <a href="{{ route('create.job.category') }}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Add New Job Category</a>
                            <button type="button" id="bulkDeleteJobCategories" class="btn btn-xs btn-danger" style="display:none;"><i class="fa fa-trash"></i> Bulk Delete</button>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <form method="post" role="form" id="jobCategory-search-form">
                                <table class="table table-striped table-bordered table-hover"  id="jobCategoryDatatableAjax">
                                    <thead>
                                        <tr role="row" class="filter"> 
                                            <td></td>
                                            <td>{!! Form::select('lang', ['' => 'Select Language']+$languages, config('default_lang'), array('id'=>'lang', 'class'=>'form-control')) !!}</td>
                                            <td><input type="text" class="form-control" name="job_category" id="job_category" autocomplete="off" placeholder="Job Category"></td>                      
                                            <td><select name="is_active" id="is_active"  class="form-control">
                                                    <option value="-1">Is Active?</option>
                                                    <option value="1" selected="selected">Active</option>
                                                    <option value="0">In Active</option>
                                                </select></td></tr>
                                        <tr role="row" class="heading">                                            
                                            <th><input type="checkbox" id="selectAllJobCategories" /></th>
                                            <th>Language</th>
                                            <th>Job Category</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY --> 
</div>
@endsection
@push('scripts') 
<script>
    $(function () {
        var oTable = $('#jobCategoryDatatableAjax').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            ajax: {
                url: '{!! route('fetch.data.job.categories') !!}',
                data: function (d) {
                    d.lang = $('#lang').val();
                    d.job_category = $('input[name=job_category]').val();
                    d.is_active = $('#is_active').val();
                }
            }, columns: [
                {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                {data: 'lang', name: 'lang'},
                {data: 'job_category', name: 'job_category'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        $('#jobCategory-search-form').on('submit', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#lang').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#job_category').on('keyup', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#is_active').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
    function deleteJobCategory(id, is_default) {
        var msg = 'Are you sure?';
        if (is_default == 1) {
            msg = 'Are you sure? You are going to delete default Job Category, all other non default Job Categories will be deleted too!';
        }
        if (confirm(msg)) {
            $.post("{{ route('delete.job.category') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        if (response == 'ok')
                        {
                            var table = $('#jobCategoryDatatableAjax').DataTable();
                            table.row('jobCategoryDtRow' + id).remove().draw(false);
                        } else
                        {
                            alert('Request Failed!');
                        }
                    });
        }
    }
    function makeActive(id) {
        $.post("{{ route('make.active.job.category') }}", {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok')
                    {
                        var table = $('#jobCategoryDatatableAjax').DataTable();
                        table.row('jobCategoryDtRow' + id).remove().draw(false);
                    } else
                    {
                        alert('Request Failed!');
                    }
                });
    }
    function makeNotActive(id) {
        $.post("{{ route('make.not.active.job.category') }}", {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok')
                    {
                        var table = $('#jobCategoryDatatableAjax').DataTable();
                        table.row('jobCategoryDtRow' + id).remove().draw(false);
                    } else
                    {
                        alert('Request Failed!');
                    }
                });
    }
    
    // Select All functionality
    $(document).on('change', '#selectAllJobCategories', function() {
        $('.checkboxes').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });
    
    $(document).on('change', '.checkboxes', function() {
        var totalCheckboxes = $('.checkboxes').length;
        var checkedCheckboxes = $('.checkboxes:checked').length;
        $('#selectAllJobCategories').prop('checked', totalCheckboxes === checkedCheckboxes);
        toggleBulkDeleteButton();
    });
    
    function toggleBulkDeleteButton() {
        var checkedCount = $('.checkboxes:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteJobCategories').show();
        } else {
            $('#bulkDeleteJobCategories').hide();
        }
    }
    
    // Bulk Delete functionality
    $('#bulkDeleteJobCategories').on('click', function() {
        var selectedIds = [];
        $('.checkboxes:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            alert('Please select at least one item to delete.');
            return;
        }
        
        if (confirm('Are you sure you want to delete ' + selectedIds.length + ' selected item(s)?')) {
            $.ajax({
                url: '{{ route('bulk.delete.job.categories') }}',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        var table = $('#jobCategoryDatatableAjax').DataTable();
                        table.draw();
                        $('#selectAllJobCategories').prop('checked', false);
                        toggleBulkDeleteButton();
                    } else {
                        alert(response.message || 'Error deleting items');
                    }
                },
                error: function() {
                    alert('Error deleting items');
                }
            });
        }
    });
</script> 
@endpush