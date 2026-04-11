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
                <li> <span>Email Templates</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Manage Email Templates <small>Customize email notifications</small> </h3>
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <!-- Begin: life time stats -->
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption"> <i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">Email Templates</span> </div>
                        <div class="actions">
                            <button onclick="resetTemplates()" class="btn btn-xs btn-warning"><i class="fa fa-refresh"></i> Reset All to Default</button>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-info">
                            <strong>Note:</strong> Use shortcodes like {SITE_NAME}, {USER_NAME}, etc. in your templates. They will be automatically replaced with actual values when emails are sent.
                        </div>
                        <div class="table-container">
                            <form method="post" role="form" id="email-template-search-form">
                                <table class="table table-striped table-bordered table-hover"  id="email_template_datatable_ajax">
                                    <thead>
                                        <tr role="row" class="filter">                  
                                            <td><input type="text" class="form-control" name="name" id="name" placeholder="Search by name" autocomplete="off"></td>
                                            <td>
                                                <select name="category" id="category" class="form-control">
                                                    <option value="">All Categories</option>
                                                    @foreach($categories as $cat)
                                                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr role="row" class="heading"> 
                                            <th>Template Name</th>
                                            <th>Category</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table></form>
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
        var oTable = $('#email_template_datatable_ajax').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            ajax: {
                url: '{!! route('fetch.data.email.templates') !!}',
                data: function (d) {
                    d.name = $('input[name=name]').val();
                    d.category = $('#category').val();
                }
            }, columns: [
                {data: 'name', name: 'name'},
                {data: 'category', name: 'category'},
                {data: 'subject', name: 'subject'},
                {data: 'is_active', name: 'is_active'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        $('#email-template-search-form').on('submit', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#category').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#name').on('keyup', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
    
    function resetTemplates() {
        if (confirm('Are you sure you want to reset all email templates to default? This will overwrite any custom changes.')) {
            $.post("{{ route('reset.email.templates') }}", {_method: 'POST', _token: '{{ csrf_token() }}'})
                .done(function (response) {
                    if (response == 'ok') {
                        alert('Email templates have been reset to default!');
                        location.reload();
                    } else {
                        alert('Request Failed!');
                    }
                });
        }
    }
</script> 
@endpush
