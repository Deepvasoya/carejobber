@extends('admin.layouts.admin_layout')

@section('content')
@push('css')
<style>
    .page-title-head { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .page-title-head .flex-grow-1 { flex: 1; min-width: 0; }
    .page-main-title { margin: 0; font-size: 1.25rem; font-weight: 600; }
    .card-header-actions { float: right; }
    .stats-card { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; margin-bottom: 20px; text-align: center; }
    .stats-card h3 { margin: 0; font-size: 24px; color: #0d6efd; }
    .stats-card p { margin: 0; color: #6c757d; font-size: 14px; }
</style>
@endpush

<div class="page-title-head">
    <div class="flex-grow-1">
        <h4 class="page-main-title">Manage Job Scraper</h4>
        <nav>
            <ol class="breadcrumb-dhonu breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Job Scraper</li>
            </ol>
        </nav>
    </div>
</div>

@include('flash::message')

<div class="row">
    <!-- HTML Job Scraper -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header border-bottom-0">
                <h5 class="card-title mb-0">Scrape Job from URL</h5>
            </div>
            <div class="card-body">
                <p>Enter a job posting URL and the system will attempt to extract job details automatically.</p>
                <div class="input-group mb-3">
                    <input type="url" id="scrape-url-input" class="form-control" placeholder="https://example.com/jobs/..." required>
                    <button type="button" class="btn btn-primary" id="scrape-url-btn">
                        <i class="ri-search-line align-middle me-1"></i> Scrape
                    </button>
                </div>
                <div id="scrape-result" style="display:none;">
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" id="scraped-title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name <small class="text-muted">(auto-matched)</small></label>
                            <input type="text" id="scraped-company" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Functional Area / Classification</label>
                            <input type="text" id="scraped-functional-area" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Job Type / Employee Class</label>
                            <input type="text" id="scraped-job-type" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Job Shift / Shift Pattern</label>
                            <input type="text" id="scraped-job-shift" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primary Location</label>
                            <input type="text" id="scraped-primary-location" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">City <small class="text-muted">(auto-detected)</small></label>
                            <input type="text" id="scraped-city-name" class="form-control" readonly>
                            <input type="hidden" id="scraped-city-id">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Application Deadline</label>
                            <input type="text" id="scraped-expiry-date" class="form-control" placeholder="e.g. Jul 25, 2026">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Min. Salary</label>
                            <input type="text" id="scraped-salary-min" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Max. Salary</label>
                            <input type="text" id="scraped-salary-max" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Hours per Shift</label>
                            <input type="text" id="scraped-hours-per-shift" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">FTE</label>
                            <input type="text" id="scraped-fte" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Union</label>
                            <input type="text" id="scraped-union" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Shifts per Cycle</label>
                            <input type="text" id="scraped-shifts-per-cycle" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Apply URL</label>
                            <input type="url" id="scraped-apply-url" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="scraped-description" class="form-control" rows="6"></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success" id="save-scraped-job-btn">
                        <i class="ri-save-line align-middle me-1"></i> Save as Job
                    </button>
                    <div id="scrape-loading" class="mt-2 text-muted" style="display:none;">
                        <i class="ri-loader-4-line align-middle me-1 fa-spin"></i> Scraping...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trigger Scraper -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header border-bottom-0">
                <h5 class="card-title mb-0">Run Scraper</h5>
            </div>
            <div class="card-body">
                <p>Clicking the button below will manually trigger the scraper to fetch jobs from all active feed sources. This process may take a minute to complete depending on the number of URLs.</p>
                <form action="{{ route('admin.scraper.run') }}" method="POST" onsubmit="return confirm('Are you sure you want to run the scraper manually? This may take up to 60 seconds.');">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="ri-play-circle-line align-middle me-1"></i> Run Scraper Now</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scheduled Cron Jobs -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header border-bottom-0">
                <h5 class="card-title mb-0">Configured Cron Schedules</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Command</th>
                                <th>Expression</th>
                                <th>Next Run Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($crons as $cron)
                            <tr>
                                <td><code>{{ $cron['command'] }}</code></td>
                                <td><span class="badge bg-primary">{{ $cron['expression'] }}</span></td>
                                <td>{{ $cron['next_run'] }}</td>
                                <td>
                                    @php
                                        $valid = [
                                            'jobs:scrape',
                                            'jobs:scrape ahs',
                                            'jobs:scrape covenant',
                                            'jobs:scrape ab-ltc',
                                            'jobs:scrape ab-agencies',
                                            'jobs:expire',
                                            'indexnow:ping'
                                        ];
                                    @endphp
                                    @if(in_array($cron['command'], $valid))
                                        <form action="{{ route('admin.scraper.run_command') }}" method="POST" style="display:inline;" onsubmit="return confirm('Run command: {{ $cron['command'] }}?');">
                                            @csrf
                                            <input type="hidden" name="command" value="{{ $cron['command'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="ri-play-fill"></i> Run Now</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No scheduled jobs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Cron Run History -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Cron Run History <small class="text-muted">(last 20 runs)</small></h5>
                @if($cronLogs->isNotEmpty())
                    <small class="text-muted">Last run: {{ $cronLogs->first()->started_at->diffForHumans() }}</small>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Command</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Finished</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cronLogs as $log)
                            <tr>
                                <td><code>{{ $log->command }}</code></td>
                                <td>
                                    @if($log->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($log->status == 'started')
                                        <span class="badge bg-info">Running</span>
                                    @elseif($log->status == 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->started_at ? $log->started_at->format('M d, H:i:s') : '-' }}</td>
                                <td>{{ $log->finished_at ? $log->finished_at->format('M d, H:i:s') : '-' }}</td>
                                <td>
                                    @if($log->started_at && $log->finished_at)
                                        {{ $log->finished_at->diffInSeconds($log->started_at) }}s
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    No cron runs recorded yet. 
                                    @if($crons)
                                        <a href="javascript:void(0);" onclick="document.querySelector('[action=\'{{ route('admin.scraper.run') }}\'] button')?.click();">Run scraper now</a> to see the first entry.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Feed Sources -->
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header border-bottom-0">
                <h5 class="card-title mb-0">Add Job Feed Source</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.scraper.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. AHS Jobs" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Source URL (Sitemap XML)</label>
                        <input type="url" name="source_url" class="form-control" placeholder="https://example.com/sitemap.xml" required>
                    </div>
                    <button type="submit" class="btn btn-success">Add Source</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom-0">
                <h5 class="card-title mb-0">Existing Feed Sources</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Provider</th>
                                <th>Last Run</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sources as $source)
                            <tr>
                                <td>
                                    <strong>{{ $source->name }}</strong><br>
                                    <small class="text-muted"><a href="{{ $source->source_url }}" target="_blank">View URL</a></small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $source->provider }}</span></td>
                                <td>{{ $source->last_run_at ? $source->last_run_at->diffForHumans() : 'Never' }}</td>
                                <td>
                                    @if($source->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted">No feed sources found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Runs -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header border-bottom-0">
                <h5 class="card-title mb-0">Recent Scraper Runs</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Discovered</th>
                                <th>Imported</th>
                                <th>Skipped</th>
                                <th>Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($runs as $run)
                            <tr>
                                <td>
                                    {{ $run->started_at ? $run->started_at->format('M d, H:i') : 'N/A' }}
                                    @if($run->finished_at && $run->started_at)
                                    <br><small class="text-muted">{{ $run->finished_at->diffInSeconds($run->started_at) }}s</small>
                                    @endif
                                </td>
                                <td>
                                    @if($run->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($run->status == 'completed_with_errors')
                                        <span class="badge bg-warning">Completed (Errors)</span>
                                    @elseif($run->status == 'running')
                                        <span class="badge bg-info">Running</span>
                                    @elseif($run->status == 'skipped')
                                        <span class="badge bg-secondary">Skipped</span>
                                    @else
                                        <span class="badge bg-danger">{{ ucfirst($run->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $run->discovered_count }}</td>
                                <td>
                                    @if($run->imported_count > 0 && $run->imported_log)
                                        <a href="javascript:void(0);" class="text-primary fw-bold js-view-imported" data-log="{{ $run->imported_log }}">{{ $run->imported_count }}</a>
                                    @else
                                        <strong>{{ $run->imported_count }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if($run->skipped_count > 0 && $run->skipped_log)
                                        <a href="javascript:void(0);" class="text-secondary fw-bold js-view-skipped" data-log="{{ $run->skipped_log }}">{{ $run->skipped_count }}</a>
                                    @else
                                        {{ $run->skipped_count }}
                                    @endif
                                </td>
                                <td>
                                    @if($run->error_message)
                                        <button type="button" class="btn btn-sm btn-outline-danger js-view-error" data-error="{{ $run->error_message }}">View</button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">No runs found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Imported Jobs Modal -->
<div class="modal fade" id="importedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Imported Jobs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>URL</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="importedModalBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skipped Jobs Modal -->
<div class="modal fade" id="skippedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Skipped Jobs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>URL</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody id="skippedModalBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // HTML URL Scraper
    const scrapeBtn = document.getElementById('scrape-url-btn');
    const scrapeInput = document.getElementById('scrape-url-input');
    const scrapeResult = document.getElementById('scrape-result');
    const scrapeLoading = document.getElementById('scrape-loading');

    if (scrapeBtn) {
        scrapeBtn.addEventListener('click', function() {
            const url = scrapeInput.value.trim();
            if (!url) {
                toastr.error('Please enter a job URL');
                return;
            }

            scrapeResult.style.display = 'none';
            scrapeLoading.style.display = 'block';
            scrapeBtn.disabled = true;

            $.ajax({
                url: '{{ route("admin.scraper.scrape_url") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    job_url: url
                },
                success: function(response) {
                    scrapeLoading.style.display = 'none';
                    scrapeBtn.disabled = false;

                    if (response.success) {
                        const d = response.data;
                        document.getElementById('scraped-title').value = d.title || '';
                        document.getElementById('scraped-company').value = d.company_name || '';
                        document.getElementById('scraped-functional-area').value = d.functional_area || '';
                        document.getElementById('scraped-job-type').value = d.job_type || '';
                        document.getElementById('scraped-job-shift').value = d.job_shift || '';
                        document.getElementById('scraped-primary-location').value = d.job_primary_location || d.location || '';
                        document.getElementById('scraped-city-name').value = d.city_name || '';
                        document.getElementById('scraped-city-id').value = d.city_id || '';
                        document.getElementById('scraped-expiry-date').value = d.expiry_date || '';
                        document.getElementById('scraped-salary-min').value = d.salary_min || '';
                        document.getElementById('scraped-salary-max').value = d.salary_max || '';
                        document.getElementById('scraped-hours-per-shift').value = d.hours_per_shift || '';
                        document.getElementById('scraped-fte').value = d.fte || '';
                        document.getElementById('scraped-union').value = d.union || '';
                        document.getElementById('scraped-shifts-per-cycle').value = d.shifts_per_cycle || '';
                        document.getElementById('scraped-apply-url').value = d.apply_url || '';
                        document.getElementById('scraped-description').value = d.description || '';
                        scrapeResult.style.display = 'block';
                        toastr.success('Job details extracted successfully! Review and click Save.');
                    } else {
                        toastr.error(response.message || 'Failed to scrape URL');
                    }
                },
                error: function(xhr) {
                    scrapeLoading.style.display = 'none';
                    scrapeBtn.disabled = false;
                    let msg = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg);
                }
            });
        });
    }

    const saveBtn = document.getElementById('save-scraped-job-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const title = document.getElementById('scraped-title').value.trim();
            if (!title) {
                toastr.error('Please enter a job title');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="ri-loader-4-line align-middle me-1 fa-spin"></i> Saving...';

            $.ajax({
                url: '{{ route("admin.scraper.save_scraped_job") }}',
                type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: title,
                        description: document.getElementById('scraped-description').value,
                        company_name: document.getElementById('scraped-company').value,
                        location: document.getElementById('scraped-primary-location').value,
                        job_type: document.getElementById('scraped-job-type').value,
                        salary: '',
                        salary_min: document.getElementById('scraped-salary-min').value,
                        salary_max: document.getElementById('scraped-salary-max').value,
                        apply_url: document.getElementById('scraped-apply-url').value,
                        job_primary_location: document.getElementById('scraped-primary-location').value,
                        job_shift: document.getElementById('scraped-job-shift').value,
                        functional_area: document.getElementById('scraped-functional-area').value,
                        union: document.getElementById('scraped-union').value,
                        fte: document.getElementById('scraped-fte').value,
                        hours_per_shift: document.getElementById('scraped-hours-per-shift').value,
                        shifts_per_cycle: document.getElementById('scraped-shifts-per-cycle').value,
                        expiry_date: document.getElementById('scraped-expiry-date').value,
                        city_id: document.getElementById('scraped-city-id').value,
                    },
                success: function(response) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ri-save-line align-middle me-1"></i> Save as Job';
                    if (response.success) {
                        toastr.success('Job created successfully! <a href="' + response.edit_url + '" style="color:#fff;text-decoration:underline;margin-left:8px;">Edit Job</a>', '', {allowHtml: true, timeOut: 5000});
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(response.message || 'Failed to save job');
                    }
                },
                error: function(xhr) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="ri-save-line align-middle me-1"></i> Save as Job';
                    let msg = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg);
                }
            });
        });
    }

    // Existing code below
    document.querySelectorAll('.js-view-error').forEach(function(button) {
        button.addEventListener('click', function() {
            alert(this.getAttribute('data-error'));
        });
    });

    document.querySelectorAll('.js-view-imported').forEach(function(button) {
        button.addEventListener('click', function() {
            var logs = JSON.parse(this.getAttribute('data-log') || '[]');
            var tbody = document.getElementById('importedModalBody');
            tbody.innerHTML = '';
            logs.forEach(function(log) {
                var title = log.title || 'N/A';
                var url = log.url || 'N/A';
                var status = log.status || 'N/A';
                var statusBadge = status === 'imported' ? '<span class="badge bg-success">Imported</span>' : '<span class="badge bg-info">Updated</span>';
                tbody.innerHTML += `<tr><td>${title}</td><td><a href="${url}" target="_blank" style="word-break: break-all;">${url}</a></td><td>${statusBadge}</td></tr>`;
            });
            var myModal = new bootstrap.Modal(document.getElementById('importedModal'));
            myModal.show();
        });
    });

    document.querySelectorAll('.js-view-skipped').forEach(function(button) {
        button.addEventListener('click', function() {
            var logs = JSON.parse(this.getAttribute('data-log') || '[]');
            var tbody = document.getElementById('skippedModalBody');
            tbody.innerHTML = '';
            logs.forEach(function(log) {
                var title = log.title || 'N/A';
                var url = log.url || 'N/A';
                var reason = log.reason || 'N/A';
                tbody.innerHTML += `<tr><td>${title}</td><td><a href="${url}" target="_blank" style="word-break: break-all;">${url}</a></td><td><span class="text-danger">${reason}</span></td></tr>`;
            });
            var myModal = new bootstrap.Modal(document.getElementById('skippedModal'));
            myModal.show();
        });
    });
});
</script>
@endpush
