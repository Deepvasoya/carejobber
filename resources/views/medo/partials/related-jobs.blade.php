@if(isset($groups) && (($groups['same_city'] ?? collect())->count() > 0 || ($groups['same_category'] ?? collect())->count() > 0 || ($groups['same_province'] ?? collect())->count() > 0))
<div class="related-jobs mt-5">
    <h3 class="mb-4">Related Jobs</h3>

    @if(($groups['same_city'] ?? collect())->count() > 0)
    <div class="mb-4">
        <h5>More jobs in {{ $job->city->name }}</h5>
        <div class="row">
            @foreach($groups['same_city'] as $relatedJob)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="{{ route('medo.jobs.detail', [$relatedJob->category, $relatedJob->province, $relatedJob->city, $relatedJob]) }}">
                                {{ $relatedJob->title }}
                            </a>
                        </h6>
                        <p class="card-text small text-muted">
                            {{ $relatedJob->employer->name }}<br>
                            <span class="badge bg-secondary">{{ $relatedJob->category->name }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(($groups['same_category'] ?? collect())->count() > 0)
    <div class="mb-4">
        <h5>More {{ $job->category->name }} jobs in {{ $job->province->name }}</h5>
        <div class="row">
            @foreach($groups['same_category'] as $relatedJob)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="{{ route('medo.jobs.detail', [$relatedJob->category, $relatedJob->province, $relatedJob->city, $relatedJob]) }}">
                                {{ $relatedJob->title }}
                            </a>
                        </h6>
                        <p class="card-text small text-muted">
                            {{ $relatedJob->employer->name }}<br>
                            {{ $relatedJob->city->name }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(($groups['same_province'] ?? collect())->count() > 0)
    <div class="mb-4">
        <h5>More jobs in {{ $job->province->name }}</h5>
        <div class="row">
            @foreach($groups['same_province'] as $relatedJob)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="{{ route('medo.jobs.detail', [$relatedJob->category, $relatedJob->province, $relatedJob->city, $relatedJob]) }}">
                                {{ $relatedJob->title }}
                            </a>
                        </h6>
                        <p class="card-text small text-muted">
                            {{ $relatedJob->employer->name }}<br>
                            {{ $relatedJob->city->name }} · <span class="badge bg-secondary">{{ $relatedJob->category->name }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
