@php
    $cardCategory = $category ?? $job->category;
    $cardProvince = $province ?? $job->province;
    $cardCity = $city ?? $job->city;
    $employmentTypes = [
        'full_time' => __('Full-time'),
        'part_time' => __('Part-time'),
        'casual' => __('Casual'),
    ];
    $shiftTypes = [
        'days' => __('Days'),
        'evenings' => __('Evenings'),
        'nights' => __('Nights'),
        'rotating' => __('Rotating'),
        'weekends' => __('Weekends'),
    ];
@endphp

<li>
    <article class="medo-job-row">
        <div>
            <h3>
                <a href="{{ route('jobs.detail', [$cardCategory, $cardProvince, $cardCity, $job]) }}">
                    {{ $job->title }}
                </a>
            </h3>
            <p class="mb-0">
                {{ optional($job->employer)->name ?? __('Employer not listed') }}
                @if($job->facility_name)
                    - {{ $job->facility_name }}
                @endif
                - {{ $cardCity->name }}, {{ $cardProvince->slug ? strtoupper($cardProvince->slug) : $cardProvince->name }}
            </p>
            <div class="medo-job-meta">
                @if($job->employment_type)
                    <span class="medo-pill">{{ $employmentTypes[$job->employment_type] ?? ucfirst(str_replace('_', ' ', $job->employment_type)) }}</span>
                @endif
                @if($job->shift_type)
                    <span class="medo-pill">{{ $shiftTypes[$job->shift_type] ?? ucfirst($job->shift_type) }}</span>
                @endif
                @if($job->wage_min || $job->wage_max)
                    <span class="medo-pill">
                        ${{ number_format($job->wage_min ?: $job->wage_max, 2) }}
                        @if($job->wage_max && $job->wage_max !== $job->wage_min)
                            - ${{ number_format($job->wage_max, 2) }}
                        @endif
                        {{ $job->wage_period === 'annual' ? __('annual') : __('hourly') }}
                    </span>
                @endif
                @if($job->posted_at)
                    <span class="medo-pill">{{ __('Posted') }} {{ $job->posted_at->format('M j, Y') }}</span>
                @endif
            </div>
        </div>
        <div>
            <a class="medo-button" href="{{ route('jobs.detail', [$cardCategory, $cardProvince, $cardCity, $job]) }}">
                {{ __('View job') }}
            </a>
        </div>
    </article>
</li>
