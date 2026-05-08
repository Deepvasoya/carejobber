@extends('layouts.app')

@section('content')
@include('includes.header')
@include('flash::message')

<main class="medo-pseo-wrap">
    <div class="container">
        <section class="medo-pseo-header">
            <div>
                <p class="medo-eyebrow">{{ __('Healthcare jobs') }}</p>
                <h1>{{ __('Browse healthcare job categories') }}</h1>
                <p class="mb-0">{{ __('Select a category to view available province and city pages.') }}</p>
            </div>
            <div class="medo-stat">
                <span>{{ __('Categories') }}</span>
                <strong>{{ number_format($categories->count()) }}</strong>
            </div>
        </section>

        <section class="medo-pseo-panel">
            <h2>{{ __('Categories') }}</h2>
            <ul class="medo-link-list">
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('medo.jobs.category', $category) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</main>

@include('includes.footer')
@endsection

@include('medo.jobs.partials.styles')
