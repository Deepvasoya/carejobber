@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li><a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i></li>
                <li><a href="{{ route('list.custom.fields') }}">{{ __('Custom Fields') }}</a> <i class="fa fa-circle"></i></li>
                <li><span>{{ __('Add') }}</span></li>
            </ul>
        </div>
        <!-- END PAGE BAR -->
        @include('flash::message')
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="header-title mb-0">{{ __('Create Custom Field') }}</h4>
                        <div class="d-flex gap-1">
                            <button type="submit" form="custom-field-form" class="btn btn-primary btn-sm"><i class="ri ri-check-line"></i></button>
                            <a href="{{ route('list.custom.fields') }}" class="btn btn-light btn-sm border"><i class="ri ri-close-line"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <form id="custom-field-form" method="post" action="{{ route('store.custom.field') }}">
                            @csrf
                            @include('admin.custom_field.forms.form', ['field' => null, 'fieldTypes' => $fieldTypes, 'contextLabels' => $contextLabels, 'optionsText' => ''])
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                <a href="{{ route('list.custom.fields') }}" class="btn btn-light border">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
