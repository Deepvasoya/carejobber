@extends('layouts.app')

@section('page_title', $metaTitle)

@section('content')
@include('includes.header')
@include('flash::message')

<div class="listpgWraper pseo-jobs-page">
    <div class="container">
        <div class="pseo-jobs-header">
            <div>
                <p class="pseo-eyebrow">{{ __('Healthcare employer') }}</p>
                <h1>{{ $company->name }} {{ __('Jobs and Careers') }}</h1>
                <p>{{ __('Explore current') }} {{ $company->name }} {{ __('jobs and career opportunities on Medojob. Browse active openings, review available positions, and apply directly to healthcare employers.') }}</p>
            </div>
            <div class="pseo-salary-summary">
                <span>{{ __('Active jobs') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
            </div>
        </div>

        @if($company->description)
            <div class="pseo-related-section">
                <h2>{{ __('About') }} {{ $company->name }}</h2>
                <div class="employer-description">
                    @if($parsedDescription->p1)
                        <p>{{ $parsedDescription->p1 }}</p>
                    @endif
                    @if($parsedDescription->p2)
                        <p>{{ $parsedDescription->p2 }}</p>
                    @endif
                    <p>{{ $parsedDescription->dynamicParagraph }}</p>
                </div>
            </div>
        @else
            <div class="pseo-related-section">
                <h2>{{ __('About') }} {{ $company->name }}</h2>
                <div class="employer-description">
                    <p>{{ $parsedDescription->fallbackP1 }}</p>
                    <p>{{ $parsedDescription->fallbackP2 }}</p>
                    <p>{{ $parsedDescription->dynamicParagraph }}</p>
                </div>
            </div>
        @endif

        <h2 class="pseo-section-title">{{ __('Current Job Openings') }}</h2>

        <ul class="featuredlist row job-search-list-single">
            @forelse($jobs as $job)
                @php
                    $jobCompany = $job->getCompany();
                    $columnClass = 'col-12';
                @endphp
                @if($jobCompany)
                    @include('includes.job_search_list_card', ['job' => $job, 'company' => $jobCompany, 'columnClass' => $columnClass])
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

        @if($relatedEmployers)
            <div class="pseo-related-section">
                <h2>{{ __('Related Healthcare Employers') }}</h2>
                <div class="pseo-link-grid-4col">
                    @foreach($relatedEmployers as $link)
                        <a href="{{ $link['url'] }}" class="pseo-link-card">
                            <span class="pseo-link-card-label">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pseo-related-section">
            <h2>{{ __('FAQ') }}</h2>
            <div class="pseo-faq-item">
                <h3>{{ __('What jobs are available at') }} {{ $company->name }}?</h3>
                <p>{{ __('Browse the current job listings above to see available positions at') }} {{ $company->name }}. {{ __('Openings may include healthcare roles across various departments and locations.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('How do I apply for') }} {{ $company->name }} {{ __('jobs?') }}</h3>
                <p>{{ __('Click on any job listing above to view details and follow the application instructions. Many listings include direct application links or employer contact information.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('Does') }} {{ $company->name }} {{ __('hire healthcare workers?') }}</h3>
                <p>{{ __('Yes') }}. {{ $company->name }} {{ __('hires healthcare professionals including HCA, LPN, RN, and other healthcare roles. Check the current listings above for specific opportunities.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('Where are') }} {{ $company->name }} {{ __('jobs located?') }}</h3>
                <p>{{ __('Job locations vary by position. Browse the current openings above to find positions in your area.') }}</p>
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
    .employer-description p {
        margin: 0 0 12px;
        color: #374151;
        font-size: 15px;
        line-height: 1.7;
    }
    .employer-description p:last-child {
        margin-bottom: 0;
    }
    .pseo-link-grid-4col {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }
    .pseo-link-card {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        text-decoration: none;
        background: #f9fafb;
        transition: background 0.15s, border-color 0.15s;
    }
    .pseo-link-card:hover {
        background: #fff;
        border-color: #0d9488;
    }
    .pseo-link-card-label {
        display: block;
        color: #111827;
        font-weight: 600;
        font-size: 14px;
    }
    @media (max-width: 991px) {
        .pseo-link-grid-4col {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 767px) {
        .pseo-jobs-header {
            grid-template-columns: 1fr;
        }
        .pseo-jobs-header h1 {
            font-size: 28px;
        }
        .pseo-link-grid-4col {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
@include('includes.job_list_apply_scripts_auth')
@endpush
