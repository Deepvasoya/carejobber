@extends('layouts.app')

@section('page_title', $category->name . ' Jobs in ' . $province->name . ' | Medojob')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <nav class="medo-breadcrumbs">
            <a href="{{ route('medo.jobs.category', $category) }}">{{ $category->name }}</a>
            <span>/ {{ $province->name }}</span>
        </nav>

        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Healthcare jobs in') }} {{ $province->name }}</p>
                <h1>{{ $category->name }} {{ __('jobs in') }} {{ $province->name }}</h1>
                <p class="mb-0">{{ __('Choose a city to view current category-city job pages.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Active cities') }}</span>
                <strong>{{ number_format($cities->count()) }}</strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Available cities') }}</h2>
            @if($cities->count())
                <ul class="medo-link-list">
                    @foreach($cities as $city)
                        <li>
                            <a href="{{ route('medo.jobs.category.province.city', [$category, $province, $city]) }}">
                                {{ $city->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="medo-muted-box">{{ __('No city pages have enough active jobs for this category yet.') }}</div>
            @endif
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
