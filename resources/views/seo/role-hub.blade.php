@extends('layouts.app')

@section('page_title', $metaTitle)

@section('content')
@include('includes.header')
@include('flash::message')

<div class="listpgWraper pseo-jobs-page">
    <div class="container">
        <div class="pseo-jobs-header">
            <div>
                <p class="pseo-eyebrow">{{ __('Healthcare Jobs in Alberta') }}</p>
                <div class="pseo-breadcrumb">
    <a href="{{ url('/') }}">Home</a>
    <span>›</span>
    <a href="{{ url('/jobs') }}">Jobs</a>
    <span>›</span>
    <span>{{ $roleLabel }} Jobs in Alberta</span>
</div>
                <h1>{{ $roleLabel }} {{ __('Jobs in Alberta') }}</h1>
                <p>{{ __('Browse') }} {{ $roleLabel }} {{ __('jobs across Alberta. Explore opportunities in various Alberta communities, connect with healthcare employers, and find the right role for you.') }}</p>
            </div>
            <div class="pseo-salary-summary">
                <span>{{ __('Active jobs across Alberta') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
            </div>
        </div>

        @if($cities->isNotEmpty())
            <div class="pseo-related-section">
                <h2>{{ __('Related Cities') }}</h2>
                <div class="pseo-link-grid-4col">
                    @foreach($cityLinks as $link)
                        <a href="{{ $link['url'] }}" class="pseo-link-card">
                            <span class="pseo-link-card-label">{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <h2 class="pseo-section-title">{{ __('Current') }} {{ $roleLabel }} {{ __('Job Openings in Alberta') }}</h2>

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

        @if($relatedRoles)
            <div class="pseo-related-section">
                <h2>{{ __('Popular Roles in Alberta') }}</h2>
                <div class="pseo-link-grid-4col">
                    @foreach($relatedRoles as $link)
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
                <h3>{{ __('What') }} {{ $roleLabel }} {{ __('jobs are available in Alberta?') }}</h3>
                <p>{{ __('Browse the current job listings above for') }} {{ $roleLabel }} {{ __('positions available in Alberta. Openings vary by location and employer.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('How do I apply for') }} {{ $roleLabel }} {{ __('jobs in Alberta?') }}</h3>
                <p>{{ __('Click on any job listing to view the full details and follow the application instructions provided by the employer.') }}</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('Where can I find') }} {{ $roleLabel }} {{ __('jobs in Alberta?') }}</h3>
                <p>{{ $roleLabel }} {{ __('jobs are available across Alberta. Use the Related Cities section above to find openings in specific communities.') }}</p>
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
    .pseo-breadcrumb {
    margin-bottom: 12px;
    font-size: 14px;
    color: #6b7280;
}

.pseo-breadcrumb a {
    color: #0d9488;
    text-decoration: none;
}

.pseo-breadcrumb a:hover {
    text-decoration: underline;
}

.pseo-breadcrumb span {
    margin: 0 4px;
}
</style>
@endpush

@push('scripts')
@include('includes.job_list_apply_scripts_auth')

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => url('/')
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Jobs',
            'item' => url('/jobs')
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $roleLabel . ' Jobs in Alberta',
            'item' => url()->current()
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush
