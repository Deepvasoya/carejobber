@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <nav class="medo-breadcrumbs">
            @foreach($breadcrumbs as $breadcrumb)
                @if($breadcrumb['url'])
                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                    <span>/ </span>
                @else
                    <span>{{ $breadcrumb['label'] }}</span>
                @endif
            @endforeach
        </nav>

        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ optional($employer)->name ?? __('Healthcare employer') }}</p>
                <h1>{{ $job->title }}</h1>
                <p class="mb-0">
                    {{ $city->name }}, {{ strtoupper($province->slug) }}
                    @if($job->facility_name)
                        - {{ $job->facility_name }}
                    @endif
                </p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Category') }}</span>
                <strong style="font-size: 24px;">{{ $category->name }}</strong>
                @if($job->posted_at)
                    <span>{{ __('Posted') }} {{ $job->posted_at->format('M j, Y') }}</span>
                @endif
            </div>
        </section>

        <div class="row">
            <div class="col-lg-8">
                <section class="medo-pseo-panel">
                    <h2>{{ __('Job details') }}</h2>
                    <div>{!! nl2br(e($job->description)) !!}</div>
                </section>
            </div>
            <aside class="col-lg-4">
                <section class="medo-pseo-panel">
                    <h3>{{ __('Apply') }}</h3>
                    @if($job->apply_url)
                        <a class="medo-button" href="{{ $job->apply_url }}" rel="nofollow noopener" target="_blank">
                            {{ __('Apply on employer site') }}
                        </a>
                    @else
                        <div class="medo-muted-box">{{ __('No external application link is available for this job.') }}</div>
                    @endif
                </section>

                @if($relatedJobs->count())
                    <section class="medo-pseo-panel">
                        <h3>{{ __('Related jobs') }}</h3>
                        <ul>
                            @foreach($relatedJobs as $relatedJob)
                                <li>
                                    <a href="{{ route('medo.jobs.detail', [$relatedJob->category, $relatedJob->province, $relatedJob->city, $relatedJob]) }}">
                                        {{ $relatedJob->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
