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

                <li> <span>Payment History</span> </li>

            </ul>

        </div>

        <!-- END PAGE BAR --> 

        <!-- BEGIN PAGE TITLE-->

        <h3 class="page-title">Manage Companies <small>Payment History</small> </h3>

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
                    <i class="fa fa-briefcase stats-icon"></i>
                    <div class="stats-number">{{ $stats['total_job_packages'] }}</div>
                    <div class="stats-label">Job Packages Sold</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card purple">
                    <i class="fa fa-file-text stats-icon"></i>
                    <div class="stats-number">{{ $stats['total_cv_packages'] }}</div>
                    <div class="stats-label">CV Packages Sold</div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12"> 

                <!-- Begin: life time stats -->

                <div class="portlet light portlet-fit portlet-datatable bordered">

                    <div class="portlet-title">

                        <div class="caption"> <i class="icon-settings font-dark"></i> <span class="caption-subject font-dark sbold uppercase">Payment History</span> </div>

                        

                    </div>

                    <div class="portlet-body">

                        <div class="table-container">

                            <form method="post" role="form" id="datatable-search-form">

                                <table class="table table-striped table-bordered table-hover"  id="companyDatatableAjax">

                                    <thead>

                                        <tr role="row" class="filter">

                                            <td><input type="text" class="form-control" name="name" id="name" autocomplete="off" placeholder="Company Name"></td>

                                            <td><input type="text" class="form-control" name="email" id="email" autocomplete="off" placeholder="Email"></td>

                                            <td>
                                                <select name="package_type" id="package_type" class="form-control">
                                                    <option value="">All Types</option>
                                                    <option value="job">Job Package</option>
                                                    <option value="cv">CV Package</option>
                                                </select>
                                            </td>

                                            <td>{!! Form::select('package', [''=>'Select Package']+$packages, null, array('class'=>'form-control', 'id'=>'package', 'required'=>'required')) !!}</td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                            <td></td>

                                        </tr>

                                        <tr role="row" class="heading">

                                            <th>Company Name</th>

                                            <th>Email</th>

                                            <th>Package Type</th>

                                            <th>Package Details</th>

                                            <th>Payment Method</th>

                                            <th>Quota Used</th>

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
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> Payment & Company Details</h4>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts') 

