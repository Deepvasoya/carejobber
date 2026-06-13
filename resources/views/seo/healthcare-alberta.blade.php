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

        <div class="pseo-related-section">
            <h2>{{ __('Healthcare Jobs by City') }}</h2>
            <ul>
                @foreach($cityLinks as $link)
                    <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="pseo-related-section">
            <h2>{{ __('Popular Healthcare Roles') }}</h2>
            <ul>
                <li><a href="{{ url('/hca-jobs-edmonton') }}">{{ __('HCA Jobs in Edmonton') }}</a></li>
                <li><a href="{{ url('/lpn-jobs-edmonton') }}">{{ __('LPN Jobs in Edmonton') }}</a></li>
                <li><a href="{{ url('/rn-jobs-edmonton') }}">{{ __('RN Jobs in Edmonton') }}</a></li>
                <li><a href="{{ url('/hca-jobs-calgary') }}">{{ __('HCA Jobs in Calgary') }}</a></li>
                <li><a href="{{ url('/lpn-jobs-calgary') }}">{{ __('LPN Jobs in Calgary') }}</a></li>
                <li><a href="{{ url('/rn-jobs-calgary') }}">{{ __('RN Jobs in Calgary') }}</a></li>
            </ul>
        </div>

        <h2>{{ __('Latest Healthcare Jobs') }}</h2>

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
    .pseo-related-section ul {
        padding-left: 18px;
        margin-bottom: 0;
    }
    .pseo-related-section li {
        margin-bottom: 8px;
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
