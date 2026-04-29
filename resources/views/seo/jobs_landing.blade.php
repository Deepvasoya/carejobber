@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<div class="listpgWraper pseo-jobs-page">
    <div class="container">
        <div class="pseo-jobs-header">
            <div>
                <p class="pseo-eyebrow">{{ __('Healthcare jobs in Alberta') }}</p>
                <h1>{{ $content['h1'] }}</h1>
                <p>{{ $content['intro'] }}</p>
            </div>
            <div class="pseo-salary-summary">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
                <a href="{{ route('seo.salary', $category->slug) }}">{{ __('View salary guide') }}</a>
            </div>
        </div>

        <div class="pseo-salary-band">
            <h2>{{ $content['salary_heading'] }}</h2>
            @if($salary['count'] > 0)
                <div class="pseo-salary-grid">
                    <div><span>{{ __('Average low') }}</span><strong>${{ number_format($salary['avg_from']) }}</strong></div>
                    <div><span>{{ __('Average high') }}</span><strong>${{ number_format($salary['avg_to']) }}</strong></div>
                    <div><span>{{ __('Observed range') }}</span><strong>${{ number_format($salary['min_from']) }} - ${{ number_format($salary['max_to']) }}</strong></div>
                </div>
            @else
                <p>{{ __('Salary data will appear here when more employers publish visible ranges for this category.') }}</p>
            @endif
        </div>

        <div class="row">
            <div class="col-lg-8">
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

            <div class="col-lg-4">
                @if(count($internalLinks))
                    <div class="pseo-side-section">
                        <h2>{{ __('Related searches') }}</h2>
                        <ul>
                            @foreach($internalLinks as $link)
                                <li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="pseo-side-section">
                    <h2>{{ __('Questions') }}</h2>
                    @foreach($content['faqs'] as $faq)
                        <details>
                            <summary>{{ $faq['question'] }}</summary>
                            <p>{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
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
    .pseo-salary-summary,
    .pseo-salary-band,
    .pseo-side-section {
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
    .pseo-salary-band {
        margin-bottom: 24px;
    }
    .pseo-salary-band h2,
    .pseo-side-section h2 {
        font-size: 20px;
        margin: 0 0 14px;
    }
    .pseo-salary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .pseo-salary-grid span {
        display: block;
        color: #64748b;
        font-size: 13px;
    }
    .pseo-salary-grid strong {
        display: block;
        color: #111827;
        font-size: 22px;
    }
    .pseo-side-section {
        margin-bottom: 18px;
    }
    .pseo-side-section ul {
        padding-left: 18px;
        margin-bottom: 0;
    }
    .pseo-side-section details {
        border-top: 1px solid #e5e7eb;
        padding: 12px 0;
    }
    .pseo-side-section details:first-of-type {
        border-top: 0;
    }
    .pseo-side-section summary {
        cursor: pointer;
        font-weight: 700;
    }
    .pseo-side-section p {
        margin: 8px 0 0;
    }
    @media (max-width: 767px) {
        .pseo-jobs-header,
        .pseo-salary-grid {
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
