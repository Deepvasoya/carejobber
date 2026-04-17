@extends('layouts.app')

@section('content')

<!-- Header start -->

@include('includes.header')

<!-- Header end -->



@include('flash::message')

@include('includes.inner_top_search')



<!-- Inner Page Title end -->

<div class="listpgWraper">

    <div class="container-fluid">



        <form action="{{ route('job.list') }}" method="get" id="search-job-list">

            <!-- Search Result and sidebar start -->

            <div class="row">

                @include('includes.job_list_side_bar')


                <div class="col-lg-9">

                    <div class="job-search-results-narrow">

                        <!-- Search List (single narrow column, compact rows) -->
                        <h3>{{ $jobs->total() }} Jobs Found</h3>
                        <div class="topstatinfo mb-0">
                            {{ __('Showing Jobs') }} : {{ $jobs->firstItem() }} - {{ $jobs->lastItem() }}
                            {{ __('Total') }} {{ $jobs->total() }}
                        </div>

                        <ul class="featuredlist row job-search-list-single">

                            <!-- job start -->

                            @if (isset($jobs) && count($jobs))
                            <?php $count_1 = 1; ?>
                            @foreach ($jobs as $job)
                            @php
                            $company = $job->getCompany();
                            @endphp

                            <?php if (isset($company)) {
                            ?>

                                <?php if ($count_1 == 7) { ?>

                                    <li class="col-12">
                                        <div class="jobint text-center">{!! $siteSetting->listing_page_horizontal_ad !!}</div>
                                    </li>

                                <?php } else { ?>





                                    @include('includes.job_search_list_card', [
                                    'job' => $job,
                                    'company' => $company,
                                    'columnClass' => 'col-12',
                                    ])






                                <?php } ?>

                                <?php $count_1++; ?>



                            <?php } ?>

                            <!-- job end -->
                            @endforeach
                            @endif











                            <!-- job end -->









                        </ul>

                        <!-- Pagination Start -->

                        <div class="pagiWrap mt-4 job-search-pagi">

                            <div class="showreslt small text-muted mb-2">

                                {{ __('Showing Jobs') }} : {{ $jobs->firstItem() }} - {{ $jobs->lastItem() }}
                                {{ __('Total') }} {{ $jobs->total() }}

                            </div>

                            @if (isset($jobs) && count($jobs))
                            <div class="job-search-pagi-links">

                                {{ $jobs->appends(request()->query())->links() }}

                            </div>
                            @endif

                        </div>

                        <!-- Pagination end -->

                    </div>





                </div>

            </div>

        </form>

    </div>

</div>

@include('includes.job_list_apply_modal')


@if (Request::get('search') != '' ||
Request::get('functional_area_id') != '' ||
Request::get('country_id') != '' ||
Request::get('state_id') != '' ||
Request::get('city_id') != '' ||
Request::get('city_id') != '')
<div class="modal fade" id="show_alert" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="submit_alert">
                @csrf
                <input type="hidden" name="search" value="{{ Request::get('search') }}">
                <input type="hidden" name="country_id"
                    value="@if (isset(Request::get('country_id')[0])) {{ Request::get('country_id')[0] }} @endif">
                <input type="hidden" name="state_id"
                    value="@if (isset(Request::get('state_id')[0])) {{ Request::get('state_id')[0] }} @endif">
                <input type="hidden" name="city_id"
                    value="@if (isset(Request::get('city_id')[0])) {{ Request::get('city_id')[0] }} @endif">
                <input type="hidden" name="functional_area_id"
                    value="@if (isset(Request::get('functional_area_id')[0])) {{ Request::get('functional_area_id')[0] }} @endif">
                <div class="modal-header">
                    <h4 class="modal-title">Job Alert</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h3>Get the latest <strong>"{{ ucfirst(Request::get('search')) }}"</strong> jobs @if (Request::get('location') != '')
                        in <strong>{{ ucfirst(Request::get('location')) }}</strong>
                        @endif sent straight to your inbox</h3>
                    <div class="form-group">
                        <input type="text" class="form-control" name="email" id="email"
                            placeholder="Enter your Email"
                            value="@if (Auth::check()) {{ Auth::user()->email }} @endif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


@include('includes.footer')

@endsection


@include('includes.job_list_search_styles')


@push('scripts')
<script>
    $('.btn-job-alert').on('click', function() {
        @if(Auth::user())
        $('#show_alert').modal('show');
        @else
        swal({
            title: "Save Job Alerts",
            text: "To save Job Alerts you must be Registered and Logged in",
            icon: "error",
            buttons: {
                Login: "Login",
                register: "Register",
                hello: "OK",
            },
        });
        @endif

    })

    $(document).ready(function($) {
        $("#search-job-list").submit(function() {
            $(this).find(":input").filter(function() {
                return !this.value;
            }).attr("disabled", "disabled");
            return true;
        });


        $("#search-job-list").find(":input").prop("disabled", false);

        $(".view_more_ul").each(function() {
            if ($(this).height() > 100) {
                $(this).addClass("hide_vm_ul");
                $(this).next(".view_more").removeClass("hide_vm");
            }
        });

        // "View More" click event
        $(".view_more").on("click", function(e) {
            e.preventDefault();
            $(this).prev(".view_more_ul").removeClass("hide_vm_ul");
            $(this).addClass("hide_vm");
            $(this).next(".view_less").removeClass("hide_vm");
        });

        // "View Less" click event
        $(".view_less").on("click", function(e) {
            e.preventDefault();
            $(this).prev(".view_more").removeClass("hide_vm");
            $(this).prevAll(".view_more_ul").addClass("hide_vm_ul");
            $(this).addClass("hide_vm");
        });

    });

    if ($("#submit_alert").length > 0) {

        $("#submit_alert").validate({
            rules: {
                email: {
                    required: true,
                    maxlength: 5000,
                    email: true
                }
            },

            messages: {
                email: {
                    required: "Email is required",
                }
            },

            submitHandler: function(form) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('subscribe.alert') }}",
                    type: "GET",
                    data: $('#submit_alert').serialize(),
                    success: function(response) {
                        $("#submit_alert").trigger("reset");
                        $('#show_alert').modal('hide');
                        swal({
                            title: "Success",
                            text: response["msg"],
                            icon: "success",
                            button: "OK",
                        });
                    }
                });
            }
        })
    }

    $(document).on('click', '.swal-button--Login', function() {
        window.location.href = "{{ route('login') }}";
    })
    $(document).on('click', '.swal-button--register', function() {
        window.location.href = "{{ route('register') }}";
    })

    @include('includes.job_list_apply_scripts_auth')
</script>

@include('includes.country_state_city_js')
@endpush