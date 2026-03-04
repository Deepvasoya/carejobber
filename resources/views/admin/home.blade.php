@extends('admin.layouts.admin_layout')
@section('content')
@push('css')
<style>
    .page-title-head { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .page-title-head .flex-grow-1 { flex: 1; min-width: 0; }
    .page-main-title { margin: 0; font-size: 1.25rem; font-weight: 600; }
    .page-title-head .text-end { margin-left: auto; }
    .breadcrumb-dhonu { background: none; padding: 0; margin: 0; font-size: 0.875rem; }
    .breadcrumb-dhonu .breadcrumb-item + .breadcrumb-item::before { content: "/"; padding: 0 6px; color: #adb5bd; }
    .chart-wrap { position: relative; height: 280px; }
    .section-card .list-unstyled li { padding: 12px 20px; border-bottom: 1px solid #f6f6f6; display: flex; align-items: flex-start; gap: 10px; }
    .section-card .feed-badge { width: 36px; height: 36px; border-radius: 8px; background: #e3e9ef; color: #405189; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-card .feed-badge-img { padding: 0; overflow: hidden; background: #fff; }
    .section-card .feed-badge-img img { width: 36px; height: 36px; object-fit: cover; border-radius: 8px; display: block; }
    .scroller-inner { max-height: 320px; overflow-y: auto; }
    .dashnav-dhonu { list-style: none; padding: 0; margin: 0 -6px; display: flex; flex-wrap: wrap; }
    .dashnav-dhonu li { padding: 6px; }
    .dashnav-dhonu li a { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px 12px; min-width: 90px; border-radius: 8px; background: #fff; color: #495057; border: 1px solid #e9ecef; text-decoration: none; font-size: 0.8125rem; font-weight: 500; transition: all .2s; }
    .dashnav-dhonu li a:hover { background: #405189; color: #fff; border-color: #405189; }
</style>
@endpush

{{-- Page title row --}}
        <div class="page-title-head">
            <div class="flex-grow-1">
                <h4 class="page-main-title">Welcome to {{ $siteSetting->site_name }}</h4>
            </div>
            
        </div>

        {{-- First row: stat cards (shown per permission) --}}
        <div class="row g-2 mb-2">
            @if(APAuthHelp::can('site_users.view'))
            <div class="col-6 col-md-3">
                <div class="card m-0">
                    <div class="card-header border-0">
                        <h4 class="card-title">Today's Users</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2 py-1">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title text-bg-primary rounded-circle">
                                    <i class="ri ri-user-line fs-xxl"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalTodaysUsers) }}</h3>
                        </div>
                        <p class="mb-0 text-muted text-center">
                            <span class="text-nowrap">Registered today</span>
                        </p>
                    </div>
                </div>
            </div>
            @endif
            @if(APAuthHelp::can('payments.view'))
            <div class="col-6 col-md-3">
                <a href="{{ route('list.payment.hostory') }}" style="text-decoration:none;color:inherit;">
                <div class="card m-0">
                    <div class="card-header border-0">
                        <h4 class="card-title">Total Revenue</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2 py-1">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title text-bg-success rounded-circle">
                                    <i class="ri ri-money-dollar-circle-line fs-xxl"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ $siteSetting->default_currency_code ?? 'USD' }} {{ number_format($totalRevenue, 0) }}</h3>
                        </div>
                        <p class="mb-0 text-muted text-center">
                            <span class="text-nowrap">Completed payments</span>
                        </p>
                    </div>
                </div>
                </a>
            </div>
            @endif
            @if(APAuthHelp::can('site_users.view'))
            <div class="col-6 col-md-3">
                <a href="{{ route('list.users') }}" style="text-decoration:none;color:inherit;">
                <div class="card m-0">
                    <div class="card-header border-0">
                        <h4 class="card-title">Active Users</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2 py-1">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title text-bg-warning rounded-circle">
                                    <i class="ri ri-user-3-line fs-xxl"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalActiveUsers) }}</h3>
                        </div>
                        <p class="mb-0 text-muted text-center">
                            <span class="text-nowrap">Active accounts</span>
                        </p>
                    </div>
                </div>
                </a>
            </div>
            @endif
            @if(APAuthHelp::can('jobs.view'))
            <div class="col-6 col-md-3">
                <a href="{{ route('list.jobs') }}" style="text-decoration:none;color:inherit;">
                <div class="card m-0">
                    <div class="card-header border-0">
                        <h4 class="card-title">Active Jobs</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2 py-1">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title text-bg-info rounded-circle">
                                    <i class="ri ri-briefcase-4-line fs-xxl"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalActiveJobs) }}</h3>
                        </div>
                        <p class="mb-0 text-muted text-center">
                            <span class="text-nowrap">Live listings</span>
                        </p>
                    </div>
                </div>
                </a>
            </div>
            @endif
        </div>

        {{-- Second row: Business Performance + New Users charts (per permission) --}}
        @if(APAuthHelp::can('payments.view') || APAuthHelp::can('site_users.view'))
        <div class="row g-2 mb-2">
            @if(APAuthHelp::can('payments.view'))
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 mb-0">
                    <div class="card-header border-0 justify-content-between">
                        <h4 class="card-title">Revenue Overview</h4>
                        <div>
                            <a href="{{ route('list.payment.hostory') }}" class="btn btn-sm btn-default">Company Payments</a>
                            <a href="{{ route('list.users.payment.history') }}" class="btn btn-sm btn-default">Users Payments</a>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="chart-wrap">
                            <canvas id="revenueChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(APAuthHelp::can('site_users.view'))
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 mb-0">
                    <div class="card-header">
                        <h4 class="card-title">New Users / Registrations (Last 12 Months)</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="chart-wrap">
                            <canvas id="visitorsChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Third row: Active Companies (companies.view) --}}
        @if(APAuthHelp::can('companies.view'))
        <div class="row g-2">
            <div class="col-12">
                <div class="card section-card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Active Companies</h4>
                        <a href="{{ route('list.companies') }}">See All Companies <i class="ri ri-arrow-right-s-line"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="scroller-inner slimScrol">
                            <ul class="list-unstyled">
                                @foreach($activeCompanies as $company)
                                <li>
                                    <div class="feed-badge feed-badge-img">{!! $company->printCompanyImage(36, 36) !!}</div>
                                    <div class="feed-desc">
                                        <a href="{{ url('admin/public-company/'.$company->id) }}">{{ $company->name }}</a> ({{ $company->email }}) &middot; {{ $company->getLocation() }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Fourth row: Recent Users + Recent Jobs (per permission) --}}
        @if(APAuthHelp::can('site_users.view') || APAuthHelp::can('jobs.view'))
        <div class="row g-2">
            @if(APAuthHelp::can('site_users.view'))
            <div class="col-md-6">
                <div class="card section-card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Recent Registered Users</h4>
                        <a href="{{ route('list.users') }}">See All <i class="ri ri-arrow-right-s-line"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="scroller-inner slimScrol">
                            <ul class="list-unstyled">
                                @foreach($recentUsers as $recentUser)
                                <li>
                                    <div class="feed-badge feed-badge-img">{!! $recentUser->printUserImage(36, 36) !!}</div>
                                    <div class="feed-desc">
                                        <a href="{{ route('edit.user', $recentUser->id) }}">{{ $recentUser->name }}</a> ({{ $recentUser->email }}) &middot; {{ $recentUser->getLocation() }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if(APAuthHelp::can('jobs.view'))
            <div class="col-md-6">
                <div class="card section-card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Recent Jobs</h4>
                        <a href="{{ route('list.jobs') }}">See All <i class="ri ri-arrow-right-s-line"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="scroller-inner slimScrol">
                            <ul class="list-unstyled">
                                @foreach($recentJobs as $recentJob)
                                <li>
                                    @php $jobCompany = $recentJob->getCompany(); @endphp
                                    <div class="feed-badge {{ $jobCompany ? 'feed-badge-img' : '' }}">
                                        @if($jobCompany)
                                            {!! $jobCompany->printCompanyImage(36, 36) !!}
                                        @else
                                            <i class="ri ri-briefcase-4-line"></i>
                                        @endif
                                    </div>
                                    <div class="feed-desc">
                                        <a href="{{ route('edit.job', $recentJob->id) }}">{{ \Illuminate\Support\Str::limit($recentJob->title, 50) }}</a> &middot; {{ $recentJob->getCompany('name') }} &middot; {{ $recentJob->getLocation() }}
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Quick nav (links shown per permission) --}}
        @if(APAuthHelp::can('jobs.view') || APAuthHelp::can('companies.view') || APAuthHelp::can('site_users.view') || APAuthHelp::can('payments.view') || APAuthHelp::can('cms.view') || APAuthHelp::can('blog.manage') || APAuthHelp::can('seo.manage') || APAuthHelp::can('faq.view') || APAuthHelp::can('testimonials.view') || APAuthHelp::can('packages.manage') || APAuthHelp::can('countries.manage') || APAuthHelp::can('states.manage') || APAuthHelp::can('cities.manage') || APAuthHelp::can('site_settings.manage'))
        <div class="row g-2">
            <div class="col-12">
                <ul class="dashnav-dhonu">
                    @if(APAuthHelp::can('jobs.view'))<li><a href="{{ route('list.jobs') }}"><i class="ri ri-briefcase-4-line"></i> Jobs</a></li>@endif
                    @if(APAuthHelp::can('companies.view'))<li><a href="{{ route('list.companies') }}"><i class="ri ri-building-line"></i> Companies</a></li>@endif
                    @if(APAuthHelp::can('site_users.view'))<li><a href="{{ route('list.users') }}"><i class="ri ri-user-line"></i> Users</a></li>@endif
                    @if(APAuthHelp::can('payments.view'))<li><a href="{{ route('list.payment.hostory') }}"><i class="ri ri-bank-card-line"></i> Company Payments</a></li>@endif
                    @if(APAuthHelp::can('payments.view'))<li><a href="{{ route('list.users.payment.history') }}"><i class="ri ri-wallet-3-line"></i> User Payments</a></li>@endif
                    @if(APAuthHelp::can('cms.view'))<li><a href="{{ route('list.cms') }}"><i class="ri ri-file-text-line"></i> CMS</a></li>@endif
                    @if(APAuthHelp::can('blog.manage'))<li><a href="{{ route('blog') }}"><i class="ri ri-article-line"></i> Blog</a></li>@endif
                    @if(APAuthHelp::can('seo.manage'))<li><a href="{{ route('list.seo') }}"><i class="ri ri-search-line"></i> SEO</a></li>@endif
                    @if(APAuthHelp::can('faq.view'))<li><a href="{{ route('list.faqs') }}"><i class="ri ri-question-line"></i> FAQs</a></li>@endif
                    @if(APAuthHelp::can('testimonials.view'))<li><a href="{{ route('list.testimonials') }}"><i class="ri ri-chat-quote-line"></i> Testimonials</a></li>@endif
                    @if(APAuthHelp::can('packages.manage'))<li><a href="{{ route('list.packages') }}"><i class="ri ri-price-tag-3-line"></i> Packages</a></li>@endif
                    @if(APAuthHelp::can('countries.manage'))<li><a href="{{ route('list.countries') }}"><i class="ri ri-global-line"></i> Countries</a></li>@endif
                    @if(APAuthHelp::can('states.manage'))<li><a href="{{ route('list.states') }}"><i class="ri ri-map-line"></i> States</a></li>@endif
                    @if(APAuthHelp::can('cities.manage'))<li><a href="{{ route('list.cities') }}"><i class="ri ri-map-pin-2-line"></i> Cities</a></li>@endif
                    @if(APAuthHelp::can('site_settings.manage'))<li><a href="{{ route('edit.site.setting') }}"><i class="ri ri-settings-3-line"></i> Settings</a></li>@endif
                </ul>
            </div>
        </div>
        @endif
