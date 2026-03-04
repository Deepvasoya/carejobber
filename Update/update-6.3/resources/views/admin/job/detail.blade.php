@extends('admin.layouts.admin_layout')
@push('css')
<style>
    .job-detail-list { list-style: none; padding: 0; margin: 0; }
    .job-detail-list li { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
    .job-detail-list li:last-child { border-bottom: 0; }
    .company-overview-logo { max-width: 80px; overflow: hidden; border-radius: 8px; }
    .company-overview-logo img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; display: block; }
    .skillslist { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 6px; }
    .skillslist li a { display: inline-block; padding: 4px 10px; background: #e9ecef; border-radius: 6px; font-size: 0.875rem; text-decoration: none; color: #495057; }
    .skillslist li a:hover { background: #405189; color: #fff; }
</style>
@endpush
@section('content')
@php
$company = $job->getCompany();
$salary = '';
if ($job->salary_type == 'single_salary') {
    $salary = $job->salary_from !== null ? ($job->salary_currency . ' ' . number_format($job->salary_from)) : '';
} elseif ($job->salary_type == 'salary_in_range') {
    $salary = $job->salary_from !== null ? ($job->salary_currency . ' ' . number_format($job->salary_from) . ' - ' . $job->salary_currency . ' ' . number_format($job->salary_to)) : '';
} elseif ($job->salary_type == 'negotiable') {
    $salary = $job->salary_from ?: __('Negotiable');
} else {
    $salary = $job->salary_from ? ($job->salary_currency . ' ' . $job->salary_from) : '';
}
@endphp

<div class="page-bar mb-3">
    <ul class="page-breadcrumb mb-0">
        <li><a href="{{ route('admin.home') }}">Home</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><a href="{{ route('list.jobs') }}">Jobs</a> <i class="ri ri-arrow-right-s-line text-muted"></i></li>
        <li><span>Job Details</span></li>
    </ul>
</div>
@include('flash::message')

{{-- Actions --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h4 class="mb-0">{{ $job->title }}</h4>
    <div class="d-flex gap-2">
        @if($job->status == 'request_to_delete')
        <button type="button" class="btn btn-danger btn-sm" onclick="deleteJob({{ $job->id }})" data-job="{{ $job->title }}" id="job-{{ $job->id }}">
            <i class="ri ri-delete-bin-line me-1"></i> Delete
        </button>
        @endif
        <a href="{{ route('edit.job', ['id' => $job->id]) }}" class="btn btn-primary btn-sm">
            <i class="ri ri-pencil-line me-1"></i> Edit Job
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        {{-- Job meta (date, salary) --}}
        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-1 small"><i class="ri ri-calendar-line me-1"></i> {{ __('Date Posted') }}: {{ $job->created_at->format('M d, Y') }}</p>
                @if(!(bool)$job->hide_salary && $salary)
                <p class="mb-0"><strong>{{ $job->getSalaryPeriod('salary_period') }}:</strong> {{ $salary }}</p>
                @endif
            </div>
        </div>

        {{-- Job Details list --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-list-check me-1"></i> {{ __('Job Details') }}</h5>
            </div>
            <div class="card-body">
                <ul class="job-detail-list">
                    <li><span class="text-muted">{{ __('Location(s)') }}</span><span>{{ (bool)$job->is_freelance ? __('Freelance') : $job->getLocation() }}</span></li>
                    <li><span class="text-muted">{{ __('Company') }}</span><span>{{ $company ? $company->name : '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Type') }}</span><span>{{ $job->getJobType('job_type') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Shift') }}</span><span>{{ $job->getJobShift('job_shift') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Career Level') }}</span><span>{{ $job->getCareerLevel('career_level') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Positions') }}</span><span>{{ $job->num_of_positions ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Experience') }}</span><span>{{ $job->getJobExperience('job_experience') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Gender') }}</span><span>{{ $job->getGender('gender') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Degree') }}</span><span>{{ $job->getDegreeLevel('degree_level') ?? '–' }}</span></li>
                    <li><span class="text-muted">{{ __('Apply Before') }}</span><span>{{ $job->expiry_date ? \Carbon\Carbon::parse($job->expiry_date)->format('M d, Y') : '–' }}</span></li>
                </ul>
            </div>
        </div>

        {{-- Job Description --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-file-text-line me-1"></i> {{ __('Job Description') }}</h5>
            </div>
            <div class="card-body">
                <div class="prose">{!! $job->description !!}</div>
            </div>
        </div>

        {{-- Benefits --}}
        @if($job->benefits)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-gift-line me-1"></i> {{ __('Benefits') }}</h5>
            </div>
            <div class="card-body">
                <div class="prose">{!! $job->benefits !!}</div>
            </div>
        </div>
        @endif

        {{-- Skills Required --}}
        @if($job->getJobSkillsList())
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-lightbulb-line me-1"></i> {{ __('Skills Required') }}</h5>
            </div>
            <div class="card-body">
                <ul class="skillslist">{!! $job->getJobSkillsList() !!}</ul>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        @if($job->isJobExpired())
        <div class="alert alert-warning mb-3">
            <i class="ri ri-time-line me-1"></i> {{ __('Job Expired') }}
        </div>
        @endif

        {{-- Company Overview --}}
        @if($company)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-building-line me-1"></i> {{ __('Company Overview') }}</h5>
            </div>
            <div class="card-body text-center">
                <div class="company-overview-logo d-inline-block mb-2">{!! $company->printCompanyImage(80, 80) !!}</div>
                <h6 class="mb-1">{{ $company->name }}</h6>
                <p class="text-muted small mb-0">{{ $company->getLocation() }}</p>
            </div>
        </div>
        @endif

        {{-- Job Location on Map --}}
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri ri-map-pin-2-line me-1"></i> {{ __('Job Location on Map') }}</h5>
            </div>
            <div class="card-body p-0">
                <iframe src="https://maps.google.it/maps?q={{ urlencode(strip_tags($job->getLocation())) }}&output=embed" width="100%" height="270" frameborder="0" style="border:0; display:block;" allowfullscreen="" title="Job location map"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style type="text/css">.formrow iframe { height: 78px; }</style>
@endpush

@push('scripts')
<script>
function deleteJob(id) {
    var msg = 'Please confirm you want to delete Job: ' + ($('#job-' + id).data('job') || '');
    if (confirm(msg)) {
        $.post("{{ route('delete.job') }}", { id: id, _method: 'DELETE', _token: '{{ csrf_token() }}' })
            .done(function(response) {
                if (response == 'ok') location.reload();
                else alert('Request Failed!');
            })
            .fail(function() { alert('Request Failed!'); });
    }
}
function makeActive(id) {
    $.post("{{ route('make.active.job') }}", { id: id, _method: 'PUT', _token: '{{ csrf_token() }}' })
        .done(function(response) {
            if (response == 'ok') {
                $('#onclickActive{{ $job->id }} i').removeClass('ri-checkbox-blank-line').addClass('ri-checkbox-line');
                location.reload();
            } else alert('Request Failed!');
        });
}
function makeNotActive(id) {
    $.post("{{ route('make.not.active.job') }}", { id: id, _method: 'PUT', _token: '{{ csrf_token() }}' })
        .done(function(response) {
            if (response == 'ok') {
                $('#onclickActive{{ $job->id }} i').removeClass('ri-checkbox-line').addClass('ri-checkbox-blank-line');
                location.reload();
            } else alert('Request Failed!');
        });
}
function makeNotReviewed(id) {
    $('#rejected_job_id').val(id);
    var modal = document.getElementById('rejectedModal');
    if (modal && typeof bootstrap !== 'undefined') new bootstrap.Modal(modal).show();
    else $(modal).modal('show');
}
</script>
@endpush
