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
                <li> <span>FAQ Categories</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Manage FAQ Categories <small>FAQ Categories</small> </h3>
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <!-- Begin: life time stats -->
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption"> <i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">FAQ Categories</span> </div>
                        <div class="actions">
                            <a href="{{ route('create.faq.category') }}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Add New Category</a>
                            <a href="{{ route('sort.faq.categories') }}" class="btn btn-xs btn-info"><i class="fa fa-sort"></i> Sort Categories</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <form method="post" role="form" id="faq-category-search-form">
                                <table class="table table-striped table-bordered table-hover"  id="faq_category_datatable_ajax">
                                    <thead>
                                        <tr role="row" class="filter">                  
                                            <td>{!! Form::select('lang', ['' => 'Select Language']+$languages, config('default_lang'), array('id'=>'lang', 'class'=>'form-control')) !!}</td>
                                            <td><input type="text" class="form-control" name="name" id="name" autocomplete="off"></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr role="row" class="heading"> 
                                            <th>Language</th>                                               
                                            <th>Name</th>
                                            <th>Description</th>
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
        var oTable = $('#faq_category_datatable_ajax').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            ajax: {
                url: '{!! route('fetch.data.faq.categories') !!}',
                data: function (d) {
                    d.lang = $('#lang').val();
                    d.name = $('input[name=name]').val();
                }
            }, columns: [
                {data: 'lang', name: 'lang'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'is_active', name: 'is_active'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        $('#faq-category-search-form').on('submit', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#lang').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        $('#name').on('keyup', function (e) {
            oTable.draw();
            e.preventDefault();
        });
    });
    function delete_faq_category(id) {
        if (confirm('Are you sure! you want to delete this category? All FAQs in this category will be uncategorized.')) {
            $.post("{{ route('delete.faq.category') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        if (response == 'ok')
                        {
                            var table = $('#faq_category_datatable_ajax').DataTable();
                            table.row('faq_category_dt_row_' + id).remove().draw(false);
                        } else
                        {
                            alert('Request Failed!');
                        }
                    });
        }
    }
</script> 
@endpush
