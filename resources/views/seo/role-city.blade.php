@extends('layouts.app')

@section('page_title', $metaTitle)

@section('content')
@include('includes.header')
@include('flash::message')

<div class="listpgWraper pseo-jobs-page">
    <div class="container">
        <div class="pseo-jobs-header">
            <div>
                <p class="pseo-eyebrow">Healthcare Careers in {{ $cityName }}</p>
                <div class="pseo-breadcrumb">
    <a href="{{ url('/') }}">Home</a>
    <span>›</span>
    <a href="{{ url('/jobs') }}">Jobs</a>
    <span>›</span>
    <span>{{ $roleLabel }} Jobs in {{ $cityName }}</span>
</div>
                <h1>{{ $roleLabel }} Jobs in {{ $cityName }}</h1>
                <p>There {{ $jobCount == 1 ? 'is' : 'are' }} currently
             <strong>{{ number_format($jobCount) }}</strong>
               active {{ $roleLabel }} position{{ $jobCount == 1 ? '' : 's' }} available in {{ $cityName }}, Alberta.
               </p></br>
                <p>Medojob helps connect healthcare professionals with employment opportunities at hospitals, long-term care homes, supportive living facilities, home care agencies, rehabilitation centres, medical clinics, and community healthcare organizations throughout the region. Whether you are an experienced professional seeking a new opportunity or someone looking to advance your healthcare career, this page provides access to current openings from employers actively hiring in {{ $cityName }} and surrounding communities.
                   </p></br>

              <p>{{ $roleLabel }} positions in {{ $cityName }} may include full-time, part-time, casual, temporary, contract, day, evening, weekend, and overnight roles. Employers are often looking for qualified candidates who can provide quality care, support patients and residents, work collaboratively with healthcare teams, and contribute to positive healthcare outcomes. Depending on the employer and position, requirements may include professional registration, certifications, specialized training, previous experience, strong communication skills, and the ability to work effectively in a fast-paced healthcare environment.
              </p></br>

                <p>Browse current listings below, save the ones that interest you, and apply directly to employers hiring {{ $roleLabel }}s in {{ $cityName }}. New jobs are added regularly — or <a href="https://medojob.com/my-alerts">set up a job alert</a> to be notified when new positions are posted.</p>
            </div>
            <div class="pseo-salary-summary">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
            </div>
        </div>

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
        
        <div class="pseo-faq-section">
    <h2>Frequently Asked Questions</h2>

    <div class="faq-item">
        <h3>What does a {{ $roleLabel }} do?</h3>
        <p>
            A {{ $roleLabel }} provides important support in healthcare settings such as hospitals,
            long-term care homes, clinics, home care, and community health organizations.
            Duties may vary depending on the employer and the specific position.
        </p>
    </div>

    <div class="faq-item">
        <h3>What qualifications are required for {{ $roleLabel }} jobs in {{ $cityName }}?</h3>
        <p>
            Requirements vary by employer. Some roles may require certification, licensing,
            registration, healthcare training, previous experience, or specific workplace skills.
            Always review the job posting carefully before applying.
        </p>
    </div>

    <div class="faq-item">
        <h3>Are {{ $roleLabel }} jobs in demand in {{ $cityName }}?</h3>
        <p>
            Healthcare roles are commonly needed across Alberta. Demand in {{ $cityName }}
            can depend on local employers, care facilities, population needs, and available services.
        </p>
    </div>

    <div class="faq-item">
        <h3>How much do {{ $roleLabel }} jobs pay in {{ $cityName }}?</h3>
        <p>
            Pay can vary based on experience, employer, shift type, and job responsibilities.
            Check the current listings above to compare available pay information where provided.
        </p>
    </div>
</div>

        <div class="pseo-related-section">
            <h2>{{ __('Related Healthcare Jobs') }}</h2>
            <div class="row">
                @foreach($relatedLinks as $link)
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
    .pseo-faq-section {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    padding: 18px;
    margin-top: 24px;
}

.pseo-faq-section h2 {
    font-size: 22px;
    margin: 0 0 16px;
}

.faq-item {
    margin-bottom: 16px;
}

.faq-item h3 {
    font-size: 17px;
    margin: 0 0 6px;
}

.faq-item p {
    margin: 0;
    color: #4b5563;
    line-height: 1.6;
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
            'name' => 'Jobs'
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $roleLabel . ' Jobs in ' . $cityName,
            'item' => url()->current()
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush
