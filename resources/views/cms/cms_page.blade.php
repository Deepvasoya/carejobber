@extends('layouts.app')
@section('content')

@php
    $fullWidthSlugs = ['employer-zone'];
    $currentSlug = request()->segment(count(request()->segments()));
    $isFullWidth = in_array($currentSlug, $fullWidthSlugs);
@endphp

<!-- Header start -->
@include('includes.header')
<!-- Header end -->

@if(!$isFullWidth)
    <!-- Inner Page Title start -->
    @include('includes.inner_top_search')
    <!-- Inner Page Title end -->
    <div class="about-wraper">
        <div class="container">
            <h1>{{$cmsContent->page_title}}</h1>
            {!! $cmsContent->page_content !!}
        </div>
    </div>
@else
    <style>
        body .page-content > *:not(.medojob-fullwidth-wrapper):not(footer):not(header):not(#page-loader) { display: none !important; }
        .medojob-fullwidth-wrapper { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .medojob-fullwidth-wrapper > * { margin: 0 !important; }
        body header + .medojob-fullwidth-wrapper,
        body .header + .medojob-fullwidth-wrapper { margin-top: 0 !important; padding-top: 0 !important; }
    </style>
    <div class="medojob-fullwidth-wrapper" style="margin: 0 !important; padding: 0 !important; width: 100% !important;">
        {!! $cmsContent->page_content !!}
    </div>
@endif

@include('includes.footer')
@endsection