@endsection

@push('scripts')
<script src="{{ asset('theme/plugins/chartjs/chart.umd.js') }}"></script>
<script>
(function() {
    var revenueLabels = @json($revenueChartLabels ?? []);
    var revenueData = @json($revenueChartData ?? []);
    var newUsersData = @json($newUsersChartData ?? []);
    var currencySymbol = @json($siteSetting->default_currency_code ?? 'USD');

    function initRevenueChart(canvasId) {
        var el = document.getElementById(canvasId);
        if (!el || !revenueLabels.length) return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: 'rgb(64, 81, 137)',
                    backgroundColor: 'rgba(64, 81, 137, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return currencySymbol + ' ' + (context.parsed.y != null ? Number(context.parsed.y).toLocaleString() : '');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,.06)' },
                        ticks: {
                            color: '#6c757d',
                            font: { size: 11 },
                            callback: function(value) {
                                return currencySymbol + ' ' + value;
                            }
                        }
                    },
                    x: { grid: { display: false }, ticks: { color: '#6c757d', font: { size: 10 }, maxRotation: 45 } }
                }
            }
        });
    }

    function initVisitorsChart() {
        var el = document.getElementById('visitorsChart');
        if (!el || !revenueLabels.length) return;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'New Users',
                    data: newUsersData,
                    backgroundColor: 'rgba(41, 156, 219, 0.7)',
                    borderColor: 'rgb(41, 156, 219)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.06)' }, ticks: { color: '#6c757d', font: { size: 11 }, stepSize: 1 } },
                    x: { grid: { display: false }, ticks: { color: '#6c757d', font: { size: 10 }, maxRotation: 45 } }
                }
            }
        });
    }

    initRevenueChart('revenueChart');
    initVisitorsChart();
})();
</script>
<script type="text/javascript">
    $(function () {
        if ($.fn.slimScroll) {
            $('.slimScrol').slimScroll({
                height: '320px',
                railVisible: true,
                alwaysVisible: true
            });
        }
    });
</script>
@endpush
