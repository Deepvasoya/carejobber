@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <nav class="medo-breadcrumbs">
            <a href="{{ route('jobs.category', $category) }}">{{ $category->name }}</a>
            <span>/ </span>
            <a href="{{ route('jobs.category.province', [$category, $province]) }}">{{ $province->name }}</a>
            <span>/ {{ $city->name }}</span>
        </nav>

        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Healthcare jobs in') }} {{ $city->name }}</p>
                <h1>{{ $category->name }} {{ __('jobs in') }} {{ $city->name }}, {{ strtoupper($province->slug) }}</h1>
                <p class="mb-0">
                    {{ __('This category-city page is available, but it is currently held out of search indexing until more active jobs are available.') }}
                </p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount ?? 0) }}</strong>
                <span>{{ __('Noindex below 3 jobs') }}</span>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Current status') }}</h2>
            <div class="medo-muted-box">
                {{ __('There are not enough active listings for this page to be shown as a full pSEO landing page yet.') }}
            </div>
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
