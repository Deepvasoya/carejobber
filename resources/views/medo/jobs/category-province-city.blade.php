@extends('layouts.app')

@section('page_title', $category->name . ' Jobs in ' . $city->name . ', ' . strtoupper($province->slug) . ' | Medojob')

@section('content')
@include('includes.header')
@include('flash::message')

{{-- JSON-LD Schemas --}}
@include('medo.partials.breadcrumb-schema', ['items' => $breadcrumbs])
@include('medo.partials.job-posting-schema', ['jobs' => $jobs])
@include('medo.partials.faq-schema', ['faqs' => $faqs])

<main class="medo-pseo-wrap">
    <div class="container">
        @include('medo.partials.breadcrumbs', ['items' => $breadcrumbs])

        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Healthcare jobs in') }} {{ $city->name }}</p>
                <h1>{{ $category->name }} {{ __('jobs in') }} {{ $city->name }}, {{ strtoupper($province->slug) }}</h1>
                <p class="mb-0">{{ $intro }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Active listings') }}</span>
                <strong>{{ number_format($jobCount) }}</strong>
                @if(!empty($salaryRange['min']) || !empty($salaryRange['max']))
                    <span>${{ number_format($salaryRange['min'] ?: $salaryRange['max'], 2) }} - ${{ number_format($salaryRange['max'] ?: $salaryRange['min'], 2) }}</span>
                @endif
            </div>
        </section>

        <div class="row">
            <div class="col-lg-8">
                <section class="medo-pseo-panel">
                    <h2>{{ __('Current openings') }}</h2>
                    <ul class="medo-job-list">
                        @foreach($jobs as $job)
                            @include('medo.jobs.partials.job-card', ['job' => $job, 'category' => $category, 'province' => $province, 'city' => $city])
                        @endforeach
                    </ul>
                </section>
            </div>
            <aside class="col-lg-4">
                @if($topEmployers->count())
                    <section class="medo-pseo-panel">
                        <h3>{{ __('Top employers') }}</h3>
                        <ul>
                            @foreach($topEmployers as $item)
                                <li>{{ optional($item['employer'])->name }} - {{ $item['count'] }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if($relatedCities->count())
                    <section class="medo-pseo-panel">
                        <h3>{{ __('Nearby city pages') }}</h3>
                        <ul>
                            @foreach($relatedCities as $relatedCity)
                                <li>
                                    <a href="{{ route('medo.jobs.category.province.city', [$category, $province, $relatedCity]) }}">
                                        {{ $relatedCity->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if($relatedCategories->count())
                    <section class="medo-pseo-panel">
                        <h3>{{ __('Related categories') }}</h3>
                        <ul>
                            @foreach($relatedCategories as $relatedCategory)
                                <li>
                                    <a href="{{ route('medo.jobs.category.province.city', [$relatedCategory, $province, $city]) }}">
                                        {{ $relatedCategory->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if($faqs && $faqs->count())
                    <section class="medo-pseo-panel">
                        <h3>{{ __('Frequently Asked Questions') }}</h3>
                        @foreach($faqs as $faq)
                            <details class="medo-faq-item">
                                <summary>{{ $faq->question }}</summary>
                                <p>{{ $faq->answer }}</p>
                            </details>
                        @endforeach
                    </section>
                @endif
            </aside>
        </div>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
