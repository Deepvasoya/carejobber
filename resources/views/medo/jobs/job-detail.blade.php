@extends('layouts.app')

@section('page_title', $job->title . ' | ' . $employer->name . ' | ' . $city->name . ', ' . $province->code)

@section('content')
<div class="container mt-4">
    @include('medo.partials.breadcrumbs', ['items' => $breadcrumbs])
    @include('medo.partials.job-posting-schema', ['jobs' => collect([$job])])
    @include('medo.partials.breadcrumb-schema', ['items' => $breadcrumbs])

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ $job->title }}</h1>
                    
                    <div class="mb-3">
                        <strong>{{ $employer->name }}</strong> · {{ $city->name }}, {{ $province->code }}
                    </div>

                    <div class="mb-3">
                        @if($job->employment_type)
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', '-', $job->employment_type)) }}</span>
                        @endif
                        @if($job->shift_type)
                            <span class="badge bg-secondary">{{ ucfirst($job->shift_type) }}</span>
                        @endif
                        @if($job->wage_min)
                            <span class="badge bg-success">${{ number_format($job->wage_min, 2) }}–${{ number_format($job->wage_max, 2) }}/{{ $job->wage_period === 'hourly' ? 'hr' : 'yr' }}</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <small class="text-muted">Posted {{ $job->posted_at?->diffForHumans() ?? $job->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="mb-4">
                        <a href="{{ $job->apply_url }}" target="_blank" rel="nofollow" class="btn btn-primary btn-lg">
                            Apply on {{ $employer->name }}'s site
                        </a>
                    </div>

                    <div class="job-description mb-4">
                        {!! $job->description !!}
                    </div>

                    <div class="mt-4">
                        <a href="{{ $job->apply_url }}" target="_blank" rel="nofollow" class="btn btn-primary btn-lg">
                            Apply for this {{ $category->name }} position
                        </a>
                    </div>
                </div>
            </div>

            @include('medo.partials.related-jobs', ['groups' => $relatedJobs])
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Job Details</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Category:</strong> {{ $category->name }}</li>
                        <li class="mb-2"><strong>Location:</strong> {{ $city->name }}, {{ $province->name }}</li>
                        @if($job->employment_type)
                            <li class="mb-2"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</li>
                        @endif
                        @if($job->shift_type)
                            <li class="mb-2"><strong>Shift:</strong> {{ ucfirst($job->shift_type) }}</li>
                        @endif
                        @if($job->setting)
                            <li class="mb-2"><strong>Setting:</strong> {{ ucfirst($job->setting) }}</li>
                        @endif
                        @if($job->is_new_grad_friendly)
                            <li class="mb-2"><span class="badge bg-info">New Grad Friendly</span></li>
                        @endif
                        @if($job->has_signing_bonus)
                            <li class="mb-2"><span class="badge bg-warning">Signing Bonus</span></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
