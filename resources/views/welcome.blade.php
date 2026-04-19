@extends('layouts.app')
@section('content')
<!-- Header start -->
@include('includes.header')
<!-- Header end -->
<!-- Search start -->
@include('includes.search')
<!-- Search End -->

<div class="infodatawrap" style="padding-top: 20px;padding-bottom:20px">
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
    $(document).ready(function($) {
        $("form").submit(function() {
            $(this).find(":input").filter(function() {
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