@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><a href="{{ route('list.certifications') }}">Other Certifications</a> <i class="fa fa-circle"></i></li>
                <li><span>Edit Certification</span></li>
            </ul>
        </div>
        <br>
        @include('flash::message')
        <div class="row">
            <div class="col-md-6">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo"><i class="icon-settings font-red-sunglo"></i> <span class="caption-subject bold uppercase">Edit Certification</span></div>
                    </div>
                    <div class="portlet-body form">
                        {!! Form::model($certification, ['method' => 'put', 'route' => ['update.certification', $certification->id]]) !!}
                        {!! Form::hidden('id', $certification->id) !!}
                        @include('admin.certification.forms.form')
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection