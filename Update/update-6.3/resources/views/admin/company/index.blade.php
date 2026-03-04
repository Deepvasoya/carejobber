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
                <li> <span>Companies</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Manage Companies  </h3>
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <!-- Begin: life time stats -->
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                       
                        <div class="actions"> 
                            <a href="{{ route('create.company') }}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Add New Company</a>
                            <button type="button" id="bulkDeleteCompanies" class="btn btn-xs btn-danger" style="display:none;"><i class="fa fa-trash"></i> Bulk Delete</button>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <form method="post" role="form" id="datatable-search-form">
                                <table class="table table-striped table-bordered table-hover"  id="companyDatatableAjax">
                                    <thead>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control" name="name" id="name" autocomplete="off" placeholder="Company Name"></td>
                                            <td><input type="text" class="form-control" name="email" id="email" autocomplete="off" placeholder="Company Email"></td>
                                            <td><select name="is_active" id="is_active" class="form-control">
                                                    <option value="-1">Is Active?</option>
                                                    <option value="1" selected="selected">Active</option>
                                                    <option value="0">In Active</option>
                                                </select></td>
                                            <td><select name="is_featured" id="is_featured" class="form-control">
                                                    <option value="-1">Is Featured?</option>
                                                    <option value="1">Featured</option>
                                                    <option value="0">Not Featured</option>
                                                </select></td>
                                            <td></td>
                                        </tr>
                                        <tr role="row" class="heading">
                                            <th><input type="checkbox" id="selectAllCompanies" /></th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Is Active?</th>
                                            <th>Is Featured?</th>
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
        var oTable = $('#companyDatatableAjax').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            /*		
             "order": [[1, "asc"]],            
             paging: true,
             info: true,
             */
            ajax: {
                url: '{!! route('fetch.data.companies') !!}',
                data: function (d) {
                    d.name = $('#name').val();
                    d.email = $('#email').val();
                    d.is_active = $('#is_active').val();
                    d.is_featured = $('#is_featured').val();
                }
            }, columns: [
                {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'is_active', name: 'is_active'},
                {data: 'is_featured', name: 'is_featured'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        $('#datatable-search-form').on('submit', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#name').on('keyup', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#email').on('keyup', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#is_active').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#is_featured').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
    function deleteCompany(id) {
        var msg = 'Are you sure?';
        if (confirm(msg)) {
            $.post("{{ route('delete.company') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        if (response == 'ok')
                        {
                            var table = $('#companyDatatableAjax').DataTable();
                            table.row('companyDtRow' + id).remove().draw(false);
                        } else
                        {
                            alert('Request Failed!');
                        }
                    });
        }
    }
    function makeActive(id) {
        $.post("{{ route('make.active.company') }}", {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok')
                    {
                        var table = $('#companyDatatableAjax').DataTable();
                        table.row('companyDtRow' + id).remove().draw(false);
                    } else
                    {
                        alert('Request Failed!');
                    }
                });
    }
    function makeNotActive(id) {
        $.post("{{ route('make.not.active.company') }}", {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok')
                    {
                        var table = $('#companyDatatableAjax').DataTable();
                        table.row('companyDtRow' + id).remove().draw(false);
                    } else
                    {
                        alert('Request Failed!');
                    }
                });
    }
    function makeFeatured(id) {
        $.post("{{ route('make.featured.company') }}", {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok')
                    {
                        var table = $('#companyDatatableAjax').DataTable();
                        table.row('companyDtRow' + id).remove().draw(false);
                    } else
                    {
                        alert('Request Failed!');
                    }
                });
    }
    function makeNotFeatured(id) {
        $.post("{{ route('make.not.featured.company') }}", {id: id, _method: 'PUT', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok')
                    {
                        var table = $('#companyDatatableAjax').DataTable();
                        table.row('companyDtRow' + id).remove().draw(false);
                    } else
                    {
                        alert('Request Failed!');
                    }
                });
    }
    
    // Select All functionality
    $(document).on('change', '#selectAllCompanies', function() {
        $('.checkboxes').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });
    
    $(document).on('change', '.checkboxes', function() {
        var totalCheckboxes = $('.checkboxes').length;
        var checkedCheckboxes = $('.checkboxes:checked').length;
        $('#selectAllCompanies').prop('checked', totalCheckboxes === checkedCheckboxes);
        toggleBulkDeleteButton();
    });
    
    function toggleBulkDeleteButton() {
        var checkedCount = $('.checkboxes:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteCompanies').show();
        } else {
            $('#bulkDeleteCompanies').hide();
        }
    }
    
    // Bulk Delete functionality
    $('#bulkDeleteCompanies').on('click', function() {
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
                url: '{{ route('bulk.delete.companies') }}',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        var table = $('#companyDatatableAjax').DataTable();
                        table.draw();
                        $('#selectAllCompanies').prop('checked', false);
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