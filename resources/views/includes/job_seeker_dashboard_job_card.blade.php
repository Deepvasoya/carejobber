{{-- Shared card: pass $job; optional $appliedJob (JobApply) for "My Applied Jobs", omit for recommended --}}
@php
    $company = $job->company ?? null;
@endphp
@if($job && $company)
    <li class="col-lg-4 col-md-6 @if($job->isPromotionFeaturedActive()) featured @endif">
        <div class="jobint mt-0 mb-3">
            @if($job->isPromotionFeaturedActive())
                <span class="promotepof-badge"><i class="fa fa-bolt" title="{{__('Featured Job')}}"></i></span>
            @endif
            <div class="d-flex justify-content-between align-items-center">
                <div class="fticon"><i class="fas fa-briefcase"></i> {{ $job->getJobType('job_type') }}</div>
                @isset($appliedJob)
                    @php
                        $statusClass = 'secondary';
                        if ($appliedJob->status == 'pending') {
                            $statusClass = 'warning';
                        } elseif (in_array($appliedJob->status, ['approved', 'hire', 'hired'])) {
                            $statusClass = 'success';
                        } elseif ($appliedJob->status == 'rejected') {
                            $statusClass = 'danger';
                        }
                    @endphp
                    <strong class="badge bg-{{ $statusClass }}">
                        {{ ucfirst($appliedJob->status) }}
                    </strong>
                @else
                    <strong class="badge bg-secondary">{{ __('Recommended') }}</strong>
                @endisset
            </div>
            <h4>
                <a href="{{ route('job.detail', [$job->slug]) }}" title="{{ $job->title }}">
                    {!! \Illuminate\Support\Str::limit($job->title, 20, '...') !!}
                </a>
            </h4>
            @if(!(bool) $job->hide_salary)
                <div class="salary mb-2">{{ __('Salary') }}:
                    <strong>{{ $job->salary_currency.''.$job->salary_from }} - {{ $job->salary_currency.''.$job->salary_to }}/{{ $job->getSalaryPeriod('salary_period') }}</strong>
                </div>
            @endif
            <strong><i class="fas fa-map-marker-alt"></i> {{ $job->getCity('city') }}</strong>
            <div class="jobcompany">
                <div class="ftjobcomp">
                    @isset($appliedJob)
                        <span>{{ __('Applied') }}: {{ $appliedJob->created_at->format('M d, Y') }}</span>
                    @else
                        <span>{{ __('Posted') }}: {{ $job->created_at->format('M d, Y') }}</span>
                    @endisset
                    <a href="{{ route('company.detail', $company->slug) }}" title="{{ $company->name }}">{{ $company->name }}</a>
                </div>
                <a href="{{ route('company.detail', $company->slug) }}" class="company-logo" title="{{ $company->name }}">{{ $company->printCompanyImage() }}</a>
            </div>
        </div>
    </li>
@endif
