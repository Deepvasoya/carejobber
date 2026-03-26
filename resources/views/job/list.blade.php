@extends('layouts.app')

@section('content') 

<!-- Header start --> 

@include('includes.header') 

<!-- Header end --> 



@include('flash::message')

@include('includes.inner_top_search')



<!-- Inner Page Title end -->

<div class="listpgWraper">

    <div class="container">

        

        <form action="{{route('job.list')}}" method="get" id="search-job-list">

            <!-- Search Result and sidebar start -->

            <div class="row"> 

                @include('includes.job_list_side_bar')
                

                <div class="col-lg-9">

                    <div class="job-search-results-narrow">

                    <!-- Search List (single narrow column, compact rows) -->
                     <h3>{{ $jobs->total() }} Jobs Found</h3>    
                    <div class="topstatinfo mb-0">
                    {{__('Showing Jobs')}} : {{ $jobs->firstItem() }} - {{ $jobs->lastItem() }} {{__('Total')}} {{ $jobs->total() }}
                    </div>

                    <ul class="featuredlist row job-search-list-single">

                        <!-- job start --> 

                        @if(isset($jobs) && count($jobs)) <?php $count_1 = 1; ?> 
                        @foreach($jobs as $job) 
                        @php $company = $job->getCompany();
                        @endphp

                             <?php if(isset($company))
                            {
                            ?>

                            <?php if($count_1 == 7) {?>

                                <li class="col-12"><div class="jobint text-center">{!! $siteSetting->listing_page_horizontal_ad !!}</div></li>

                            <?php }else{ ?>

                          
                                


              @php
                  $hasApplied = false;
                  if(Auth::check()) {
                      $hasApplied = \App\JobApply::where('job_id', $job->id)
                          ->where('user_id', Auth::user()->id)
                          ->exists();
                  }
              @endphp
              @php
                  $jobShiftLabel = $job->getJobShift('job_shift');
                  $benefitsPlain = trim(strip_tags((string) $job->benefits));
                  $expired = $job->isJobExpired();
              @endphp
              <li class="col-12 @if($job->is_featured == 1) featured @endif">
                <div class="jobint job-list-card-enhanced job-list-card-compact @if(!empty($job->is_highlighted) && !$hasApplied) job-card-highlighted @endif @if($hasApplied) job-list-card-applied @endif">
                    <div class="job-list-card-top">
                        <div class="job-list-card-badges">
                            @if(!empty($job->is_urgent))
                                <span class="job-list-pill job-list-pill-urgent"><i class="fas fa-fire"></i> {{ __('Urgent hiring') }}</span>
                            @endif
                            @if(!empty($job->is_featured))
                                <span class="job-list-pill job-list-pill-featured"><i class="fas fa-bolt"></i> {{ __('Featured job') }}</span>
                            @endif
                            @if(!empty($job->is_highlighted))
                                <span class="job-list-pill job-list-pill-highlight"><i class="fas fa-star"></i> {{ __('Highlighted') }}</span>
                            @endif
                            @if($hasApplied)
                                <span class="job-list-pill job-list-pill-applied"><i class="fa fa-check-circle"></i> {{ __('Applied') }}</span>
                            @endif
                        </div>
                        <div class="job-list-card-save">
                            @guest
                                <a href="{{ route('login') }}" class="job-list-save-btn" title="{{ __('Sign in to save jobs') }}" aria-label="{{ __('Save job') }}"><i class="far fa-heart"></i></a>
                            @else
                                @if(Auth::user()->isFavouriteJob($job->slug))
                                    <a href="{{ route('remove.from.favourite', $job->slug) }}" class="job-list-save-btn job-list-save-btn-active" title="{{ __('Remove from saved') }}" aria-label="{{ __('Remove from saved') }}"><i class="fas fa-heart"></i></a>
                                @else
                                    <a href="{{ route('add.to.favourite', $job->slug) }}" class="job-list-save-btn" title="{{ __('Save job') }}" aria-label="{{ __('Save job') }}"><i class="far fa-heart"></i></a>
                                @endif
                            @endguest
                        </div>
                    </div>

                    <h4 class="job-list-card-title"><a href="{{ route('job.detail', [$job->slug]) }}" title="{{ $job->title }}">{{ \Illuminate\Support\Str::limit($job->title, 72) }}</a></h4>

                    <dl class="job-list-card-meta">
                        <div class="job-list-meta-row">
                            <dt><i class="fas fa-money-bill-wave"></i> {{ __('Salary') }}</dt>
                            <dd>
                                @if(!(bool) $job->hide_salary)
                                    <strong>{{ $job->salary_currency }}{{ $job->salary_from }} – {{ $job->salary_currency }}{{ $job->salary_to }}</strong>
                                    <span class="job-list-meta-muted">/ {{ $job->getSalaryPeriod('salary_period') }}</span>
                                @else
                                    <span class="job-list-meta-muted">{{ __('Not disclosed') }}</span>
                                @endif
                            </dd>
                        </div>
                        <div class="job-list-meta-row">
                            <dt><i class="fas fa-briefcase"></i> {{ __('Job type') }}</dt>
                            <dd>{{ $job->getJobType('job_type') ?: '—' }}</dd>
                        </div>
                        <div class="job-list-meta-row">
                            <dt><i class="fas fa-clock"></i> {{ __('Job shift') }}</dt>
                            <dd>{{ $jobShiftLabel ?: '—' }}</dd>
                        </div>
                        <div class="job-list-meta-row job-list-meta-row-full">
                            <dt><i class="fas fa-gift"></i> {{ __('Benefits') }}</dt>
                            <dd>
                                @if($benefitsPlain !== '')
                                    {{ \Illuminate\Support\Str::limit($benefitsPlain, 100) }}
                                @else
                                    <span class="job-list-meta-muted">—</span>
                                @endif
                            </dd>
                        </div>
                        <div class="job-list-meta-row">
                            <dt><i class="fas fa-calendar-times"></i> {{ __('Application deadline') }}</dt>
                            <dd>
                                @if($job->expiry_date)
                                    <strong>{{ \Carbon\Carbon::parse($job->expiry_date)->format('M d, Y') }}</strong>
                                    @if($expired)
                                        <span class="job-list-pill job-list-pill-expired ms-1">{{ __('Expired') }}</span>
                                    @endif
                                @else
                                    <span class="job-list-meta-muted">—</span>
                                @endif
                            </dd>
                        </div>
                        <div class="job-list-meta-row">
                            <dt><i class="fas fa-map-marker-alt"></i> {{ __('Location') }}</dt>
                            <dd>{{ $job->getCity('city') ?: '—' }}</dd>
                        </div>
                    </dl>

                    <div class="job-list-card-company">
                        <a href="{{ route('company.detail', $company->slug) }}" class="job-list-company-logo" title="{{ $company->name }}">{{ $company->printCompanyImage() }}</a>
                        <div class="job-list-company-text">
                            <a href="{{ route('company.detail', $company->slug) }}" class="job-list-company-name" title="{{ $company->name }}">{{ $company->name }}</a>
                            <div class="job-list-posted">{{ __('Posted') }}: {{ $job->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>

                    <div class="job-list-card-actions">
                        @if($expired)
                            <span class="job-list-apply job-list-apply-disabled"><i class="fas fa-ban"></i> {{ __('Job closed') }}</span>
                        @elseif(Auth::check() && $hasApplied)
                            <span class="job-list-apply job-list-apply-done"><i class="fas fa-check-circle"></i> {{ __('Already applied') }}</span>
                        @elseif(Auth::check())
                            <a href="javascript:void(0);"
                               class="job-list-apply job-list-apply-primary js-job-list-open-apply"
                               role="button"
                               data-job-slug="{{ $job->slug }}">
                                <i class="fas fa-paper-plane"></i> {{ __('Apply now') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="job-list-apply job-list-apply-primary"><i class="fas fa-paper-plane"></i> {{ __('Apply now') }}</a>
                        @endif
                        <a href="{{ route('job.detail', [$job->slug]) }}" class="job-list-apply job-list-apply-secondary">{{ __('View details') }}</a>
                    </div>
                </div>
            </li>







						 <?php } ?>

                            <?php $count_1++; ?>

						

						 <?php } ?>

                        <!-- job end --> 

                        @endforeach
                        @endif

						

						

						

                           

                       

                            <!-- job end -->

                            

						

						

						

                    </ul>

                    <!-- Pagination Start -->

                    <div class="pagiWrap mt-4 job-search-pagi">

                        <div class="showreslt small text-muted mb-2">

                            {{__('Showing Jobs')}} : {{ $jobs->firstItem() }} - {{ $jobs->lastItem() }} {{__('Total')}} {{ $jobs->total() }}

                        </div>

                        @if(isset($jobs) && count($jobs))

                        <div class="job-search-pagi-links">

                            {{ $jobs->appends(request()->query())->links() }}

                        </div>

                        @endif

                    </div>

                    <!-- Pagination end -->

                    </div>

                   



                </div>

            </div>

        </form>

    </div>

</div>

@if(Auth::check())
<div class="modal fade" id="applyJobListModal" tabindex="-1" aria-labelledby="applyJobListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-body text-center py-5 text-muted">
                <div class="spinner-border text-secondary" role="status"><span class="visually-hidden">{{ __('Loading') }}…</span></div>
            </div>
        </div>
    </div>
</div>

<div id="applySubmitPageOverlay" class="apply-submit-page-overlay" aria-hidden="true" role="status">
    <div class="apply-submit-page-overlay__card">
        <div class="apply-submit-page-overlay__rings" aria-hidden="true">
            <span class="apply-submit-page-overlay__ring"></span>
            <span class="apply-submit-page-overlay__ring apply-submit-page-overlay__ring--delay"></span>
        </div>
        <p class="apply-submit-page-overlay__title">{{ __('Submitting your application') }}</p>
        <p class="apply-submit-page-overlay__hint">{{ __('Please wait a moment…') }}</p>
    </div>
</div>
@endif


@if (Request::get('search') != '' || Request::get('functional_area_id') != '' || Request::get('country_id') != ''|| Request::get('state_id') != '' || Request::get('city_id') != ''|| Request::get('city_id') != '')

<div class="modal fade" id="show_alert" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="submit_alert">
                @csrf
                <input type="hidden" name="search" value="{{ Request::get('search') }}">
                <input type="hidden" name="country_id" value="@if(isset(Request::get('country_id')[0])) {{ Request::get('country_id')[0] }} @endif">
                <input type="hidden" name="state_id" value="@if(isset(Request::get('state_id')[0])){{ Request::get('state_id')[0] }} @endif">
                <input type="hidden" name="city_id" value="@if(isset(Request::get('city_id')[0])){{ Request::get('city_id')[0] }} @endif">
                <input type="hidden" name="functional_area_id" value="@if(isset(Request::get('functional_area_id')[0])){{ Request::get('functional_area_id')[0] }} @endif">
                <div class="modal-header">
                    <h4 class="modal-title">Job Alert</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
					<h3>Get the latest <strong>"{{ ucfirst(Request::get('search')) }}"</strong> jobs  @if(Request::get('location')!='') in <strong>{{ ucfirst(Request::get('location')) }}</strong>@endif sent straight to your inbox</h3>
                    <div class="form-group">
                        <input type="text" class="form-control" name="email" id="email" placeholder="Enter your Email" value="@if(Auth::check()){{Auth::user()->email}}@endif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif


@include('includes.footer')

@endsection

@push('styles')

<style type="text/css">

    .searchList li .jobimg {

        min-height: 80px;

    }

    .hide_vm_ul{

        height:100px;

        overflow:hidden;

    }

    .hide_vm{

        display:none !important;

    }

    .view_more{

        cursor:pointer;

    }

    .view_less{

        cursor:pointer;

    }

    .job-card-highlighted {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
        border: 1px solid #f59e0b !important;
        box-shadow: 0 2px 12px rgba(245, 158, 11, 0.12);
    }

    .promotepof-badge.job-urgent-badge {
        background: #dc2626 !important;
        left: 10px;
        right: auto;
    }

    /* Narrow list lives inside normal Bootstrap col (avoid width:auto on col — breaks flex + width:100% child) */
    .job-search-results-narrow {
    
        width: 100%;
    }

    .job-search-results-narrow > h3 {
        font-size: 1.1rem;
        margin-bottom: 0.35rem;
    }

    .job-search-results-narrow .topstatinfo {
        font-size: 0.8rem;
        margin-bottom: 0.5rem !important;
        color: #64748b;
    }

    .job-search-pagi .pagination {
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 0.2rem;
        margin-bottom: 0;
        font-size: 0.8rem;
    }

    .job-search-pagi .page-link {
        padding: 0.25rem 0.45rem;
    }

    ul.featuredlist.row.job-search-list-single {
        --bs-gutter-x: 0.65rem;
        --bs-gutter-y: 0.5rem;
        margin-left: 0;
        margin-right: 0;
    }

    ul.featuredlist.row.job-search-list-single > li {
        padding-left: 0;
        padding-right: 0;
    }

    .job-list-card-enhanced {
        position: relative;
        height: auto;
        display: flex;
        flex-direction: column;
        padding: 1.1rem 1.15rem 1rem;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .job-list-card-compact {
        padding: 0.55rem 0.65rem 0.5rem;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(15, 23, 42, 0.05);
    }

    .job-list-card-compact .job-list-card-top {
        margin-bottom: 0.35rem;
        min-height: 0;
    }

    .job-list-card-compact .job-list-pill {
        font-size: 0.62rem;
        padding: 0.15rem 0.4rem;
    }

    .job-list-card-compact .job-list-save-btn {
        width: 1.85rem;
        height: 1.85rem;
    }

    .job-list-card-compact .job-list-card-title {
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 0.4rem;
    }

    .job-list-card-compact .job-list-card-title a {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .job-list-card-compact .job-list-card-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 0.35rem 0.75rem;
        margin: 0 0 0.45rem;
        padding: 0;
        font-size: 0.78rem;
        grid-template-columns: unset;
    }

    .job-list-card-compact .job-list-meta-row {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 0.2rem 0.35rem;
        margin: 0;
    }

    .job-list-card-compact .job-list-meta-row-full {
        flex-basis: 100%;
    }

    .job-list-card-compact .job-list-card-meta dt {
        font-size: 0.65rem;
        margin-bottom: 0;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
        color: #94a3b8;
    }

    .job-list-card-compact .job-list-card-meta dt i {
        margin-right: 0.15rem;
        font-size: 0.7rem;
    }

    .job-list-card-compact .job-list-card-meta dd {
        line-height: 1.35;
        margin: 0;
    }

    .job-list-card-compact .job-list-card-company {
        padding-top: 0.45rem;
        margin-top: 0;
        margin-bottom: 0.45rem;
        border-top: 1px solid #f1f5f9;
        gap: 0.5rem;
    }

    .job-list-card-compact .job-list-company-logo img {
        max-height: 32px;
        max-width: 32px;
        border-radius: 6px;
    }

    .job-list-card-compact .job-list-company-name {
        font-size: 0.82rem;
    }

    .job-list-card-compact .job-list-posted {
        font-size: 0.72rem;
        margin-top: 0;
    }

    .job-list-card-compact .job-list-card-actions {
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .job-list-card-compact .job-list-apply {
        padding: 0.35rem 0.65rem;
        font-size: 0.78rem;
        border-radius: 6px;
    }

    .job-list-card-compact .job-list-meta-row-full dd {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .job-list-card-enhanced:hover {
        border-color: #c7d2fe;
        box-shadow: 0 8px 24px rgba(37, 87, 167, 0.12);
    }

    .job-list-card-applied {
        border-color: #86efac !important;
        background: linear-gradient(180deg, #f0fdf4 0%, #fff 55%) !important;
    }

    .job-list-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.65rem;
        min-height: 1.75rem;
    }

    .job-list-card-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        flex: 1;
        min-width: 0;
    }

    .job-list-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        line-height: 1.2;
    }

    .job-list-pill-urgent {
        background: #fee2e2;
        color: #b91c1c;
    }

    .job-list-pill-featured {
        background: #fef3c7;
        color: #92400e;
    }

    .job-list-pill-highlight {
        background: #e0f2fe;
        color: #0369a1;
    }

    .job-list-pill-applied {
        background: #d1fae5;
        color: #047857;
    }

    .job-list-pill-expired {
        font-size: 0.65rem;
        background: #f3f4f6;
        color: #6b7280;
        text-transform: none;
        font-weight: 600;
    }

    .job-list-save-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        color: #64748b;
        background: #f8fafc;
        text-decoration: none;
        flex-shrink: 0;
        transition: color 0.15s, background 0.15s, border-color 0.15s;
    }

    .job-list-save-btn:hover {
        color: #dc2626;
        border-color: #fecaca;
        background: #fef2f2;
    }

    .job-list-save-btn-active {
        color: #dc2626;
        border-color: #fecaca;
        background: #fff1f2;
    }

    .job-list-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.35;
        margin: 0 0 0.85rem;
    }

    .job-list-card-title a {
        color: #0f172a;
        text-decoration: none;
    }

    .job-list-card-title a:hover {
        color: #2557a7;
    }

    .job-list-card-meta {
        margin: 0 0 1rem;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.55rem 1rem;
        font-size: 0.875rem;
    }

    @media (max-width: 575.98px) {
        .job-list-card-meta {
            grid-template-columns: 1fr;
        }
    }

    .job-list-meta-row {
        margin: 0;
        min-width: 0;
    }

    .job-list-meta-row-full {
        grid-column: 1 / -1;
    }

    .job-list-card-meta dt {
        font-weight: 600;
        color: #64748b;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 0.15rem;
    }

    .job-list-card-meta dt i {
        margin-right: 0.25rem;
        opacity: 0.85;
    }

    .job-list-card-meta dd {
        margin: 0;
        color: #1e293b;
        line-height: 1.45;
        word-break: break-word;
    }

    .job-list-meta-muted {
        color: #94a3b8;
        font-weight: 500;
    }

    .job-list-card-company {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-top: 0.75rem;
        margin-top: auto;
        border-top: 1px solid #f1f5f9;
        margin-bottom: 0.85rem;
    }

    .job-list-company-logo img {
        max-height: 44px;
        max-width: 44px;
        border-radius: 8px;
        object-fit: contain;
    }

    .job-list-company-name {
        font-weight: 600;
        color: #2557a7;
        text-decoration: none;
        display: block;
        font-size: 0.9rem;
    }

    .job-list-company-name:hover {
        text-decoration: underline;
    }

    .job-list-posted {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.15rem;
    }

    .job-list-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .job-list-apply {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        border: 1px solid transparent;
        line-height: 1.2;
    }

    .job-list-apply-primary {
        background: #2557a7;
        color: #fff !important;
        border-color: #2557a7;
    }

    .job-list-apply-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff !important;
    }

    .job-list-apply-secondary {
        background: #fff;
        color: #334155 !important;
        border-color: #e2e8f0;
    }

    .job-list-apply-secondary:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #0f172a !important;
    }

    .job-list-apply-done,
    .job-list-apply-disabled {
        cursor: default;
        background: #f1f5f9;
        color: #64748b !important;
        border-color: #e2e8f0;
    }

    /* Full-screen overlay while apply form submits (above Bootstrap modal, z-modal ~1055) */
    .apply-submit-page-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.48);
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
    }

    .apply-submit-page-overlay.is-visible {
        display: flex;
    }

    .apply-submit-page-overlay__card {
        background: #fff;
        border-radius: 20px;
        padding: 2.25rem 2.5rem 2rem;
        box-shadow:
            0 25px 50px -12px rgba(0, 0, 0, 0.28),
            0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        text-align: center;
        max-width: 340px;
        width: 100%;
        animation: apply-submit-card-in 0.35s ease-out;
    }

    @keyframes apply-submit-card-in {
        from {
            opacity: 0;
            transform: scale(0.94) translateY(8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .apply-submit-page-overlay__rings {
        position: relative;
        width: 72px;
        height: 72px;
        margin: 0 auto 1.35rem;
    }

    .apply-submit-page-overlay__ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid transparent;
        border-top-color: #17d27c;
        border-right-color: rgba(23, 210, 124, 0.35);
        animation: apply-submit-orbit 1s linear infinite;
    }

    .apply-submit-page-overlay__ring--delay {
        inset: 8px;
        border-top-color: #2557a7;
        border-right-color: rgba(37, 87, 167, 0.3);
        animation-duration: 1.35s;
        animation-direction: reverse;
    }

    @keyframes apply-submit-orbit {
        to {
            transform: rotate(360deg);
        }
    }

    .apply-submit-page-overlay__title {
        margin: 0 0 0.35rem;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .apply-submit-page-overlay__hint {
        margin: 0;
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.45;
    }

</style>

@endpush

@push('scripts') 

<script>
$('.btn-job-alert').on('click', function() {
	@if(Auth::user())
	$('#show_alert').modal('show');
	@else
	swal({
		title: "Save Job Alerts",
		text: "To save Job Alerts you must be Registered and Logged in",
		icon: "error",
		buttons: {
			Login: "Login",
			register: "Register",
			hello: "OK",
		},
	});
	@endif

})

$(document).ready(function($) {
	$("#search-job-list").submit(function() {
		$(this).find(":input").filter(function() {
			return !this.value;
		}).attr("disabled", "disabled");
		return true;
	});


	$("#search-job-list").find(":input").prop("disabled", false);

	$(".view_more_ul").each(function () {
    if ($(this).height() > 100) {
        $(this).addClass("hide_vm_ul");
        $(this).next(".view_more").removeClass("hide_vm");
    }
});

// "View More" click event
$(".view_more").on("click", function (e) {
    e.preventDefault();
    $(this).prev(".view_more_ul").removeClass("hide_vm_ul");
    $(this).addClass("hide_vm");
    $(this).next(".view_less").removeClass("hide_vm");
});

// "View Less" click event
$(".view_less").on("click", function (e) {
    e.preventDefault();
    $(this).prev(".view_more").removeClass("hide_vm");
    $(this).prevAll(".view_more_ul").addClass("hide_vm_ul");
    $(this).addClass("hide_vm");
});

});

if ($("#submit_alert").length > 0) {

	$("#submit_alert").validate({
		rules: {
			email: {
				required: true,
				maxlength: 5000,
				email: true
			}
		},

		messages: {
			email: {
				required: "Email is required",
			}
		},

		submitHandler: function(form) {
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				url: "{{route('subscribe.alert')}}",
				type: "GET",
				data: $('#submit_alert').serialize(),
				success: function(response) {
					$("#submit_alert").trigger("reset");
					$('#show_alert').modal('hide');
					swal({
						title: "Success",
						text: response["msg"],
						icon: "success",
						button: "OK",
					});
				}
			});
		}
	})
}

$(document).on('click', '.swal-button--Login', function() {
	window.location.href = "{{route('login')}}";
})
$(document).on('click', '.swal-button--register', function() {
	window.location.href = "{{route('register')}}";
})

@if(Auth::check())
function applySubmitOverlayShow() {
	var $o = $('#applySubmitPageOverlay');
	if (!$o.length) return;
	$o.addClass('is-visible').attr('aria-hidden', 'false');
	$('body').css('overflow', 'hidden');
}

function applySubmitOverlayHide() {
	var $o = $('#applySubmitPageOverlay');
	if (!$o.length) return;
	$o.removeClass('is-visible').attr('aria-hidden', 'true');
	$('body').css('overflow', '');
}

function applyJobListShowModal() {
	var el = document.getElementById('applyJobListModal');
	if (!el) return;
	if (window.bootstrap && window.bootstrap.Modal) {
		window.bootstrap.Modal.getOrCreateInstance(el).show();
	} else if (window.jQuery && jQuery.fn.modal) {
		jQuery(el).modal('show');
	}
}

function applyJobListHideModal() {
	var el = document.getElementById('applyJobListModal');
	if (!el) return;
	if (window.bootstrap && window.bootstrap.Modal) {
		var inst = window.bootstrap.Modal.getInstance(el);
		if (inst) inst.hide();
	} else if (window.jQuery && jQuery.fn.modal) {
		jQuery(el).modal('hide');
	}
}

function applyListSelectCv(cvId, element) {
	var modal = document.getElementById('applyJobListModal');
	if (!modal) return;
	modal.querySelectorAll('.apply-list-cv-card').forEach(function (card) {
		card.style.borderColor = '#e0e0e0';
		card.style.background = '#fff';
	});
	element.style.borderColor = '#667eea';
	element.style.background = '#f8f9ff';
	var radio = document.getElementById('apply_list_cv_' + cvId);
	if (radio) radio.checked = true;
	var uploadOpt = document.getElementById('apply_list_upload_new_cv');
	if (uploadOpt) uploadOpt.checked = false;
	var uploadField = document.getElementById('apply_list_cv_upload_field');
	if (uploadField) uploadField.style.display = 'none';
}

function applyListToggleCvUpload() {
	var uploadField = document.getElementById('apply_list_cv_upload_field');
	var uploadOpt = document.getElementById('apply_list_upload_new_cv');
	if (!uploadField || !uploadOpt) return;
	if (uploadOpt.checked) {
		uploadField.style.display = 'block';
		document.querySelectorAll('#applyJobListModal input.apply-list-cv-radio').forEach(function (r) { r.checked = false; });
		document.querySelectorAll('#applyJobListModal .apply-list-cv-card').forEach(function (card) {
			card.style.borderColor = '#e0e0e0';
			card.style.background = '#fff';
		});
	} else {
		uploadField.style.display = 'none';
	}
}

function applyListShowFileName(input) {
	var display = document.getElementById('apply_list_file_name_display');
	if (!display || !input.files || !input.files[0]) return;
	display.innerHTML = '<i class="fas fa-file-pdf text-danger me-2"></i>' + input.files[0].name;
}

function applyListBindCoverLetterCounter() {
	var ta = document.getElementById('apply_list_cover_letter');
	var cc = document.getElementById('apply_list_char_count');
	if (!ta || !cc) return;
	cc.textContent = String(ta.value.length);
	ta.oninput = function () { cc.textContent = String(ta.value.length); };
}

$(document).on('click', '.js-job-list-open-apply', function (e) {
	e.preventDefault();
	var slug = $(this).data('job-slug');
	if (!slug) return;
	var $dialog = $('#applyJobListModal .modal-dialog');
	$dialog.html('<div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);"><div class="modal-body text-center py-5 text-muted"><div class="spinner-border text-secondary" role="status"></div></div></div>');
	applyJobListShowModal();
	$.ajax({
		url: "{{ url('job-apply-modal') }}/" + encodeURIComponent(slug),
		type: "GET",
		headers: {
			"X-Requested-With": "XMLHttpRequest",
			"Accept": "application/json",
			"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
		},
		success: function (data) {
			if (data.success && data.html) {
				$dialog.html(data.html);
				applyListBindCoverLetterCounter();
			} else {
				swal({ title: "{{ __('Error') }}", text: data.message || "{{ __('Could not load apply form.') }}", icon: "error" });
				applyJobListHideModal();
			}
		},
		error: function (xhr) {
			var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('Request failed.') }}";
			swal({ title: "{{ __('Error') }}", text: msg, icon: "error" });
			applyJobListHideModal();
		}
	});
});

$(document).on('submit', '#applyJobListForm', function (e) {
	e.preventDefault();
	var form = this;
	var $btn = $(form).find('.apply-job-list-submit');
	$btn.prop('disabled', true);
	applySubmitOverlayShow();
	$.ajax({
		url: $(form).attr('action'),
		type: "POST",
		data: new FormData(form),
		processData: false,
		contentType: false,
		headers: {
			"X-Requested-With": "XMLHttpRequest",
			"Accept": "application/json",
			"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
		},
		success: function (res) {
			$btn.prop('disabled', false);
			if (res.success) {
				applyJobListHideModal();
				swal({ title: "{{ __('Success') }}", text: res.message || "{{ __('You have successfully applied for this job') }}", icon: "success" });
				window.location.reload();
			} else {
				swal({ title: "{{ __('Error') }}", text: res.message || "{{ __('Something went wrong.') }}", icon: "error" });
			}
		},
		error: function (xhr) {
			$btn.prop('disabled', false);
			var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('Something went wrong.') }}";
			swal({ title: "{{ __('Error') }}", text: msg, icon: "error" });
		},
		complete: function () {
			applySubmitOverlayHide();
		}
	});
});
@endif

</script>

@include('includes.country_state_city_js')

@endpush