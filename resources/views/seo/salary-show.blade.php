@extends('layouts.app')
@section('page_title', $metaTitle)
@section('content')
@include('includes.header')
@include('flash::message')
<div class="listpgWraper pseo-jobs-page">
    <div class="container">
        <div class="pseo-jobs-header">
            <div>
                <p class="pseo-eyebrow">{{ __('Salary Information') }}</p>
                <div class="pseo-breadcrumb">
    <a href="{{ url('/') }}">Home</a>
    <span>›</span>
    <span>Salaries</span>
    <span>›</span>
    <span>{{ $roleLabel }} Salary in {{ $location }}</span>
</div>
                <h1>{{ $roleLabel }} {{ __('Salary in') }} {{ $location }}</h1>
                @if($salaryData)
                <p>{{ __('The average advertised salary for') }} {{ $roleLabel }} {{ __('jobs in') }} {{ $location }} {{ __('is') }} ${{ number_format($salaryData->avg, 2) }}/hr, {{ __('based on') }} {{ $salaryData->count }} {{ __('active job postings with salary information on Medojob. Current advertised salaries range from') }} ${{ number_format($salaryData->min, 2) }} {{ __('to') }} ${{ number_format($salaryData->max, 2) }}/hr. {{ __('Pay may vary by employer, city, experience, shift type, and healthcare setting.') }}</p>
                @else
                <p>{{ __('Salary information is not currently available for') }} {{ $roleLabel }} {{ __('jobs in') }} {{ $location }}. {{ __('Browse the latest job postings below to review available roles, employers, locations, and pay details.') }}</p>
                @endif
            </div>
            @if($salaryData)
            <div class="pseo-salary-summary">
                <span>{{ __('Avg Advertised Salary') }}</span>
                <strong>${{ number_format($salaryData->avg, 2) }}</strong>
                <span style="font-size:12px;color:#64748b;">/hr &bull; {{ $salaryData->count }} {{ __('postings') }}</span>
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e5e7eb;">
                    <span style="display:block;font-size:12px;">{{ __('Range') }}: ${{ number_format($salaryData->min, 2) }} – ${{ number_format($salaryData->max, 2) }}</span>
                </div>
            </div>
            @endif
        </div>

        @if($pageType === 'province' && !empty($cities))
        <div class="pseo-related-section">
            <h2>{{ __('Salary by City') }}</h2>
            <div class="table-responsive">
                <table class="table table-bordered" style="font-size:14px;">
                    <thead><tr><th>{{ __('City') }}</th><th>{{ __('Average') }}</th><th>{{ __('Range') }}</th><th>{{ __('Jobs') }}</th></tr></thead>
                    <tbody>
                    @foreach($cities as $row)
                        <tr>
                            <td><a href="{{ url('/' . $role . '-salary-' . $row['slug']) }}">{{ $row['city'] }}</a></td>
                            <td>@if($row['avg']) ${{ number_format($row['avg'], 2) }}/hr @else — @endif</td>
                            <td>@if($row['min'] && $row['max']) ${{ number_format($row['min'], 2) }} – ${{ number_format($row['max'], 2) }} @else — @endif</td>
                            <td>{{ $row['salaryCount'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if(!empty($employers))
        <div class="pseo-related-section">
            <h2>{{ __('Employers Hiring') }} {{ $roleLabel }} {{ __('in') }} {{ $location }}</h2>
            <div class="pseo-link-grid-4col">
                @foreach($employers as $emp)
               <a href="{{ url('/employers/' . ($emp['slug'] ?? '')) }}" class="pseo-link-card">
               <span class="pseo-link-card-label">{{ $emp['name'] ?? '' }}</span>
                  </a>
               @endforeach
            </div>
        </div>
        @endif

        <h2 class="pseo-section-title">{{ __('Latest') }} {{ $roleLabel }} {{ __('Jobs in') }} {{ $location }}</h2>
        <ul class="featuredlist row job-search-list-single">
            @forelse($latestJobs as $job)
                @php $jc = $job->getCompany(); @endphp
                @if($jc)
                    @include('includes.job_search_list_card', ['job' => $job, 'company' => $jc, 'columnClass' => 'col-12'])
                @endif
            @empty
                <li class="col-12"><div class="nodatabox"><h4>{{ __('No active job listings available.') }}</h4></div></li>
            @endforelse
        </ul>

        @if($pageType === 'city' && !empty($provinceSlug))
            <div style="margin:20px 0;">
                <a href="{{ url('/' . $role . '-salary-' . $provinceSlug) }}" class="btn btn-outline-primary btn-sm">← {{ $roleLabel }} {{ __('Salary in') }} {{ $provinceName }}</a>
                <a href="{{ url('/' . $role . '-jobs-' . $locationSlug) }}" class="btn btn-outline-primary btn-sm">{{ $roleLabel }} {{ __('Jobs in') }} {{ $location }}</a>
            </div>
        @endif

        @if($pageType === 'city' && !empty($relatedCitySalaries))
            <div class="pseo-related-section">
                <h2>{{ __('Related Salary Pages') }}</h2>
                <div class="pseo-link-grid-4col">
                    @foreach($relatedCitySalaries as $link)
                        <a href="{{ $link['url'] }}" class="pseo-link-card"><span class="pseo-link-card-label">{{ $link['label'] }}</span></a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pseo-related-section">
            <h2>{{ __('FAQ') }}</h2>
            <div class="pseo-faq-item">
                <h3>{{ __('What is the average') }} {{ $roleLabel }} {{ __('salary in') }} {{ $location }}?</h3>
                <p>@if($salaryData) {{ __('The average advertised salary for') }} {{ $roleLabel }} {{ __('jobs in') }} {{ $location }} {{ __('is') }} ${{ number_format($salaryData->avg, 2) }}/hr. @else {{ __('Salary information is currently limited.') }} @endif</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('What is the salary range for') }} {{ $roleLabel }} {{ __('jobs in') }} {{ $location }}?</h3>
                <p>@if($salaryData) {{ __('Current advertised salaries range from') }} ${{ number_format($salaryData->min, 2) }} {{ __('to') }} ${{ number_format($salaryData->max, 2) }}/hr. @else {{ __('Salary range data is not currently available.') }} @endif</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('Which employers are hiring') }} {{ $roleLabel }} {{ __('in') }} {{ $location }}?</h3>
                <p>{{ __('Browse the employers section above to see organizations currently hiring') }} {{ $roleLabel }} {{ __('professionals in') }} {{ $location }}.</p>
            </div>
            <div class="pseo-faq-item">
                <h3>{{ __('Do') }} {{ $roleLabel }} {{ __('salaries vary by employer?') }}</h3>
                <p>{{ __('Yes, advertised salaries may vary by employer, experience, shift type, facility setting, and job requirements.') }}</p>
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
    .pseo-jobs-header{display:grid;grid-template-columns:minmax(0,1fr) 260px;gap:24px;align-items:start;margin-bottom:24px}
    .pseo-jobs-header h1{margin:0 0 10px;font-size:34px;line-height:1.2}
    .pseo-eyebrow{margin:0 0 6px;color:#0f766e;font-weight:700;text-transform:uppercase;font-size:13px}
    .pseo-salary-summary{border:1px solid #e5e7eb;border-radius:8px;background:#fff;padding:18px}
    .pseo-salary-summary span{display:block;color:#64748b;font-size:13px}
    .pseo-salary-summary strong{display:block;font-size:32px;line-height:1.1;color:#111827;margin:6px 0 4px}
    .pseo-section-title{font-size:24px;margin:0 0 18px}
    .pseo-related-section{border:1px solid #e5e7eb;border-radius:8px;background:#fff;padding:18px;margin-top:24px}
    .pseo-related-section h2{font-size:20px;margin:0 0 14px}
    .pseo-link-grid-4col{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
    .pseo-link-card{display:block;border:1px solid #e5e7eb;border-radius:8px;padding:16px;text-decoration:none;background:#f9fafb;transition:background .15s,border-color .15s}
    .pseo-link-card:hover{background:#fff;border-color:#0d9488}
    .pseo-link-card-label{display:block;color:#111827;font-weight:600;font-size:14px}
    .pseo-faq-item{margin-bottom:16px}
    .pseo-faq-item h3{font-size:16px;margin:0 0 6px;color:#111827}
    .pseo-faq-item p{margin:0;color:#64748b;font-size:14px}
    .table-responsive table th,.table-responsive table td{padding:8px 12px}
    @media(max-width:991px){.pseo-link-grid-4col{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:767px){.pseo-jobs-header{grid-template-columns:1fr}.pseo-jobs-header h1{font-size:28px}.pseo-link-grid-4col{grid-template-columns:1fr}}
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
            'name' => 'Salaries'
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $roleLabel . ' Salary in ' . $location,
            'item' => url()->current()
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

@endpush
