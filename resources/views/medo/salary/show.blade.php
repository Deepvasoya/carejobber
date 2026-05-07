@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ $province->name }} {{ __('salary guide') }}</p>
                <h1>{{ $category->name }} {{ __('salary guide for') }} {{ $province->name }}</h1>
                <p class="mb-0">{{ __('Salary data is calculated from active postings that publish wage ranges.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Listings analyzed') }}</span>
                <strong>{{ number_format($salary['count']) }}</strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Current wage signal') }}</h2>
            @if($salary['count'] > 0)
                <div class="medo-pseo-grid">
                    <div><span>{{ __('Average low') }}</span><strong>${{ number_format($salary['avg_min'], 2) }}</strong></div>
                    <div><span>{{ __('Average high') }}</span><strong>${{ number_format($salary['avg_max'], 2) }}</strong></div>
                    <div><span>{{ __('Observed range') }}</span><strong>${{ number_format($salary['min'], 2) }} - ${{ number_format($salary['max'], 2) }}</strong></div>
                </div>
            @else
                <div class="medo-muted-box">{{ __('Salary data will appear here once imported jobs include wage ranges.') }}</div>
            @endif
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
