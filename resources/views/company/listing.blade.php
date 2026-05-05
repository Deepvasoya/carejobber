@extends('layouts.app')
@section('content') 
@include('includes.header')

<div class="pageSearch text-center">
    <h3>{{__('Get hired in most high rated companies')}}.</h3>
</div>

<style>
/* ── List layout ── */
.company-list-item {
    display: flex;
    align-items: center;
    gap: 18px;
    background: #fff;
    border: 1px solid #e8edf3;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 14px;
    transition: box-shadow .2s, border-color .2s;
}
.company-list-item:hover {
    box-shadow: 0 4px 18px rgba(0,0,0,.08);
    border-color: #c5d5ea;
}
.company-list-logo {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    border-radius: 8px;
    overflow: hidden;
    background: #f4f6f9;
    display: flex;
    align-items: center;
    justify-content: center;
}
.company-list-logo img { width: 100%; height: 100%; object-fit: contain; }
.company-list-body { flex: 1; min-width: 0; }
.company-list-body h4 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 4px;
    color: #1a2332;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.company-list-meta {
    font-size: 13px;
    color: #6b7a8d;
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 4px;
}
.company-list-meta span { display: flex; align-items: center; gap: 4px; }
.company-list-actions { flex-shrink: 0; text-align: right; }
.company-list-actions .btn-view {
    background: #0056b3;
    color: #fff;
    border: none;
    padding: 7px 18px;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 500;
    white-space: nowrap;
    text-decoration: none;
    display: inline-block;
}
.company-list-actions .btn-view:hover { background: #004494; color: #fff; }

/* ── Verified badges ── */
.badge-verified {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 600;
}
.badge-reviewed {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 600;
}
.badge-unverified {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 11px;
    font-weight: 600;
}

/* ── Sidebar filter ── */
.compnysidebarserch { background: #fff; border: 1px solid #e8edf3; border-radius: 8px; padding: 20px; }
.compnysidebarserch h4 { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: #1a2332; }
.filter-section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #8a96a3; letter-spacing: .5px; margin: 16px 0 8px; }
.verified-filter-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e8edf3;
    background: #f8f9fa;
    font-size: 13px;
    cursor: pointer;
    margin-bottom: 6px;
    text-decoration: none;
    color: #1a2332;
    transition: background .15s, border-color .15s;
}
.verified-filter-btn:hover, .verified-filter-btn.active { background: #e8f0fe; border-color: #0056b3; color: #0056b3; }
.verified-filter-btn.active.filter-reviewed { background: #fff8e1; border-color: #ffc107; color: #856404; }
.verified-filter-btn.active.filter-unverified { background: #fff1f2; border-color: #dc3545; color: #9f1239; }
.verified-filter-btn.active.filter-all { background: #f1f5f9; border-color: #64748b; color: #334155; }
.verified-filter-btn .count-badge {
    background: #e8edf3;
    border-radius: 10px;
    padding: 1px 8px;
    font-size: 11px;
    font-weight: 600;
}
.verified-filter-btn.active .count-badge { background: #0056b3; color: #fff; }
.verified-filter-btn.active.filter-reviewed .count-badge { background: #ffc107; color: #1f2937; }
.verified-filter-btn.active.filter-unverified .count-badge { background: #dc3545; color: #fff; }
.verified-filter-btn.active.filter-all .count-badge { background: #64748b; color: #fff; }
.verified-filter-btn .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.dot-green { background: #28a745; }
.dot-yellow { background: #ffc107; }
.dot-red   { background: #dc3545; }
.dot-all   { background: #6c757d; }
</style>

<div class="listpgWraper">
<div class="container">
    <div class="row">

        {{-- ── Sidebar ── --}}
        <div class="col-lg-3">
            <form id="top-search" method="GET" action="{{route('company.listing')}}">
                {{-- preserve verified_filter when other filters change --}}
                @if(request('verified_filter'))
                    <input type="hidden" name="verified_filter" value="{{ request('verified_filter') }}">
                @endif

                <div class="compnysidebarserch">
                    <h4>{{__('Search Filter')}}</h4>

                    <div class="mb-3">
                        <input type="text" name="search" value="{{Request::get('search', '')}}" class="form-control" placeholder="{{__('keywords e.g. "Venta Care"')}}">
                    </div>
                    <div class="mb-3">
                        <label>{{__('Country')}}</label>
                        {!! Form::select('country_id[]', ['' => __('Select Country')]+$countries, Request::get('country_id', $siteSetting->default_country_id), ['class'=>'form-control', 'id'=>'country_id']) !!}
                    </div>
                    <div class="mb-3">
                        <label>{{__('State')}}</label>
                        <span id="state_dd">
                            {!! Form::select('state_id[]', ['' => __('Select State')], Request::get('state_id', null), ['class'=>'form-control', 'id'=>'state_id']) !!}
                        </span>
                    </div>
                    <div class="mb-3">
                        <label>{{__('City')}}</label>
                        <span id="city_dd">
                            {!! Form::select('city_id[]', ['' => __('Select City')], Request::get('city_id', null), ['class'=>'form-control', 'id'=>'city_id']) !!}
                        </span>
                    </div>

                    <div class="comfilter">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search"></i> {{__('Search Company')}}
                        </button>
                    </div>
                </div>
            </form>

            {{-- ── Verified filter (outside main form, uses direct links) ── --}}
            <div class="compnysidebarserch mt-3">
                <div class="filter-section-title">{{__('Employer Status')}}</div>

                @php
                    $baseQuery = request()->except('verified_filter');
                    $allUrl      = route('company.listing') . '?' . http_build_query(array_merge($baseQuery, []));
                    $verifiedUrl = route('company.listing') . '?' . http_build_query(array_merge($baseQuery, ['verified_filter' => 'verified']));
                    $reviewedUrl = route('company.listing') . '?' . http_build_query(array_merge($baseQuery, ['verified_filter' => 'reviewed']));
                    $unverifiedUrl = route('company.listing') . '?' . http_build_query(array_merge($baseQuery, ['verified_filter' => 'unverified']));
                    $statusTitles = [
                        'all' => __('Show all employers regardless of verification status.'),
                        'verified' => __("Fully Verified Employer\nThis employer has been reviewed and confirmed as a trusted healthcare organization."),
                        'reviewed' => __("Basic Verified Employer\nThis employer has been reviewed but has limited verification."),
                        'unverified' => __("Unverified Employer\nThis employer has not yet been reviewed. Please apply with caution."),
                    ];
                @endphp

                <a href="{{ $allUrl }}"
                   class="verified-filter-btn filter-all {{ empty($verified_filter) ? 'active' : '' }}"
                   title="{{ $statusTitles['all'] }}">
                    <span><span class="dot dot-all"></span> {{__('All Employers')}}</span>
                    <span class="count-badge">{{ $allEmployerStatusCount }}</span>
                </a>

                <a href="{{ $verifiedUrl }}"
                   class="verified-filter-btn filter-verified {{ $verified_filter === 'verified' ? 'active' : '' }}"
                   title="{{ $statusTitles['verified'] }}">
                    <span><span class="dot dot-green"></span> {{__('Verified')}}</span>
                    <span class="count-badge">{{ $verifiedCount }}</span>
                </a>

                <a href="{{ $reviewedUrl }}"
                   class="verified-filter-btn filter-reviewed {{ $verified_filter === 'reviewed' ? 'active' : '' }}"
                   title="{{ $statusTitles['reviewed'] }}">
                    <span><span class="dot dot-yellow"></span> {{__('Reviewed')}}</span>
                    <span class="count-badge">{{ $reviewedCount }}</span>
                </a>

                <a href="{{ $unverifiedUrl }}"
                   class="verified-filter-btn filter-unverified {{ $verified_filter === 'unverified' ? 'active' : '' }}"
                   title="{{ $statusTitles['unverified'] }}">
                    <span><span class="dot dot-red"></span> {{__('Unverified')}}</span>
                    <span class="count-badge">{{ $unverifiedCount }}</span>
                </a>
            </div>
        </div>

        {{-- ── Results ── --}}
        <div class="col-lg-9">

            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h3 class="mb-0">{{ $companies->total() }} {{__('Employers Found')}}</h3>
                    <div class="text-muted" style="font-size:13px;">
                        {{__('Showing')}} {{ $companies->firstItem() }}–{{ $companies->lastItem() }} {{__('of')}} {{ $companies->total() }}
                    </div>
                </div>
                @if(request('verified_filter'))
                    <a href="{{ route('company.listing') }}?{{ http_build_query(request()->except('verified_filter')) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-times"></i> {{__('Clear filter')}}
                    </a>
                @endif
            </div>

            @if($companies->isEmpty())
                <div class="alert alert-info">{{__('No companies found matching your criteria.')}}</div>
            @else
                @foreach($companies as $company)
                <div class="company-list-item">

                    {{-- Logo --}}
                    <div class="company-list-logo">
                        <a href="{{route('company.detail', $company->slug)}}">
                            {!! $company->printCompanyImage() !!}
                        </a>
                    </div>

                    {{-- Info --}}
                    <div class="company-list-body">
                        <h4>
                            <a href="{{route('company.detail', $company->slug)}}" style="color:inherit;text-decoration:none;">
                                {{$company->name}}
                            </a>
                            @php
                                $trustStatus = $company->getEmployerTrustStatus();
                            @endphp
                            @if($trustStatus === 'verified')
                                <span class="badge-verified" title="{{ $statusTitles['verified'] }}" style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                                    🟢 {{__('Verified')}}
                                </span>
                            @elseif($trustStatus === 'reviewed')
                                <span class="badge-reviewed" title="{{ $statusTitles['reviewed'] }}" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
                                    🟡 {{__('Reviewed')}}
                                </span>
                            @else
                                <span class="badge-unverified" title="{{ $statusTitles['unverified'] }}" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
                                    🔴 {{__('Unverified')}}
                                </span>
                            @endif
                        </h4>
                        <div class="company-list-meta">
                            @if($company->getCity('city'))
                                <span><i class="fas fa-map-marker-alt"></i> {{ $company->getCity('city') }}</span>
                            @endif
                            @if($company->getIndustry('industry'))
                                <span><i class="fas fa-industry"></i> {{ $company->getIndustry('industry') }}</span>
                            @endif
                            <span><i class="fas fa-briefcase"></i> {{ $company->countNumJobs('company_id', $company->id) }} {{__('Open Jobs')}}</span>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div class="company-list-actions">
                        <a href="{{route('company.detail', $company->slug)}}" class="btn-view">
                            {{__('View Profile')}}
                        </a>
                    </div>

                </div>
                @endforeach
            @endif

            {{-- Pagination --}}
            <div class="pagiWrap mt-3">
                <div class="row">
                    <div class="col-md-5">
                        <div class="showreslt text-muted" style="font-size:13px;">
                            {{__('Showing')}} {{ $companies->firstItem() }}–{{ $companies->lastItem() }} {{__('of')}} {{ $companies->total() }}
                        </div>
                    </div>
                    <div class="col-md-7 text-right">
                        @if($companies->hasPages())
                            {{ $companies->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>

        </div>{{-- /col-lg-9 --}}
    </div>
</div>
</div>

@include('includes.footer')
@endsection

@push('scripts')
@include('includes.country_state_city_js')
@endpush
