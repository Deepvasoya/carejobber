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
                <li> <span>FAQ Sections</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <h3 class="page-title">Manage FAQ Sections <small>FAQ Sections</small> </h3>
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12"> 
                <!-- Begin: life time stats -->
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption"> <i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">FAQ Sections</span> </div>
                        <div class="actions">
                            <a href="{{ route('create.faq.section') }}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Add New Section</a>
                            <a href="{{ route('sort.faq.sections') }}" class="btn btn-xs btn-info"><i class="fa fa-sort"></i> Sort Sections</a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <form method="post" role="form" id="faq-section-search-form">
                                <table class="table table-striped table-bordered table-hover"  id="faq_section_datatable_ajax">
                                    <thead>
                                        <tr role="row" class="filter">                  
                                            <td>{!! Form::select('lang', ['' => 'Select Language']+$languages, config('default_lang'), array('id'=>'lang', 'class'=>'form-control')) !!}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr role="row" class="heading"> 
                                            <th>Language</th>                                               
                                            <th>Name</th>
                                            <th>Categories</th>
                                            <th>Status</th>
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
        var oTable = $('#faq_section_datatable_ajax').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: false,
            ajax: {
                url: '{!! route('fetch.data.faq.sections') !!}',
                data: function (d) {
                    d.lang = $('#lang').val();
                }
            }, columns: [
                {data: 'lang', name: 'lang'},
                {data: 'name', name: 'name'},
                {data: 'categories_count', name: 'categories_count', orderable: false},
                {data: 'is_active', name: 'is_active'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        
        $('#lang').on('change', function (e) {
            oTable.draw();
            e.preventDefault();
        });
        
        $(document).on('click', '.delete-faq-section', function() {
            var id = $(this).data('id');
            if (confirm('Are you sure! you want to delete this section? All categories in this section will be uncategorized.')) {
                $.post("{{ route('delete.faq.section') }}", {id: id, _method: 'DELETE', _token: '{{ csrf_token() }}'})
                    .done(function (response) {
                        if (response == 'ok') {
                            var table = $('#faq_section_datatable_ajax').DataTable();
                            table.row('#faq_section_dt_row_' + id).remove().draw(false);
                        } else {
                            alert('Request Failed!');
                        }
                    });
            }
        });
    });
</script> 
@endpush
