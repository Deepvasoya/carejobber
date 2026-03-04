@extends('admin.layouts.admin_layout')

@section('content')

<style type="text/css">
    .table td, .table th {
        font-size: 12px;
        line-height: 2.42857 !important;
    }
    .stats-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        border-left: 4px solid;
    }
    .stats-card.primary { border-left-color: #3498db; }
    .stats-card.success { border-left-color: #2ecc71; }
    .stats-card.warning { border-left-color: #f39c12; }
    .stats-card.danger { border-left-color: #e74c3c; }
    .stats-card.info { border-left-color: #9b59b6; }
    .stats-card.purple { border-left-color: #8e44ad; }
    .stats-number {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
    }
    .stats-label {
        font-size: 14px;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stats-icon {
        font-size: 40px;
        opacity: 0.3;
        float: right;
    }
</style>

<div class="page-content-wrapper"> 

    <!-- BEGIN CONTENT BODY -->

    <div class="page-content"> 

        <!-- BEGIN PAGE HEADER--> 

        <!-- BEGIN PAGE BAR -->

        <div class="page-bar">

            <ul class="page-breadcrumb">

                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>

                <li> <span>Jobseeker Payment History</span> </li>

            </ul>

        </div>

        <!-- END PAGE BAR --> 

        <!-- BEGIN PAGE TITLE-->

        <h3 class="page-title">Manage Jobseekers <small>Payment History</small> </h3>

        <!-- END PAGE TITLE--> 

        <!-- END PAGE HEADER-->
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="stats-card primary">
                    <i class="fa fa-money stats-icon"></i>
                    <div class="stats-number">${{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stats-label">Total Revenue</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card success">
                    <i class="fa fa-shopping-cart stats-icon"></i>
                    <div class="stats-number">{{ $stats['total_payments'] }}</div>
                    <div class="stats-label">Total Payments</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card warning">
                    <i class="fa fa-check-circle stats-icon"></i>
                    <div class="stats-number">{{ $stats['active_subscriptions'] }}</div>
                    <div class="stats-label">Active Subscriptions</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card danger">
                    <i class="fa fa-times-circle stats-icon"></i>
                    <div class="stats-number">{{ $stats['expired_subscriptions'] }}</div>
                    <div class="stats-label">Expired Subscriptions</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card info">
                    <i class="fa fa-star stats-icon"></i>
                    <div class="stats-number">{{ $stats['featured_profiles'] }}</div>
                    <div class="stats-label">Featured Profiles</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card purple">
                    <i class="fa fa-bolt stats-icon"></i>
                    <div class="stats-number">{{ $stats['immediate_available'] }}</div>
                    <div class="stats-label">Immediate Available</div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12"> 

                <!-- Begin: life time stats -->

                <div class="portlet light portlet-fit portlet-datatable bordered">

                    <div class="portlet-title">

                        <div class="caption"> <i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">Jobseeker Payment History</span> </div>

                        

                    </div>

                    <div class="portlet-body">

                        <div class="table-container">

                            <form method="post" role="form" id="datatable-search-form">

                                <table class="table table-striped table-bordered table-hover"  id="userDatatableAjax">

                                    <thead>

                                        <tr role="row" class="filter">

                                            <td><input type="text" class="form-control" name="name" id="name" autocomplete="off" placeholder="User Name"></td>

                                            <td><input type="text" class="form-control" name="email" id="email" autocomplete="off" placeholder="Email"></td>

                                            <td>{!! Form::select('package', [''=>'Select Package']+$packages, null, array('class'=>'form-control', 'id'=>'package', 'required'=>'required')) !!}</td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                        </tr>

                                        <tr role="row" class="heading">

                                            <th>User Name</th>

                                            <th>Email</th>

                                            <th>Package</th>

                                            <th>Amount</th>

                                            <th>Payment Method</th>

                                            <th>Quota Used</th>

                                            <th>Featured</th>

                                            <th>Start Date</th>

                                            <th>End Date</th>

                                            <th>Action</th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                    </tbody>

                                </table>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- END CONTENT BODY --> 

</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Complete Payment & User Details</h4>
            </div>
            <div class="modal-body" id="modalContent" style="max-height: 70vh; overflow-y: auto;">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts') 

<script>

    $(function () {

        var oTable = $('#userDatatableAjax').DataTable({

            processing: true,

            serverSide: true,

            stateSave: true,

            searching: false,

            ajax: {

                url: '{!! route('fetch.data.usersHistory') !!}',

                data: function (d) {

                    d.name = $('#name').val();

                    d.email = $('#email').val();

                    d.package = $('#package').val();

                }

            }, columns: [

                {data: 'name', name: 'name'},

                {data: 'email', name: 'email'},

                {data: 'package', name: 'package'},

                {data: 'amount', name: 'amount'},

                {data: 'payment_method', name: 'payment_method'},

                {data: 'quota', name: 'quota', orderable: false},

                {data: 'is_featured', name: 'is_featured'},

                {data: 'package_start_date', name: 'package_start_date'},

                {data: 'package_end_date', name: 'package_end_date'},

                {data: 'action', name: 'action', orderable: false, searchable: false}

            ]

        });

        $('#datatable-search-form').on('submit', function (e) {

            oTable.draw();

            e.preventDefault();

        });

        $('#name').on('keyup', function (e) {

            oTable.draw();

            e.preventDefault();

        });

        $('#email').on('keyup', function (e) {

            oTable.draw();

            e.preventDefault();

        });

        $('#package').on('change', function (e) {

            oTable.draw();

            e.preventDefault();

        });
        
        // View details click handler
        $(document).on('click', '.view-details', function() {
            var userId = $(this).data('id');
            $('#detailsModal').modal('show');
            loadUserDetails(userId);
        });

    });
    
    function loadUserDetails(userId) {
        $.ajax({
            url: '{{ route("get.user.payment.details") }}',
            data: { id: userId },
            success: function(response) {
                var html = '<div class="row">';
                
                // User Information
                html += '<div class="col-md-6">';
                html += '<h4><i class="fa fa-user"></i> User Information</h4>';
                html += '<table class="table table-bordered table-striped">';
                html += '<tr><th width="40%">Name</th><td>' + response.user.name + '</td></tr>';
                html += '<tr><th>Email</th><td>' + response.user.email + '</td></tr>';
                html += '<tr><th>Phone</th><td>' + (response.user.phone || 'N/A') + '</td></tr>';
                html += '<tr><th>Gender</th><td>' + (response.user.gender ? response.user.gender.gender : 'N/A') + '</td></tr>';
                html += '<tr><th>Date of Birth</th><td>' + (response.user.date_of_birth || 'N/A') + '</td></tr>';
                html += '<tr><th>Marital Status</th><td>' + (response.user.marital_status ? response.user.marital_status.marital_status : 'N/A') + '</td></tr>';
                html += '<tr><th>Country</th><td>' + (response.user.country ? response.user.country.country : 'N/A') + '</td></tr>';
                html += '<tr><th>State</th><td>' + (response.user.state ? response.user.state.state : 'N/A') + '</td></tr>';
                html += '<tr><th>City</th><td>' + (response.user.city ? response.user.city.city : 'N/A') + '</td></tr>';
                html += '<tr><th>Location</th><td>' + (response.user.location || 'N/A') + '</td></tr>';
                html += '<tr><th>Profile Views</th><td><span class="badge badge-info">' + (response.user.num_profile_views || 0) + '</span></td></tr>';
                html += '<tr><th>Verified</th><td>' + (response.user.verified ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>') + '</td></tr>';
                html += '<tr><th>Active</th><td>' + (response.user.is_active ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>') + '</td></tr>';
                html += '<tr><th>Immediate Available</th><td>' + (response.user.immediate_available ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>') + '</td></tr>';
                html += '</table>';
                html += '</div>';
                
                // Package Information
                html += '<div class="col-md-6">';
                html += '<h4><i class="fa fa-shopping-cart"></i> Package Information</h4>';
                
                if (response.package) {
                    var isFeaturedPackage = (response.user.package_id == 9);
                    var badgeClass = isFeaturedPackage ? 'label-success' : 'label-primary';
                    var badgeText = isFeaturedPackage ? 'Featured Profile Package' : 'Jobseeker Package';
                    
                    html += '<div class="alert alert-info" style="margin-bottom: 10px;">';
                    html += '<h5><strong><span class="label ' + badgeClass + '">' + badgeText + '</span></strong></h5><br>';
                    html += '<table class="table table-bordered" style="background: white; margin-top: 10px;">';
                    html += '<tr><th width="45%">Package</th><td>' + response.package.package_title + '</td></tr>';
                    html += '<tr><th>Amount Paid</th><td><strong class="text-success">$' + parseFloat(response.package.package_price).toFixed(2) + '</strong></td></tr>';
                    html += '<tr><th>Payment Method</th><td>';
                    if (response.user.payment_method && response.user.payment_method !== '') {
                        html += '<span class="label label-primary">' + response.user.payment_method + '</span>';
                    } else {
                        html += '<span class="label label-warning">Admin Assign</span>';
                    }
                    html += '</td></tr>';
                    html += '<tr><th>Duration</th><td>' + response.package.package_num_days + ' days</td></tr>';
                    
                    // Only show quota for non-featured packages
                    if (!isFeaturedPackage) {
                        html += '<tr><th>Applications Quota</th><td><span class="badge badge-primary">' + (response.user.availed_jobs_quota || 0) + '</span> / <span class="badge badge-success">' + (response.user.jobs_quota || 0) + '</span></td></tr>';
                    }
                    
                    // Use appropriate date fields based on package type
                    var startDate, endDate;
                    if (isFeaturedPackage) {
                        startDate = response.user.featured_package_start_at;
                        endDate = response.user.featured_package_end_at;
                    } else {
                        startDate = response.user.package_start_date;
                        endDate = response.user.package_end_date;
                    }
                    
                    html += '<tr><th>Start Date</th><td>' + (startDate ? new Date(startDate).toLocaleDateString('en-GB') : 'N/A') + '</td></tr>';
                    html += '<tr><th>End Date</th><td>' + (endDate ? new Date(endDate).toLocaleDateString('en-GB') : 'N/A') + '</td></tr>';
                    
                    if (endDate) {
                        var endDateTime = new Date(endDate);
                        var isExpired = endDateTime < new Date();
                        html += '<tr><th>Status</th><td><span class="label label-' + (isExpired ? 'danger' : 'success') + '">' + (isExpired ? 'Expired' : 'Active') + '</span></td></tr>';
                    }
                    
                    html += '<tr><th>Featured Profile</th><td>' + (response.user.is_featured ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>') + '</td></tr>';
                    html += '</table>';
                    html += '</div>';
                } else {
                    html += '<div class="alert alert-warning">No active package</div>';
                }
                
                html += '</div>';
                
                html += '</div>';
                
                $('#modalContent').html(html);
            },
            error: function() {
                $('#modalContent').html('<div class="alert alert-danger">Error loading details</div>');
            }
        });
    }

</script> 

@endpush

