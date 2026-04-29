@extends('layouts.app')

@section('content')
@include('includes.header')

<div class="listpgWraper pseo-guide-page">
    <div class="container">
        <article class="pseo-guide">
            <p class="pseo-eyebrow">{{ __('Healthcare career guide') }}</p>
            <h1>{{ $guide->title }}</h1>
            @if($guide->excerpt)
                <p class="pseo-guide-excerpt">{{ $guide->excerpt }}</p>
            @endif
            <div class="pseo-guide-body">
                {!! $guide->body !!}
            </div>
        </article>
    </div>
</div>

@include('includes.footer')
@endsection

@push('styles')
<style>
    .pseo-guide {
        max-width: 840px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 28px;
    }
    .pseo-guide h1 {
        font-size: 36px;
        line-height: 1.2;
        margin: 0 0 12px;
    }
    .pseo-eyebrow {
        margin: 0 0 6px;
        color: #0f766e;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 13px;
    }
    .pseo-guide-excerpt {
        font-size: 18px;
        color: #475569;
    }
    .pseo-guide-body {
        color: #1f2937;
        line-height: 1.75;
    }
    .pseo-guide-body h2,
    .pseo-guide-body h3 {
        margin-top: 28px;
    }
    @media (max-width: 767px) {
        .pseo-guide {
            padding: 20px;
        }
        .pseo-guide h1 {
            font-size: 28px;
        }
    }
</style>
@endpush
