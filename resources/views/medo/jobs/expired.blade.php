@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Expired job') }}</p>
                <h1>{{ $job->title }}</h1>
                <p class="mb-0">{{ __('This job is no longer active.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Status') }}</span>
                <strong style="font-size: 24px;">{{ __('Expired') }}</strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <a class="medo-button" href="{{ route('medo.jobs.category.province.city', [$category, $province, $city]) }}">
                {{ __('View current jobs in this city') }}
            </a>
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
