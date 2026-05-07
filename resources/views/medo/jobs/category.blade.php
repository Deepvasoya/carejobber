@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Healthcare jobs') }}</p>
                <h1>{{ $category->name }} {{ __('jobs by province') }}</h1>
                <p class="mb-0">{{ __('Browse active province pages for this healthcare category.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Active provinces') }}</span>
                <strong>{{ number_format($provinces->count()) }}</strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Available provinces') }}</h2>
            @if($provinces->count())
                <ul class="medo-link-list">
                    @foreach($provinces as $province)
                        <li>
                            <a href="{{ route('medo.jobs.category.province', [$category, $province]) }}">
                                {{ $province->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="medo-muted-box">{{ __('No active province pages are available for this category yet.') }}</div>
            @endif
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
