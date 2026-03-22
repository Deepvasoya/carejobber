@extends('layouts.app')

@section('content')
@include('includes.header')

<div id="recruiter-posting-packs-subs" class="container" style="max-width: 900px; margin: 2rem auto; padding: 0 15px;">
    @include('flash::message')

    <section class="hero text-center mb-4">
        <div class="mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="50" viewBox="0 0 248 144" fill="none" class="d-inline-block" style="max-width: 120px;"><path d="M31 0h186a9 9 0 019 9v124a9 9 0 01-9 9H31a9 9 0 01-9-9V9a9 9 0 019-9z" fill="#74737E"/><path fill="#FFF" d="M31 11h186v118H31z"/><rect fill="#428EE6" x="50" y="50" width="80" height="8" rx="2"/><rect fill="#D4D6DE" x="50" y="70" width="120" height="6" rx="2"/></svg>
        </div>
        <h1 class="heading h2 mb-2">{{ __('Packages and Subscriptions') }}</h1>
        <p class="text-muted">{{ __('Simple pricing. No surprise fees. Advanced features.') }}</p>
    </section>

    <ul class="nav nav-tabs nav-fill mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'packages' ? 'active' : '' }}" href="{{ route('recruiter.posting.packages', ['cc' => $country_code]) }}">{{ __('Packages') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'subscriptions' ? 'active' : '' }}" href="{{ route('recruiter.posting.subscriptions', ['cc' => $country_code]) }}">{{ __('Subscriptions') }}</a>
        </li>
    </ul>

    <ul class="list-unstyled mb-4">
        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Easy and instant posting process – your jobs available online in no time') }}</li>
        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Job posting credits that you purchase will never expire. Buy now and post whenever you need.') }}</li>
    </ul>

    <section class="package-list">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <h2 class="h4 mb-3">{{ __('Select your package') }}</h2>

        <div class="row align-items-center mb-4">
            <div class="col-md-6 mb-2">
                <span class="me-2">{{ $countries[$country_code] ?? $country_code }}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#tip-package-country" title="{{ __('Help') }}">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
            <div class="col-md-6">
                <label for="country" class="visually-hidden">{{ __('Country') }}</label>
                <select id="country" class="form-select form-select-sm">
                    <option value="">{{ __('Change country') }}</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" data-url="{{ $tab === 'subscriptions' ? route('recruiter.posting.subscriptions', ['cc' => $code]) : route('recruiter.posting.packages', ['cc' => $code]) }}" {{ $code === $country_code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="modal fade" id="tip-package-country" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Country selection') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('You need to specify a country when purchasing a job posting package because:') }}</p>
                        <ul>
                            <li>{{ __('Your job posting credits can only be used in the selected country.') }}</li>
                            <li>{{ __("Should you wish to post jobs in another country, please purchase another job posting package according to your requirements.") }}</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <ul class="list-unstyled packages-list">
            @forelse($packages as $index => $pkg)
            <li class="border rounded p-3 mb-3 package-card">
                <div class="row align-items-center">
                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                        <input type="radio" name="package" value="{{ $pkg->id }}" id="package-{{ $index }}" class="form-check-input me-2" {{ $index === 0 ? 'checked' : '' }}>
                        <label for="package-{{ $index }}" class="form-check-label d-inline">
                            <strong>{{ $pkg->package_num_listings }}</strong> {{ $tab === 'subscriptions' ? __('months unlimited job postings') : __('job postings') }}
                        </label>
                    </div>
                    <div class="col-12 col-md-4 mb-2 mb-md-0">
                        @if($pkg->rebate_percent)
                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> {{ $pkg->rebate_percent }}% {{ __('rebate') }}</span>
                        @elseif($tab === 'subscriptions')
                        <span class="badge bg-primary">{{ __('Unlimited job postings') }}</span>
                        @endif
                    </div>
                    <div class="col-12 col-md-3 mb-2 mb-md-0">
                        <div class="fw-bold">{{ $siteSetting->default_currency_code ?? 'CAD' }} {{ number_format($pkg->package_price, 2) }}</div>
                    </div>
                    <div class="col-12 mt-2">
                        <a href="{{ route('recruiter.checkout.package', ['packageId' => $pkg->id, 'cc' => $country_code, 'tab' => $tab]) }}" class="btn btn-primary">
                            {{ __('Buy now') }}
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                        <ul class="list-inline d-inline ms-3 mt-2 mb-0">
                            <li class="list-inline-item"><i class="fab fa-cc-visa text-muted" title="Visa"></i></li>
                            <li class="list-inline-item"><i class="fab fa-cc-mastercard text-muted" title="Mastercard"></i></li>
                            <li class="list-inline-item"><i class="fab fa-cc-amex text-muted" title="Amex"></i></li>
                            <li class="list-inline-item"><i class="fab fa-cc-stripe text-muted" title="Stripe"></i></li>
                        </ul>
                    </div>
                </div>
            </li>
            @empty
            <li class="text-muted py-4">{{ __('No packages available for this country.') }}</li>
            @endforelse
        </ul>
    </section>

    <section class="help border-top pt-4 mt-4">
        <h2 class="h5">{{ __('Want to post more?') }}</h2>
        <p>{{ __('Please') }} <a href="{{ url('/contact-us') }}">{{ __('contact us') }}</a> {{ __("and we'll find a personalised solution for you.") }}</p>
    </section>
</div>

@include('includes.footer')
@endsection

@push('scripts')
<script>
document.getElementById('country').addEventListener('change', function() {
    var url = this.options[this.selectedIndex].getAttribute('data-url');
    if (url) window.location.href = url;
});
</script>
@endpush
