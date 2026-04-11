@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper"> 
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content"> 
        <!-- BEGIN PAGE HEADER--> 
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <a href="{{ route('list.email.templates') }}">Email Templates</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Edit Email Template</span> </li>
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
                        <div class="caption font-red-sunglo"> <i class="icon-settings font-red-sunglo"></i> <span class="caption-subject bold uppercase">Email Template Form</span> </div>
                        <div class="actions">
                            <a href="{{ route('list.email.templates') }}" class="btn btn-xs btn-info"><i class="fa fa-list"></i> All Templates</a>
                        </div>
                    </div>
                    <div class="portlet-body form">          
                        <ul class="nav nav-tabs">              
                            <li class="active"> <a href="#Details" data-bs-toggle="tab" aria-expanded="false"> Email Template </a> </li>
                        </ul>
                        {!! Form::model($emailTemplate, array('method' => 'put', 'route' => array('update.email.template', $emailTemplate->id), 'class' => 'form')) !!}
                        {!! Form::hidden('id', $emailTemplate->id) !!}            
                        <div class="tab-content">              
                            <div class="tab-pane fade active in show" id="Details"> @include('admin.email_template.forms.form') </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- END CONTENT BODY --> 
    </div>
    @endsection
