@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Employer') }}</p>
                <h1>{{ $employer->name }}</h1>
                <p class="mb-0">{{ __('Current healthcare openings from this employer.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Open jobs') }}</h2>
            @if($jobs->count())
                <ul class="medo-job-list">
                    @foreach($jobs as $job)
                        @include('medo.jobs.partials.job-card', [
                            'job' => $job,
                            'category' => $job->category,
                            'province' => $job->province,
                            'city' => $job->city,
                        ])
                    @endforeach
                </ul>
            @else
                <div class="medo-muted-box">{{ __('No active jobs are available for this employer yet.') }}</div>
            @endif
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
