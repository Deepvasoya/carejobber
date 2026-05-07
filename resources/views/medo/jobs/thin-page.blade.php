@extends('layouts.app')

{{-- Issue 3: Dynamic page title (Section 9 of brief) --}}
@section('page_title'){{ $category->name }} Jobs in {{ $city->name }}, {{ strtoupper($province->slug) }} | Medojob@endsection

{{-- Issue 4: noindex for thin pages (Section 7 of brief) --}}
@push('robots_meta')
<meta name="robots" content="noindex, follow">
@endpush

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
                <h1>{{ $category->name }} {{ __('Jobs in') }} {{ $city->name }}, {{ strtoupper($province->slug) }}</h1>
                <p class="mb-0">
                    {{ __('Be the first to know when') }} {{ $category->name }} {{ __('positions open up in') }} {{ $city->name }}.
                    {{ __('Set up a free job alert and we\'ll email you as soon as new listings arrive.') }}
                </p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount ?? 0) }}</strong>
                <span>{{ __('More coming soon') }}</span>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <div class="medo-alert-cta">
                <div class="medo-alert-cta__icon">
                    <i class="fas fa-bell" aria-hidden="true"></i>
                </div>
                <div class="medo-alert-cta__copy">
                    <h2>{{ __('No listings yet — but don\'t miss out') }}</h2>
                    <p>
                        {{ __('We\'re actively building out') }} <strong>{{ $city->name }}</strong> {{ __('coverage.') }}
                        {{ __('Create a free job alert and you\'ll be notified the moment a') }}
                        <strong>{{ $category->name }}</strong> {{ __('role is posted in your area.') }}
                    </p>
                    <a href="{{ route('my-alerts') }}" class="btn btn-success">
                        <i class="fas fa-bell me-1" aria-hidden="true"></i>
                        {{ __('Set Up a Free Job Alert') }}
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')

@push('styles')
<style>
.medo-alert-cta {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    background: #f0faf4;
    border: 1px solid #c3e6cb;
    border-radius: 12px;
    padding: 2rem;
}
.medo-alert-cta__icon {
    flex-shrink: 0;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #28a745;
    display: flex;
    align-items: center;
    justify-content: center;
}
.medo-alert-cta__icon i {
    color: #fff;
    font-size: 1.5rem;
}
.medo-alert-cta__copy h2 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #155724;
}
.medo-alert-cta__copy p {
    color: #383d41;
    margin-bottom: 1rem;
}
@media (max-width: 576px) {
    .medo-alert-cta { flex-direction: column; }
}
</style>
@endpush