<script>

    $(function () {

        var oTable = $('#companyDatatableAjax').DataTable({

            processing: true,

            serverSide: true,

            stateSave: true,

            searching: false,

            ajax: {

                url: '{!! route('fetch.data.companiesHistory') !!}',

                data: function (d) {

                    d.name = $('#name').val();

                    d.email = $('#email').val();

                    d.package_type = $('#package_type').val();

                    d.package = $('#package').val();

                }

            }, columns: [

                {data: 'name', name: 'name'},

                {data: 'email', name: 'email'},

                {data: 'package_type_badge', name: 'package_type_badge', orderable: false},

                {data: 'package', name: 'package'},

                {data: 'payment_method', name: 'payment_method'},

                {data: 'quota', name: 'quota', orderable: false},

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

        $('#package_type').on('change', function (e) {

            oTable.draw();

            e.preventDefault();

        });

        $('#package').on('change', function (e) {

            oTable.draw();

            e.preventDefault();

        });

        // View details click handler
        $(document).on('click', '.view-details', function() {
            var companyId = $(this).data('id');
            var packageType = $(this).data('type');
            $('#detailsModal').modal('show');
            loadCompanyDetails(companyId, packageType);
        });

    });

    function loadCompanyDetails(companyId, packageType) {
        $.ajax({
            url: '{{ route("get.company.payment.details") }}',
            data: { id: companyId },
            success: function(response) {
                var html = '<div class="row">';
                
                // Company Information
                html += '<div class="col-md-6">';
                html += '<h4><i class="fa fa-building"></i> Company Information</h4>';
                html += '<table class="table table-bordered">';
                html += '<tr><th width="40%">Company Name</th><td>' + response.company.name + '</td></tr>';
                html += '<tr><th>Email</th><td>' + response.company.email + '</td></tr>';
                html += '<tr><th>Phone</th><td>' + (response.company.phone || 'N/A') + '</td></tr>';
                html += '<tr><th>CEO</th><td>' + (response.company.ceo || 'N/A') + '</td></tr>';
                html += '<tr><th>Website</th><td>' + (response.company.website || 'N/A') + '</td></tr>';
                html += '<tr><th>Employees</th><td>' + (response.company.no_of_employees || 'N/A') + '</td></tr>';
                html += '<tr><th>Established</th><td>' + (response.company.established_in || 'N/A') + '</td></tr>';
                html += '</table>';
                html += '</div>';
                
                // Package Information
                html += '<div class="col-md-6">';
                html += '<h4><i class="fa fa-shopping-cart"></i> Package Information</h4>';
                
                // Show only the clicked package type
                if (packageType === 'job' && response.job_package) {
                    html += '<div class="alert alert-info">';
                    html += '<h5><strong><span class="label label-primary">Job Package</span></strong></h5><br>';
                    html += '<p><strong>Package:</strong> ' + response.job_package.package_title + '</p>';
                    html += '<p><strong>Price:</strong> $' + response.job_package.package_price + '</p>';
                    html += '<p><strong>Duration:</strong> ' + response.job_package.package_num_days + ' days</p>';
                    html += '<p><strong>Jobs Quota:</strong> ' + response.company.availed_jobs_quota + '/' + response.company.jobs_quota + '</p>';
                    html += '<p><strong>Start Date:</strong> ' + (response.company.package_start_date ? new Date(response.company.package_start_date).toLocaleDateString() : 'N/A') + '</p>';
                    html += '<p><strong>End Date:</strong> ' + (response.company.package_end_date ? new Date(response.company.package_end_date).toLocaleDateString() : 'N/A') + '</p>';
                    var endDate = new Date(response.company.package_end_date);
                    var isExpired = endDate < new Date();
                    html += '<p><strong>Status:</strong> <span class="label label-' + (isExpired ? 'danger' : 'success') + '">' + (isExpired ? 'Expired' : 'Active') + '</span></p>';
                    html += '</div>';
                } else if (packageType === 'cv' && response.cv_package) {
                    html += '<div class="alert alert-success">';
                    html += '<h5><strong><span class="label label-success">CV Search Package</span></strong></h5><br>';
                    html += '<p><strong>Package:</strong> ' + response.cv_package.package_title + '</p>';
                    html += '<p><strong>Price:</strong> $' + response.cv_package.package_price + '</p>';
                    html += '<p><strong>Duration:</strong> ' + response.cv_package.package_num_days + ' days</p>';
                    html += '<p><strong>CVs Quota:</strong> ' + response.company.availed_cvs_quota + '/' + response.company.cvs_quota + '</p>';
                    html += '<p><strong>Start Date:</strong> ' + (response.company.cvs_package_start_date ? new Date(response.company.cvs_package_start_date).toLocaleDateString() : 'N/A') + '</p>';
                    html += '<p><strong>End Date:</strong> ' + (response.company.cvs_package_end_date ? new Date(response.company.cvs_package_end_date).toLocaleDateString() : 'N/A') + '</p>';
                    var endDate = new Date(response.company.cvs_package_end_date);
                    var isExpired = endDate < new Date();
                    html += '<p><strong>Status:</strong> <span class="label label-' + (isExpired ? 'danger' : 'success') + '">' + (isExpired ? 'Expired' : 'Active') + '</span></p>';
                    html += '</div>';
                } else {
                    html += '<div class="alert alert-warning">Package not found</div>';
                }
                
                html += '<hr>';
                html += '<h5><strong>Payment Method:</strong> ' + (response.company.payment_method || 'N/A') + '</h5>';
                
                // Show other packages available
                html += '<hr><h5>Other Active Packages:</h5>';
                var hasOtherPackages = false;
                if (packageType !== 'job' && response.job_package) {
                    html += '<p><span class="label label-primary">Job Package</span>: ' + response.job_package.package_title + '</p>';
                    hasOtherPackages = true;
                }
                if (packageType !== 'cv' && response.cv_package) {
                    html += '<p><span class="label label-success">CV Package</span>: ' + response.cv_package.package_title + '</p>';
                    hasOtherPackages = true;
                }
                if (!hasOtherPackages) {
                    html += '<p class="text-muted">No other active packages</p>';
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
