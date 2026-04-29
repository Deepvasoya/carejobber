@extends('layouts.app')

@section('content')
@include('includes.header')

<div class="listpgWraper pseo-salary-page">
    <div class="container">
        <div class="pseo-salary-hero">
            <p class="pseo-eyebrow">{{ __('Alberta salary guide') }}</p>
            <h1>{{ $categoryLabel }} {{ __('salary guide for Alberta') }}</h1>
            <p>{{ __('This guide summarizes salary ranges from active healthcare job listings where employers publish compensation.') }}</p>
        </div>

        <div class="pseo-salary-grid">
            <div>
                <span>{{ __('Listings analyzed') }}</span>
                <strong>{{ number_format($salary['count']) }}</strong>
            </div>
            <div>
                <span>{{ __('Average low') }}</span>
                <strong>@if($salary['count']) ${{ number_format($salary['avg_from']) }} @else {{ __('N/A') }} @endif</strong>
            </div>
            <div>
                <span>{{ __('Average high') }}</span>
                <strong>@if($salary['count']) ${{ number_format($salary['avg_to']) }} @else {{ __('N/A') }} @endif</strong>
            </div>
            <div>
                <span>{{ __('Observed range') }}</span>
                <strong>@if($salary['count']) ${{ number_format($salary['min_from']) }} - ${{ number_format($salary['max_to']) }} @else {{ __('N/A') }} @endif</strong>
            </div>
        </div>

        <div class="pseo-copy">
            <h2>{{ __('Current market signal') }}</h2>
            <p>
                {{ __('Medojob calculates this range from active Alberta postings, so it changes as employers add, expire, or update jobs.') }}
                {{ __('For the best comparison, review the live listings and check whether each employer publishes hourly, annual, casual, part-time, or full-time compensation.') }}
            </p>
            <a class="btn btn-primary" href="{{ route('seo.jobs.category', $category->slug) }}">{{ __('View active jobs') }}</a>
        </div>
    </div>
</div>

@include('includes.footer')
@endsection

@push('styles')
<style>
    .pseo-salary-hero {
        max-width: 760px;
        margin-bottom: 24px;
    }
    .pseo-salary-hero h1 {
        font-size: 34px;
        margin: 0 0 10px;
    }
    .pseo-eyebrow {
        margin: 0 0 6px;
        color: #0f766e;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
    }
    .pseo-salary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 26px;
    }
    .pseo-salary-grid > div,
    .pseo-copy {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        padding: 18px;
    }
    .pseo-salary-grid span {
        display: block;
        color: #64748b;
        font-size: 13px;
    }
    .pseo-salary-grid strong {
        display: block;
        color: #111827;
        font-size: 24px;
        line-height: 1.2;
        margin-top: 6px;
    }
    .pseo-copy {
        max-width: 820px;
    }
    .pseo-copy h2 {
        font-size: 22px;
        margin-bottom: 10px;
    }
    @media (max-width: 767px) {
        .pseo-salary-grid {
            grid-template-columns: 1fr;
        }
        .pseo-salary-hero h1 {
            font-size: 28px;
        }
    }
</style>
@endpush
