@extends('admin.layouts.admin_layout')
@section('content')
<div class="page-bar mb-3">
    <ul class="page-breadcrumb mb-0">
        <li><a href="{{ route('admin.home') }}">Home</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><span>{{ $page->title }} {{ __('Components') }}</span></li>
    </ul>
</div>
@if(session()->has('message.added'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{ __('Task Done!') }}</strong> {!! session('message.content') !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(null !== $widgets)
    @foreach($widgets as $wid)
    <?php $widget_data = \App\Models\WidgetsData::where('widget_id', $wid->id)->first(); ?>
    <div class="card mb-4" id="widget_{{ $wid->id }}">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="ri ri-widget-line me-1"></i>{{ $wid->title }} Widget</h5>
        </div>
        <div class="card-body">
            @if(null !== $widget_data)
                {!! Form::model($widget_data, ['method' => 'post', 'route' => ['admin.widget_data.store', $wid->id], 'class' => 'form', 'files' => true]) !!}
            @else
                {!! Form::open(['method' => 'post', 'route' => ['admin.widget_data.store', $wid->id], 'class' => 'form', 'files' => true]) !!}
            @endif
            {!! Form::hidden('id', $wid->id) !!}
            @include('admin.widgets_data.inc.form')
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @endforeach
@endif
@endsection
@push('css')
<link href="{{ asset('theme/plugins/filer/css/jquery.filer.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('theme/plugins/filer/css/themes/jquery.filer-dragdropbox-theme.css') }}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
<script src="{{ asset('theme/plugins/filer/js/jquery.filer.min.js') }}"></script>
@include('admin.widgets_data.widgetfiler')
@endpush