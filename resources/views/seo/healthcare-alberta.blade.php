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
                <h1>{{ __('Healthcare Jobs in Alberta') }}</h1>
                <p>{{ __('Browse current healthcare jobs across Alberta, including HCA, LPN, RN, and other healthcare careers in Edmonton, Calgary, Red Deer, Lethbridge, Medicine Hat, and surrounding communities.') }}</p>
            </div>
            <div class="pseo-salary-summary">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
            </div>
        </div>

        <h2 class="pseo-section-title">{{ __('Latest Healthcare Jobs') }}</h2>

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
            <h2>{{ __('Browse Jobs by City') }}</h2>
            <div class="row">
                @foreach($cityLinks as $link)
                    <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                        <a href="{{ $link['url'] }}" class="pseo-link-card">{{ $link['label'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pseo-related-section">
            <h2>{{ __('Popular Healthcare Roles') }}</h2>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                    <a href="{{ url('/hca-jobs-edmonton') }}" class="pseo-link-card">{{ __('HCA Jobs in Edmonton') }}</a>
                </div>
                <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                    <a href="{{ url('/lpn-jobs-edmonton') }}" class="pseo-link-card">{{ __('LPN Jobs in Edmonton') }}</a>
                </div>
                <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                    <a href="{{ url('/rn-jobs-edmonton') }}" class="pseo-link-card">{{ __('RN Jobs in Edmonton') }}</a>
                </div>
                <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                    <a href="{{ url('/hca-jobs-calgary') }}" class="pseo-link-card">{{ __('HCA Jobs in Calgary') }}</a>
                </div>
                <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                    <a href="{{ url('/lpn-jobs-calgary') }}" class="pseo-link-card">{{ __('LPN Jobs in Calgary') }}</a>
                </div>
                <div class="col-lg-3 col-md-4 col-6 pseo-grid-item">
                    <a href="{{ url('/rn-jobs-calgary') }}" class="pseo-link-card">{{ __('RN Jobs in Calgary') }}</a>
                </div>
            </div>
        </div>

        <div class="pseo-related-section">
            <h2>{{ __('FAQ') }}</h2>
            <div class="pseo-faq-item">
                <h3>{{ __('What healthcare jobs are available in Alberta?') }}</h3>
                <p>{{ __('Alberta offers a wide range of healthcare careers including Health Care Aides (HCA), Licensed Practical Nurses (LPN), Registered Nurses (RN), hospital staff, long-term care workers, home care aides, and clinic personnel across Edmonton, Calgary, Red Deer, Lethbridge, Medicine Hat, and other communities.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('How do I apply for healthcare jobs in Alberta?') }}</h3>
                <p>{{ __('Browse the latest healthcare job listings above, click on a position that matches your skills, and follow the application instructions. Many listings include direct application links or employer contact information.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('Which cities in Alberta have the most healthcare jobs?') }}</h3>
                <p>{{ __('Major healthcare employment hubs in Alberta include Edmonton and Calgary, with significant opportunities also available in Red Deer, Lethbridge, and Medicine Hat. Use the city links above to explore healthcare jobs in each location.') }}</p>
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
    .pseo-section-title {
        font-size: 24px;
        margin: 0 0 18px;
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
    .pseo-faq-item {
        margin-bottom: 16px;
    }
    .pseo-faq-item h3 {
        font-size: 16px;
        margin: 0 0 6px;
        color: #111827;
    }
    .pseo-faq-item p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
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
