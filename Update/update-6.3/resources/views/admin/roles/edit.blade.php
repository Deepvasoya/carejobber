@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <!-- BEGIN CONTENT BODY -->
    <div class="page-content">
        <!-- BEGIN PAGE BAR -->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <a href="{{ route('list.roles') }}">Manage Roles</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Edit Role</span> </li>
            </ul>
        </div>
        <!-- END PAGE BAR -->
        <h3 class="page-title"> Edit Role <small>Roles & Permissions</small> </h3>
        @include('flash::message')
        <div class="row">
            <div class="col-md-8">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo"> <i class="icon-settings font-red-sunglo"></i> <span class="caption-subject bold uppercase">Edit Role Form</span> </div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model($role, ['method' => 'put', 'route' => ['update.role', $role->id], 'class' => 'form', 'novalidate' => 'novalidate']) !!}
                        {!! Form::hidden('id', $role->id) !!}
                        @include('admin.roles.add_edit_form')
                        <div class="form-actions mt-3">
                            {!! Form::submit('Update Role', ['class' => 'btn btn-primary']) !!}
                            <a href="{{ route('list.roles') }}" class="btn btn-default">Cancel</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
</div>
@endsection
