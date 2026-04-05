@extends('layouts.app')
@push('styles')
<style>
/* Homepage hero + wider job search (also targets includes.search on this page only via welcome) */
.home-search-hero {
    position: relative;
    padding: 3.5rem 0 3.25rem;
    margin-bottom: 0;
    background: linear-gradient(135deg, #174a5e 0%, #1f6b82 38%, #1a8a7e 100%);
}
.home-search-hero--image {
    background-image: linear-gradient(105deg, rgba(15, 38, 48, 0.88) 0%, rgba(15, 38, 48, 0.72) 45%, rgba(15, 38, 48, 0.65) 100%), var(--home-hero-image);
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    min-height: 420px;
    display: flex;
    align-items: center;
}
.home-search-hero--image > .container {
    width: 100%;
}
.home-search-hero__inner .home-modern-search-form {
    margin-left: auto;
    margin-right: auto;
    text-align: left;
    max-width: 920px;
}
.home-search-hero__inner .searchbar {
    max-width: 920px;
    margin-left: auto;
    margin-right: auto;
    text-align: left;
}
.home-search-hero__inner .searchbar > h3 {
    text-align: center;
}
.home-search-hero .bxsrctxt h1 {
    color: #fff;
    text-shadow: 0 2px 18px rgba(0,0,0,0.35);
    font-weight: 700;
}
.home-search-hero .bxsrctxt p {
    color: rgba(255,255,255,0.92);
    text-shadow: 0 1px 10px rgba(0,0,0,0.3);
}
.home-modern-search-form {
    background: #fff;
    padding: 24px 32px;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}
.home-search-input-wrap {
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
}
.home-search-input-icon {
    background: #fff !important;
    border: none !important;
    padding: 18px 18px !important;
}
.home-search-input {
    border: none !important;
    padding: 18px 14px !important;
    font-size: 17px !important;
    min-height: 54px;
    box-shadow: none !important;
}
.home-search-submit-btn {
    padding: 18px 20px !important;
    font-size: 17px !important;
    font-weight: 600 !important;
    border-radius: 10px !important;
    min-height: 54px;
}
.home-search-hero__inner .home-popular-searches { text-align: center; }
.home-popular-searches { text-align: left; }
.home-popular-label {
    color: rgba(255,255,255,0.9);
    font-size: 15px;
}
.home-popular-link {
    color: rgba(255,255,255,0.95);
    font-size: 15px;
    margin-left: 8px;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.35);
}
.home-popular-link:hover {
    color: #fff;
    border-bottom-color: #fff;
}
@media (max-width: 991px) {
    .home-search-hero { padding: 2.5rem 0 2rem; }
    .home-search-hero--image { min-height: 360px; }
    .home-modern-search-form { padding: 28px 20px; }
}
</style>
@endpush
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end --> 
<!-- Search start -->
@include('includes.search')
<!-- Search End --> 

<div class="infodatawrap">
<div class="container">
<div class="row">
    <div class="col-md-6">@include('includes.login_text')</div>
    <div class="col-md-6">@include('includes.employer_login_text')</div>
</div>

</div>
</div>

<!-- Popular Searches start -->
@include('includes.popular_searches')
<!-- Popular Searches ends --> 

<!-- Top Employers start -->
{{-- @include('includes.top_employers') --}}
<!-- Top Employers ends --> 

<!-- Featured Jobs start -->
@include('includes.featured_jobs')
<!-- Featured Jobs ends -->

<!-- Latest Jobs start -->
{{-- @include('includes.latest_jobs') --}}
<!-- Latest Jobs ends -->

<!-- Testimonials start -->
{{-- @include('includes.home_blogs') --}}
<!-- Testimonials End -->

@include('includes.job_list_apply_modal')

@include('includes.footer')

@livewire('apply-job-modal')

@endsection
@push('scripts') 
<script>
    $(document).ready(function ($) {
        $("form").submit(function () {
            $(this).find(":input").filter(function () {
                return !this.value;
            }).attr("disabled", "disabled");
            return true;
        });
        $("form").find(":input").prop("disabled", false);
    });
</script>
@include('includes.country_state_city_js')
<script>
@include('includes.job_list_apply_scripts_auth')
</script>
@endpush
