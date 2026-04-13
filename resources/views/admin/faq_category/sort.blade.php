@extends('admin.layouts.admin_layout')

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    #sortable {
        list-style-type: none;
        margin: 20px 0;
        padding: 0;
        width: 100%;
    }
    #sortable li {
        margin: 5px 0;
        padding: 15px;
        font-size: 14px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    #sortable li:hover {
        background: #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    #sortable li i {
        margin-right: 10px;
        color: #6c757d;
    }
    #sortable .ui-sortable-helper {
        opacity: 0.8;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    #sortable .ui-sortable-placeholder {
        border: 2px dashed #007bff;
        background: #e7f3ff;
        visibility: visible !important;
        height: 50px !important;
    }

    .ui-sortable-handle{
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="page-content-wrapper"> 
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content"> 
        <!-- BEGIN PAGE HEADER--> 
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <a href="{{ route('list.faq.categories') }}">FAQ Categories</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Sort FAQ Categories</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR --> 
        <!-- BEGIN PAGE TITLE-->
        <!--<h3 class="page-title">Edit User <small>Users</small> </h3>-->
        <!-- END PAGE TITLE--> 
        <!-- END PAGE HEADER-->
        <br />
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo"> <i class="icon-settings font-red-sunglo"></i> <span class="caption-subject bold uppercase">Sort FAQ Categories</span> </div>
                    </div>
                    <div class="portlet-body form">          
                        <ul class="nav nav-tabs">              
                            <li class="active"> <a href="#Details" data-bs-toggle="tab" aria-expanded="false"> Sort FAQ Categories </a> </li>
                        </ul>
                        <div class="tab-content">              
                            <div class="tab-pane fade active in show" id="Details"> @include('admin.faq_category.forms.sort') </div>
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
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
@endpush
