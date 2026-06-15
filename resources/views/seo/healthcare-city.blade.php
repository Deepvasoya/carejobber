@extends('layouts.app')

@section('page_title', $metaTitle)

@section('content')
@include('includes.header')
@include('flash::message')

<div class="listpgWraper pseo-jobs-page">
    <div class="container">
        <div class="pseo-jobs-header">
            <div>
                <p class="pseo-eyebrow">{{ __('Healthcare jobs in Alberta') }}</p>
                <h1>{{ __('Healthcare Jobs in') }} {{ $cityName }}</h1>
                <p>{{ __('Browse current healthcare jobs in') }} {{ $cityName }}, {{ $stateName }}. Medojob connects healthcare professionals with employers across {{ $cityName }} — including hospitals, long-term care homes, assisted living facilities, home care agencies, clinics, and community health organizations.</p></br>

                <p>There are currently <strong>{{ number_format($jobCount) }} healthcare job openings</strong> in {{ $cityName }}. Whether you're a Health Care Aide (HCA), Licensed Practical Nurse (LPN), Registered Nurse (RN), Nurse Practitioner (NP), unit clerk, therapist, or work in food services, housekeeping, or facility maintenance, you'll find current openings here and across {{ $stateName }}. Positions range from full-time and part-time to casual and temporary, with day, evening, weekend, and overnight shifts available.</p></br>

               <p>Browse current listings below, save the ones that interest you, and apply directly to employers hiring in {{ $cityName }}. New jobs are added regularly, so check back often, or <a href="https://medojob.com/my-alerts">set up a job alert</a> to be notified when matching positions are posted.</p>
            </div>
            <div class="pseo-salary-summary">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
            </div>
        </div>

        <div class="pseo-related-section">
            <h2>{{ __('Popular Roles in') }} {{ $cityName }}</h2>
            <div class="row">
                @foreach($roleLinks as $link)
                    <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                        <a href="{{ $link['url'] }}" class="pseo-link-card">{{ $link['label'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>

        <h2>{{ __('Latest Healthcare Jobs in') }} {{ $cityName }}</h2>

        <ul class="featuredlist row job-search-list-single">
            @forelse($jobs as $job)
                @php
                    $company = $job->getCompany();
                    $columnClass = 'col-12';
                @endphp
                @if($company)
                    @include('includes.job_search_list_card', ['job' => $job, 'company' => $company, 'columnClass' => $columnClass])
                @endif
            @empty
                <li class="col-12">
                    <div class="nodatabox">
                        <h4>{{ __('There are currently no open positions available.') }}</h4>
                    </div>
                </li>
            @endforelse
        </ul>

        {{ $jobs->links() }}

        <div class="pseo-related-section">
            <h2>{{ __('Related Cities') }}</h2>
            <div class="row">
                @foreach($relatedCities as $link)
                    <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                        <a href="{{ $link['url'] }}" class="pseo-link-card">{{ $link['label'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('includes.job_list_apply_modal')
@include('includes.footer')
@endsection

@include('includes.job_list_search_styles')

@push('styles')
<style>
    .pseo-jobs-header {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 240px;
        gap: 24px;
        align-items: start;
        margin-bottom: 24px;
    }
    .pseo-jobs-header h1 {
        margin: 0 0 10px;
        font-size: 34px;
        line-height: 1.2;
    }
    .pseo-eyebrow {
        margin: 0 0 6px;
        color: #0f766e;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
    }
    .pseo-salary-summary {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        padding: 18px;
    }
    .pseo-salary-summary span {
        display: block;
        color: #64748b;
        font-size: 13px;
    }
    .pseo-salary-summary strong {
        display: block;
        font-size: 36px;
        line-height: 1.1;
        color: #111827;
        margin: 6px 0 10px;
    }
    .pseo-related-section {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        padding: 18px;
        margin-top: 24px;
    }
    .pseo-related-section h2 {
        font-size: 20px;
        margin: 0 0 14px;
    }
    .pseo-grid-item {
        margin-bottom: 10px;
    }
    .pseo-link-card {
        display: block;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #f9fafb;
        color: #0f766e;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s, border-color 0.2s, box-shadow 0.2s;
    }
    .pseo-link-card:hover {
        background: #fff;
        border-color: #0f766e;
        box-shadow: 0 2px 8px rgba(15, 118, 110, 0.12);
        color: #0f766e;
    }
    @media (max-width: 767px) {
        .pseo-jobs-header {
            grid-template-columns: 1fr;
        }
        .pseo-jobs-header h1 {
            font-size: 28px;
        }
    }
</style>
@endpush

@push('scripts')
@include('includes.job_list_apply_scripts_auth')
@endpush
