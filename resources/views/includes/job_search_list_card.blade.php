@php
    $columnClass = $columnClass ?? 'col-12';
    $hasApplied = false;
    if (Auth::check()) {
        $hasApplied = \App\JobApply::where('job_id', $job->id)
            ->where('user_id', Auth::user()->id)
            ->exists();
    }
    $jobShiftLabel = $job->getJobShift('job_shift');
    $benefitLines = $job->getBenefitsPreviewLines(4);
    $expired = $job->isJobExpired();
@endphp
<li class="{{ $columnClass }} @if ($job->isPromotionFeaturedActive()) featured @endif">
    <div
        class="jobint job-list-card-enhanced job-list-card-compact @if ($job->isPromotionHighlightedActive() && !$hasApplied) job-card-highlighted @endif @if ($hasApplied) job-list-card-applied @endif">
        <div class="job-list-card-top">
            <div class="job-list-card-badges">
                @if ($job->isPromotionUrgentActive())
                    <span class="job-list-pill job-list-pill-urgent"><i class="fas fa-fire"></i>
                        {{ __('Urgent hiring') }}</span>
                @endif
                @if ($job->isPromotionFeaturedActive())
                    <span class="job-list-pill job-list-pill-featured"><i class="fas fa-bolt"></i>
                        {{ __('Featured job') }}</span>
                @endif
                @if ($job->isPromotionHighlightedActive())
                    <span class="job-list-pill job-list-pill-highlight"><i class="fas fa-star"></i>
                        {{ __('Highlighted') }}</span>
                @endif
                @if ($hasApplied)
                    <span class="job-list-pill job-list-pill-applied"><i class="fa fa-check-circle"></i>
                        {{ __('Applied') }}</span>
                @endif
            </div>
            <div class="job-list-card-save">
                @guest
                    <a href="{{ route('login') }}" class="job-list-save-btn" title="{{ __('Sign in to save jobs') }}"
                        aria-label="{{ __('Save job') }}"><i class="far fa-heart"></i></a>
                @else
                    @if (Auth::user()->isFavouriteJob($job->slug))
                        <a href="{{ route('remove.from.favourite', $job->slug) }}"
                            class="job-list-save-btn job-list-save-btn-active" title="{{ __('Remove from saved') }}"
                            aria-label="{{ __('Remove from saved') }}"><i class="fas fa-heart"></i></a>
                    @else
                        <a href="{{ route('add.to.favourite', $job->slug) }}" class="job-list-save-btn"
                            title="{{ __('Save job') }}" aria-label="{{ __('Save job') }}"><i
                                class="far fa-heart"></i></a>
                    @endif
                @endguest
            </div>
        </div>

        <div class="job-list-card-main">
            <div class="job-list-card-body">

                <h4 class="job-list-card-title"><a href="{{ route('job.detail', [$job->slug]) }}"
                        title="{{ $job->title }}">{{ \Illuminate\Support\Str::limit($job->title, 72) }}</a>
                </h4>

                <dl class="job-list-card-meta">
                    <div class="job-list-meta-row">
                        <dt><i class="fas fa-money-bill-wave"></i> {{ __('Salary') }}</dt>
                        <dd>
                            @if (!(bool) $job->hide_salary)
                                <strong>{{ $job->salary_currency }}{{ $job->salary_from }} –
                                    {{ $job->salary_currency }}{{ $job->salary_to }}</strong>
                                <span class="job-list-meta-muted">/
                                    {{ $job->getSalaryPeriod('salary_period') }}</span>
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
                            @if (count($benefitLines))
                                <span class="job-list-benefits-inline">
                                    @foreach ($benefitLines as $i => $line)
                                        @if ($i > 0)
                                            <span class="job-list-meta-muted"> · </span>
                                        @endif
                                        <span>{{ \Illuminate\Support\Str::limit($line, 90) }}</span>
                                    @endforeach
                                </span>
                            @else
                                <span class="job-list-meta-muted">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="job-list-meta-row">
                        <dt><i class="fas fa-calendar-times"></i> {{ __('Application deadline') }}</dt>
                        <dd>
                            @if ($job->expiry_date)
                                <strong>{{ \Carbon\Carbon::parse($job->expiry_date)->format('M d, Y') }}</strong>
                                @if ($expired)
                                    <span
                                        class="job-list-pill job-list-pill-expired ms-1">{{ __('Expired') }}</span>
                                @endif
                            @else
                                <span class="job-list-meta-muted">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="job-list-meta-row">
                        <dt><i class="fas fa-map-marker-alt"></i> {{ __('Location') }}</dt>
                        <dd>{{ trim(implode(', ', array_filter([$job->getCity('city'), $job->getState('state')]))) ?: '—' }}
                        </dd>
                    </div>
                </dl>

                <div class="job-list-card-company">
                    <a href="{{ route('company.detail', $company->slug) }}" class="job-list-company-logo"
                        title="{{ $company->name }}">{{ $company->printCompanyImage() }}</a>
                    <div class="job-list-company-text">
                        <a href="{{ route('company.detail', $company->slug) }}" class="job-list-company-name"
                            title="{{ $company->name }}">{{ $company->name }}</a>
                        @include('components.verified-badge', ['company' => $company])
                        <div class="job-list-posted">{{ __('Posted') }}:
                            {{ $job->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

            </div>

            <div class="job-list-card-actions job-list-card-actions--stack">
                @if ($expired)
                    <span class="job-list-apply job-list-apply-disabled"><i class="fas fa-ban"></i>
                        {{ __('Job closed') }}</span>
                @elseif(Auth::check() && $hasApplied)
                    <span class="job-list-apply job-list-apply-done"><i class="fas fa-check-circle"></i>
                        {{ __('Already applied') }}</span>
                @elseif(Auth::check())
                    <a href="javascript:void(0);" class="job-list-apply job-list-apply-primary js-job-list-open-apply"
                        role="button" data-job-slug="{{ $job->slug }}">
                        <i class="fas fa-paper-plane"></i> {{ __('Apply now') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="job-list-apply job-list-apply-primary"><i
                            class="fas fa-paper-plane"></i>
                        {{ __('Apply now') }}</a>
                @endif
                <a href="{{ route('job.detail', [$job->slug]) }}"
                    class="job-list-apply job-list-apply-secondary">{{ __('View details') }}</a>
            </div>
        </div>

    </div>
</li>
