@extends('admin.layouts.admin_layout')
@section('content')
<style type="text/css">
    .table td, .table th {
        font-size: 12px;
        line-height: 2.42857 !important;
    }
    .stat-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #333;
    }
    .stat-card .stat-label {
        color: #666;
        font-size: 14px;
    }
    .stat-card.stat-total { border-left: 4px solid #11998e; }
    .stat-card.stat-pending { border-left: 4px solid #ffc107; }
    .stat-card.stat-registered { border-left: 4px solid #17a2b8; }
    .stat-card.stat-rewarded { border-left: 4px solid #28a745; }
    .stat-card.stat-bonus { border-left: 4px solid #f5576c; }
    .badge-warning { background: #ffc107; color: #212529; }
    .badge-info { background: #17a2b8; color: #fff; }
    .badge-success { background: #28a745; color: #fff; }
    .badge-secondary { background: #6c757d; color: #fff; }
    .settings-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .settings-card h4 {
        margin-bottom: 15px;
        color: #333;
    }
</style>
<div class="page-content-wrapper"> 
    <div class="page-content"> 
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li> <a href="{{ route('admin.home') }}">Home</a> <i class="fa fa-circle"></i> </li>
                <li> <span>Job Seeker Referral Program</span> </li>
            </ul>
        </div>
        
        <h3 class="page-title">Job Seeker Referral Program <small>Manage job seeker referrals and settings</small></h3>
        
        @include('flash::message')
        
        <!-- Statistics -->
        <div class="row">
            <div class="col-md-2">
                <div class="stat-card stat-total">
                    <div class="stat-value">{{ $totalReferrals }}</div>
                    <div class="stat-label">Total Referrals</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-pending">
                    <div class="stat-value">{{ $pendingReferrals }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-registered">
                    <div class="stat-value">{{ $registeredReferrals }}</div>
                    <div class="stat-label">Registered</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-rewarded">
                    <div class="stat-value">{{ $rewardedReferrals }}</div>
                    <div class="stat-label">Rewarded</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-bonus">
                    <div class="stat-value">{{ $totalFeaturedDaysGiven }}</div>
                    <div class="stat-label">Featured Days Given</div>
                </div>
            </div>
        </div>
        
        <!-- Settings Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="settings-card">
                    <h4><i class="fa fa-cog"></i> Job Seeker Referral Settings</h4>
                    <form action="{{ route('admin.update.user.referral.settings') }}" method="POST" class="form-inline">
                        @csrf
                        <div class="form-group mb-3" style="margin-right: 20px;">
                            <label for="user_referral_required_count" style="margin-right: 10px;">Referrals Required for Reward:</label>
                            <input type="number" class="form-control" name="user_referral_required_count" id="user_referral_required_count" 
                                   value="{{ $siteSetting->user_referral_required_count ?? 3 }}" min="1" style="width: 80px;">
                        </div>
                        <div class="form-group mb-3" style="margin-right: 20px;">
                            <label for="user_referral_reward_days" style="margin-right: 10px;">Featured Days as Reward:</label>
                            <input type="number" class="form-control" name="user_referral_reward_days" id="user_referral_reward_days" 
                                   value="{{ $siteSetting->user_referral_reward_days ?? 7 }}" min="1" style="width: 80px;">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Reward History Button -->
        <div class="row" style="margin-bottom: 20px;">
            <div class="col-md-12">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#rewardHistoryModal">
                    <i class="fa fa-trophy"></i> View Reward History
                </button>
            </div>
        </div>

        <!-- Referrals Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light portlet-fit portlet-datatable bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gift font-dark"></i>
                            <span class="caption-subject font-dark sbold uppercase">All Job Seeker Referrals</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="userReferralDatatableAjax">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Referrer</th>
                                        <th>Referred</th>
                                        <th>Referral Code</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reward History Modal -->
<div class="modal fade" id="rewardHistoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-trophy"></i> Job Seeker Reward History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered" id="rewardHistoryTable">
                    <thead>
                        <tr>
                            <th>Job Seeker</th>
                            <th>Total Referrals</th>
                            <th>Successful</th>
                            <th>Featured Days Earned</th>
                            <th>Featured Until</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rewardHistory as $reward)
                        <tr>
                            <td>{{ $reward['user_name'] }}</td>
                            <td>{{ $reward['total_referrals'] }}</td>
                            <td>{{ $reward['successful_referrals'] }}</td>
                            <td><span class="badge badge-success">{{ $reward['featured_days_earned'] }} days</span></td>
                            <td>
                                @if($reward['featured_until'])
                                    <span class="badge badge-info">{{ $reward['featured_until'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if(count($rewardHistory) == 0)
                        <tr><td colspan="5" class="text-center">No rewards given yet</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(function() {
    var oTable = $('#userReferralDatatableAjax').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.fetch.user.referrals') }}"
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'referrer', name: 'referrer', orderable: false, searchable: false },
            { data: 'referred', name: 'referred', orderable: false, searchable: false },
            { data: 'referral_code', name: 'referral_code' },
            { data: 'status_badge', name: 'status' },
            { data: 'date', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    // Delete referral
    $(document).on('click', '.delete-referral', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this referral?')) {
            $.ajax({
                url: "{{ route('admin.delete.user.referral') }}",
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        oTable.ajax.reload();
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
